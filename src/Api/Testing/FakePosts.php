<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use DateTimeImmutable;
use DateTimeZone;
use Merkushin\Wpal\Api\Exception\PostNotFound;
use Merkushin\Wpal\Api\Posts;
use Merkushin\Wpal\Api\Posts\Order;
use Merkushin\Wpal\Api\Posts\Post;
use Merkushin\Wpal\Api\Posts\PostQuery;
use Merkushin\Wpal\Api\Posts\SortBy;

/**
 * In-memory posts for tests, with the same query semantics as WordPress's for the supported criteria.
 */
final class FakePosts implements Posts {
	/** @var array<int, Post> By ID. */
	public private(set) array $posts = [];

	/** @var array<int, array<string, mixed>> Meta stored per post ID. */
	public private(set) array $meta = [];

	private int $nextId = 1;

	public function __construct( Post ...$posts ) {
		foreach ( $posts as $post ) {
			$this->posts[ $post->id ] = $post;
			$this->nextId             = max( $this->nextId, $post->id + 1 );
		}
	}

	public function find( int $id ): ?Post {
		return $this->posts[ $id ] ?? null;
	}

	public function get( int $id ): Post {
		return $this->find( $id ) ?? throw new PostNotFound( $id );
	}

	public function query(): PostQuery {
		return new PostQuery( $this );
	}

	public function matching( PostQuery $query ): array {
		$search  = $query->search !== null ? mb_strtolower( $query->search ) : null;
		$matches = array_filter(
			$this->posts,
			static fn ( Post $post ): bool => in_array( $post->type, $query->types, true )
				&& ( in_array( $post->status, $query->statuses, true ) || in_array( 'any', $query->statuses, true ) )
				&& ( $query->authorId === null || $post->authorId === $query->authorId )
				&& ( $query->parentId === null || ( $post->parentId ?? 0 ) === $query->parentId )
				&& ( $query->ids === [] || in_array( $post->id, $query->ids, true ) )
				&& ( $search === null || str_contains( mb_strtolower( $post->title . ' ' . $post->content ), $search ) )
		);

		if ( $query->sortBy === SortBy::Random ) {
			shuffle( $matches );
		} else {
			usort(
				$matches,
				static function ( Post $a, Post $b ) use ( $query ): int {
					$result = self::sortKey( $a, $query->sortBy ) <=> self::sortKey( $b, $query->sortBy );

					return $query->order === Order::Asc ? $result : -$result;
				}
			);
		}

		return array_slice( $matches, $query->offset, $query->limit );
	}

	public function create(
		string $title = '',
		string $content = '',
		string $type = 'post',
		string $status = 'draft',
		string $excerpt = '',
		?string $slug = null,
		?int $authorId = null,
		?int $parentId = null,
		int $menuOrder = 0,
		array $meta = [],
	): Post {
		$id  = $this->nextId++;
		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );

		$this->posts[ $id ] = new Post(
			id: $id,
			type: $type,
			status: $status,
			title: $title,
			content: $content,
			excerpt: $excerpt,
			slug: $slug ?? $this->slugify( $title, $id ),
			authorId: $authorId ?? 0,
			parentId: $parentId,
			menuOrder: $menuOrder,
			date: $status === 'draft' ? null : $now,
			modified: $now,
		);
		$this->meta[ $id ] = $meta;

		return $this->posts[ $id ];
	}

	public function update(
		int $id,
		?string $title = null,
		?string $content = null,
		?string $status = null,
		?string $excerpt = null,
		?string $slug = null,
		?int $authorId = null,
		?int $parentId = null,
		?int $menuOrder = null,
		?array $meta = null,
	): Post {
		$post = $this->get( $id );

		$this->posts[ $id ] = new Post(
			id: $id,
			type: $post->type,
			status: $status ?? $post->status,
			title: $title ?? $post->title,
			content: $content ?? $post->content,
			excerpt: $excerpt ?? $post->excerpt,
			slug: $slug ?? $post->slug,
			authorId: $authorId ?? $post->authorId,
			parentId: $parentId ?? $post->parentId,
			menuOrder: $menuOrder ?? $post->menuOrder,
			date: $post->date ?? ( ( $status ?? $post->status ) === 'draft' ? null : new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ) ),
			modified: new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) ),
			mimeType: $post->mimeType,
		);
		if ( $meta !== null ) {
			$this->meta[ $id ] = $meta + ( $this->meta[ $id ] ?? [] );
		}

		return $this->posts[ $id ];
	}

	public function trash( int $id ): void {
		$this->update( $id, status: 'trash' );
	}

	public function delete( int $id ): void {
		$this->get( $id );
		unset( $this->posts[ $id ], $this->meta[ $id ] );
	}

	private static function sortKey( Post $post, SortBy $sortBy ): string|int {
		return match ( $sortBy ) {
			SortBy::Date      => $post->date?->format( 'U.u' ) ?? '',
			SortBy::Modified  => $post->modified?->format( 'U.u' ) ?? '',
			SortBy::Title     => mb_strtolower( $post->title ),
			SortBy::Id        => $post->id,
			SortBy::MenuOrder => $post->menuOrder,
			SortBy::Slug      => $post->slug,
			SortBy::Random    => 0,
		};
	}

	private function slugify( string $title, int $id ): string {
		$slug = trim( (string) preg_replace( '/[^a-z0-9]+/', '-', strtolower( $title ) ), '-' );

		return $slug !== '' ? $slug : (string) $id;
	}
}

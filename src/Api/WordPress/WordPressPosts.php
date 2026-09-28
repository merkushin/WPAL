<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use Merkushin\Wpal\Api\Exception\PostNotFound;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Api\Posts;
use Merkushin\Wpal\Api\Posts\Post;
use Merkushin\Wpal\Api\Posts\PostQuery;
use Merkushin\Wpal\Service\Posts as PostsService;

final class WordPressPosts implements Posts {
	public function __construct( private readonly PostsService $posts ) {
	}

	public function find( int $id ): ?Post {
		if ( $id <= 0 ) {
			return null;
		}
		$post = $this->posts->get_post( $id );

		return is_object( $post ) ? Post::fromWordPress( $post ) : null;
	}

	public function get( int $id ): Post {
		return $this->find( $id ) ?? throw new PostNotFound( $id );
	}

	public function query(): PostQuery {
		return new PostQuery( $this );
	}

	public function matching( PostQuery $query ): array {
		$args = [
			'post_type'      => $query->types,
			'post_status'    => $query->statuses,
			'orderby'        => $query->sortBy->value,
			'order'          => $query->order->value,
			'posts_per_page' => $query->limit ?? -1,
			'offset'         => $query->offset,
		];
		if ( $query->authorId !== null ) {
			$args['author'] = $query->authorId;
		}
		if ( $query->parentId !== null ) {
			$args['post_parent'] = $query->parentId;
		}
		if ( $query->ids !== [] ) {
			$args['post__in'] = $query->ids;
		}
		if ( $query->search !== null ) {
			$args['s'] = $query->search;
		}

		return array_values( array_map( Post::fromWordPress( ... ), $this->posts->get_posts( $args ) ) );
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
		$fields = $this->fields( $title, $content, $status, $excerpt, $slug, $authorId, $parentId, $menuOrder, $meta ) + [ 'post_type' => $type ];

		return $this->get( $this->saved( $this->posts->wp_insert_post( $fields, true ), 'create the post' ) );
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
		$this->get( $id );
		$fields = [ 'ID' => $id ] + $this->fields( $title, $content, $status, $excerpt, $slug, $authorId, $parentId, $menuOrder, $meta );

		return $this->get( $this->saved( $this->posts->wp_update_post( $fields, true ), "update post {$id}" ) );
	}

	public function trash( int $id ): void {
		$this->get( $id );
		if ( ! $this->posts->wp_trash_post( $id ) ) {
			throw new WordPressError( "WordPress could not trash post {$id}." );
		}
	}

	public function delete( int $id ): void {
		$this->get( $id );
		if ( ! $this->posts->wp_delete_post( $id, true ) ) {
			throw new WordPressError( "WordPress could not delete post {$id}." );
		}
	}

	/**
	 * The wp_insert_post() fields for the arguments that were given.
	 *
	 * @param array<string, mixed>|null $meta
	 * @return array<string, mixed>
	 */
	private function fields( ?string $title, ?string $content, ?string $status, ?string $excerpt, ?string $slug, ?int $authorId, ?int $parentId, ?int $menuOrder, ?array $meta ): array {
		$fields = [
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => $status,
			'post_excerpt' => $excerpt,
			'post_name'    => $slug,
			'post_author'  => $authorId,
			'post_parent'  => $parentId,
			'menu_order'   => $menuOrder,
			'meta_input'   => $meta === [] ? null : $meta,
		];

		return array_filter( $fields, static fn ( mixed $value ): bool => $value !== null );
	}

	/**
	 * The post ID WordPress returned, or an exception for its error.
	 */
	private function saved( mixed $result, string $action ): int {
		if ( WordPressError::isWpError( $result ) ) {
			throw WordPressError::fromWpError( $result );
		}
		if ( ! is_int( $result ) || $result <= 0 ) {
			throw new WordPressError( "WordPress could not {$action}." );
		}

		return $result;
	}
}

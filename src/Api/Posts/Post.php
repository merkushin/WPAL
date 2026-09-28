<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Posts;

use DateTimeImmutable;
use DateTimeZone;

/**
 * A post of any type, as stored. Dates are UTC; `date` is null for drafts that were never scheduled.
 */
final readonly class Post {
	public function __construct(
		public int $id,
		public string $type = 'post',
		public string $status = 'draft',
		public string $title = '',
		public string $content = '',
		public string $excerpt = '',
		public string $slug = '',
		public int $authorId = 0,
		public ?int $parentId = null,
		public int $menuOrder = 0,
		public ?DateTimeImmutable $date = null,
		public ?DateTimeImmutable $modified = null,
		public string $mimeType = '',
	) {
	}

	/**
	 * @param object $post A `WP_Post`.
	 */
	public static function fromWordPress( object $post ): self {
		$field = static fn ( string $name ): string => isset( $post->$name ) && is_scalar( $post->$name ) ? (string) $post->$name : '';
		$parent = (int) $field( 'post_parent' );

		return new self(
			id: (int) $field( 'ID' ),
			type: $field( 'post_type' ),
			status: $field( 'post_status' ),
			title: $field( 'post_title' ),
			content: $field( 'post_content' ),
			excerpt: $field( 'post_excerpt' ),
			slug: $field( 'post_name' ),
			authorId: (int) $field( 'post_author' ),
			parentId: $parent > 0 ? $parent : null,
			menuOrder: (int) $field( 'menu_order' ),
			date: self::utc( $field( 'post_date_gmt' ) ),
			modified: self::utc( $field( 'post_modified_gmt' ) ),
			mimeType: $field( 'post_mime_type' ),
		);
	}

	public function isPublished(): bool {
		return $this->status === 'publish';
	}

	private static function utc( string $value ): ?DateTimeImmutable {
		if ( $value === '' || str_starts_with( $value, '0000-00-00' ) ) {
			return null;
		}
		$date = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $value, new DateTimeZone( 'UTC' ) );

		return $date === false ? null : $date;
	}
}

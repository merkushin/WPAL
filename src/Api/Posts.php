<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Merkushin\Wpal\Api\Exception\PostNotFound;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Api\Posts\Post;
use Merkushin\Wpal\Api\Posts\PostQuery;

/**
 * Posts of every type: posts, pages, attachments and custom post types.
 */
interface Posts {
	public function find( int $id ): ?Post;

	/**
	 * @throws PostNotFound
	 */
	public function get( int $id ): Post;

	/**
	 * Starts a query; by default the 10 newest published posts.
	 */
	#[\NoDiscard]
	public function query(): PostQuery;

	/**
	 * @return list<Post>
	 */
	public function matching( PostQuery $query ): array;

	/**
	 * @param array<string, mixed> $meta Post meta to store with it.
	 * @throws WordPressError
	 */
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
	): Post;

	/**
	 * Changes only the fields you pass.
	 *
	 * @param array<string, mixed>|null $meta Post meta to add or change.
	 * @throws PostNotFound
	 * @throws WordPressError
	 */
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
	): Post;

	/**
	 * Moves the post to the trash (or deletes it when trash is disabled, as WordPress does).
	 *
	 * @throws PostNotFound
	 * @throws WordPressError
	 */
	public function trash( int $id ): void;

	/**
	 * Deletes the post permanently, skipping the trash.
	 *
	 * @throws PostNotFound
	 * @throws WordPressError
	 */
	public function delete( int $id ): void;
}

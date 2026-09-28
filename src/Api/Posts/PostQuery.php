<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Posts;

use Merkushin\Wpal\Api\Posts;

/**
 * Which posts to fetch. Immutable: every method returns a new query.
 *
 *     $wp->posts()->query()->type( 'page' )->status( 'publish', 'draft' )->sortBy( SortBy::Title, Order::Asc )->get();
 */
final class PostQuery {
	/** @var string[] */
	public private(set) array $types = [ 'post' ];

	/** @var string[] */
	public private(set) array $statuses = [ 'publish' ];

	public private(set) ?int $authorId = null;

	public private(set) ?int $parentId = null;

	/** @var int[] */
	public private(set) array $ids = [];

	public private(set) ?string $search = null;

	public private(set) SortBy $sortBy = SortBy::Date;

	public private(set) Order $order = Order::Desc;

	/** Null for no limit. */
	public private(set) ?int $limit = 10;

	public private(set) int $offset = 0;

	public function __construct( private readonly Posts $posts ) {
	}

	public function type( string ...$types ): self {
		$query        = clone $this;
		$query->types = $types;

		return $query;
	}

	public function status( string ...$statuses ): self {
		$query           = clone $this;
		$query->statuses = $statuses;

		return $query;
	}

	public function author( int $authorId ): self {
		$query           = clone $this;
		$query->authorId = $authorId;

		return $query;
	}

	/**
	 * @param int $parentId 0 for top-level posts.
	 */
	public function parent( int $parentId ): self {
		$query           = clone $this;
		$query->parentId = $parentId;

		return $query;
	}

	public function ids( int ...$ids ): self {
		$query      = clone $this;
		$query->ids = $ids;

		return $query;
	}

	public function search( string $terms ): self {
		$query         = clone $this;
		$query->search = $terms;

		return $query;
	}

	public function sortBy( SortBy $sortBy, Order $order = Order::Desc ): self {
		$query         = clone $this;
		$query->sortBy = $sortBy;
		$query->order  = $order;

		return $query;
	}

	/**
	 * @param int|null $limit Null for every matching post.
	 */
	public function limit( ?int $limit ): self {
		$query        = clone $this;
		$query->limit = $limit;

		return $query;
	}

	public function offset( int $offset ): self {
		$query         = clone $this;
		$query->offset = $offset;

		return $query;
	}

	/**
	 * @return list<Post>
	 */
	public function get(): array {
		return $this->posts->matching( $this );
	}

	public function first(): ?Post {
		return $this->limit( 1 )->get()[0] ?? null;
	}
}

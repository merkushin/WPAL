<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Exception;

use RuntimeException;

final class PostNotFound extends RuntimeException implements WpalException {
	public function __construct( public readonly int $postId ) {
		parent::__construct( "Post {$postId} does not exist." );
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Posts;

enum Order: string {
	case Asc  = 'ASC';
	case Desc = 'DESC';
}

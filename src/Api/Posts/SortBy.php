<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Posts;

enum SortBy: string {
	case Date      = 'date';
	case Modified  = 'modified';
	case Title     = 'title';
	case Id        = 'ID';
	case MenuOrder = 'menu_order';
	case Slug      = 'name';
	case Random    = 'rand';
}

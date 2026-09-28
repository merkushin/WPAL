<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Assets;

enum LoadingStrategy: string {
	case Defer = 'defer';
	case Async = 'async';
}

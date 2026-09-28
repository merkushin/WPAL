<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Closure;
use ReflectionFunction;

/**
 * How many hook arguments a callback accepts.
 *
 * @internal
 */
final class CallbackArity {
	public static function of( callable $callback ): int {
		$reflection = new ReflectionFunction( Closure::fromCallable( $callback ) );

		return $reflection->isVariadic() ? PHP_INT_MAX : $reflection->getNumberOfParameters();
	}
}

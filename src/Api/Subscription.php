<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Closure;

/**
 * A callback attached to a hook. Keep it to detach the callback later.
 */
final class Subscription {
	private bool $active = true;

	/**
	 * @param Closure(): void $remove Detaches the callback.
	 */
	public function __construct(
		public readonly string $hook,
		public readonly int $priority,
		private readonly Closure $remove,
	) {
	}

	/**
	 * Detaches the callback. Safe to call more than once.
	 */
	public function remove(): void {
		if ( $this->active ) {
			( $this->remove )();
			$this->active = false;
		}
	}

	public function isActive(): bool {
		return $this->active;
	}
}

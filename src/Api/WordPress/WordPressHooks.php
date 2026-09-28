<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use Merkushin\Wpal\Api\CallbackArity;
use Merkushin\Wpal\Api\Hooks;
use Merkushin\Wpal\Api\Subscription;
use Merkushin\Wpal\Service\Hooks as HooksService;

final class WordPressHooks implements Hooks {
	public function __construct( private readonly HooksService $hooks ) {
	}

	public function onAction( string $hook, callable $callback, int $priority = 10 ): Subscription {
		$this->hooks->add_action( $hook, $callback, $priority, CallbackArity::of( $callback ) );

		return new Subscription(
			$hook,
			$priority,
			function () use ( $hook, $callback, $priority ): void {
				$this->hooks->remove_action( $hook, $callback, $priority );
			}
		);
	}

	public function onFilter( string $hook, callable $callback, int $priority = 10 ): Subscription {
		$this->hooks->add_filter( $hook, $callback, $priority, CallbackArity::of( $callback ) );

		return new Subscription(
			$hook,
			$priority,
			function () use ( $hook, $callback, $priority ): void {
				$this->hooks->remove_filter( $hook, $callback, $priority );
			}
		);
	}

	public function doAction( string $hook, mixed ...$args ): void {
		$this->hooks->do_action( $hook, ...$args );
	}

	public function applyFilters( string $hook, mixed $value, mixed ...$args ): mixed {
		return $this->hooks->apply_filters( $hook, $value, ...$args );
	}

	public function didAction( string $hook ): int {
		return $this->hooks->did_action( $hook );
	}

	public function hasAction( string $hook ): bool {
		return (bool) $this->hooks->has_action( $hook );
	}

	public function hasFilter( string $hook ): bool {
		return (bool) $this->hooks->has_filter( $hook );
	}
}

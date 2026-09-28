<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

/**
 * Actions and filters.
 *
 * Callbacks receive as many hook arguments as they declare parameters, so there is no `$accepted_args`.
 */
interface Hooks {
	/**
	 * Runs `$callback` whenever `$hook` fires. Lower priorities run first.
	 */
	public function onAction( string $hook, callable $callback, int $priority = 10 ): Subscription;

	/**
	 * Passes `$hook`'s value through `$callback`, which must return the (possibly changed) value.
	 */
	public function onFilter( string $hook, callable $callback, int $priority = 10 ): Subscription;

	public function doAction( string $hook, mixed ...$args ): void;

	public function applyFilters( string $hook, mixed $value, mixed ...$args ): mixed;

	/**
	 * How many times `$hook` has fired during this request.
	 */
	public function didAction( string $hook ): int;

	/**
	 * Whether any callback is attached to action `$hook`.
	 */
	public function hasAction( string $hook ): bool;

	/**
	 * Whether any callback is attached to filter `$hook`.
	 */
	public function hasFilter( string $hook ): bool;
}

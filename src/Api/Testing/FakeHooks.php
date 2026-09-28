<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use Merkushin\Wpal\Api\CallbackArity;
use Merkushin\Wpal\Api\Hooks;
use Merkushin\Wpal\Api\Subscription;

/**
 * In-memory hooks for tests: callbacks really run, in priority order, without WordPress.
 */
final class FakeHooks implements Hooks {
	/** @var array<string, array<int, array{priority: int, callback: callable, arity: int}>> */
	private array $actions = [];

	/** @var array<string, array<int, array{priority: int, callback: callable, arity: int}>> */
	private array $filters = [];

	/** @var array<string, int> */
	private array $fired = [];

	private int $sequence = 0;

	public function onAction( string $hook, callable $callback, int $priority = 10 ): Subscription {
		$id                            = $this->sequence++;
		$this->actions[ $hook ][ $id ] = [ 'priority' => $priority, 'callback' => $callback, 'arity' => CallbackArity::of( $callback ) ];

		return new Subscription(
			$hook,
			$priority,
			function () use ( $hook, $id ): void {
				unset( $this->actions[ $hook ][ $id ] );
			}
		);
	}

	public function onFilter( string $hook, callable $callback, int $priority = 10 ): Subscription {
		$id                            = $this->sequence++;
		$this->filters[ $hook ][ $id ] = [ 'priority' => $priority, 'callback' => $callback, 'arity' => CallbackArity::of( $callback ) ];

		return new Subscription(
			$hook,
			$priority,
			function () use ( $hook, $id ): void {
				unset( $this->filters[ $hook ][ $id ] );
			}
		);
	}

	public function doAction( string $hook, mixed ...$args ): void {
		$this->fired[ $hook ] = ( $this->fired[ $hook ] ?? 0 ) + 1;
		foreach ( $this->ordered( $this->actions[ $hook ] ?? [] ) as $entry ) {
			( $entry['callback'] )( ...array_slice( $args, 0, $entry['arity'] ) );
		}
	}

	public function applyFilters( string $hook, mixed $value, mixed ...$args ): mixed {
		foreach ( $this->ordered( $this->filters[ $hook ] ?? [] ) as $entry ) {
			$value = ( $entry['callback'] )( ...array_slice( [ $value, ...$args ], 0, $entry['arity'] ) );
		}

		return $value;
	}

	public function didAction( string $hook ): int {
		return $this->fired[ $hook ] ?? 0;
	}

	public function hasAction( string $hook ): bool {
		return ( $this->actions[ $hook ] ?? [] ) !== [];
	}

	public function hasFilter( string $hook ): bool {
		return ( $this->filters[ $hook ] ?? [] ) !== [];
	}

	/**
	 * @param array<int, array{priority: int, callback: callable, arity: int}> $entries
	 * @return array<int, array{priority: int, callback: callable, arity: int}> By priority, then registration order.
	 */
	private function ordered( array $entries ): array {
		uksort( $entries, static fn ( int $a, int $b ): int => [ $entries[ $a ]['priority'], $a ] <=> [ $entries[ $b ]['priority'], $b ] );

		return $entries;
	}
}

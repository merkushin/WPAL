<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use Merkushin\Wpal\Api\Options;
use Merkushin\Wpal\Api\Options\TypedGetters;

/**
 * In-memory options for tests.
 */
final class FakeOptions implements Options {
	use TypedGetters;

	/** @var array<string, bool|null> Autoload flag per option, as last set. */
	public private(set) array $autoload = [];

	/**
	 * @param array<string, mixed> $values Options that exist from the start.
	 */
	public function __construct( public private(set) array $values = [] ) {
	}

	public function get( string $name, mixed $default = null ): mixed {
		return array_key_exists( $name, $this->values ) ? $this->values[ $name ] : $default;
	}

	public function has( string $name ): bool {
		return array_key_exists( $name, $this->values );
	}

	public function set( string $name, mixed $value, ?bool $autoload = null ): void {
		$this->values[ $name ]   = $value;
		$this->autoload[ $name ] = $autoload;
	}

	public function add( string $name, mixed $value, ?bool $autoload = null ): bool {
		if ( $this->has( $name ) ) {
			return false;
		}
		$this->set( $name, $value, $autoload );

		return true;
	}

	public function delete( string $name ): bool {
		$existed = $this->has( $name );
		unset( $this->values[ $name ], $this->autoload[ $name ] );

		return $existed;
	}
}

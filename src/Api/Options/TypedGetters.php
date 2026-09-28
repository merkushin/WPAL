<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Options;

/**
 * The typed getters of {@see \Merkushin\Wpal\Api\Options}, built on get().
 *
 * @internal
 */
trait TypedGetters {
	abstract public function get( string $name, mixed $default = null ): mixed;

	public function string( string $name, string $default = '' ): string {
		$value = $this->get( $name );

		return is_scalar( $value ) ? (string) $value : $default;
	}

	public function int( string $name, int $default = 0 ): int {
		$value = $this->get( $name );

		return is_int( $value ) || ( is_string( $value ) && is_numeric( $value ) ) || is_float( $value ) ? (int) $value : $default;
	}

	public function bool( string $name, bool $default = false ): bool {
		$value = $this->get( $name );
		if ( is_bool( $value ) ) {
			return $value;
		}

		return is_scalar( $value ) ? ( filter_var( $value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE ) ?? $default ) : $default;
	}

	/**
	 * @param array<mixed> $default
	 * @return array<mixed>
	 */
	public function array( string $name, array $default = [] ): array {
		$value = $this->get( $name );

		return is_array( $value ) ? $value : $default;
	}
}

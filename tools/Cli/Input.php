<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Cli;

use RuntimeException;

/**
 * Parsed command line: positional arguments plus `--name=value` and `--flag` options.
 */
final class Input {
	/**
	 * @param string[]                   $arguments
	 * @param array<string, string|true> $options
	 */
	public function __construct(
		public readonly array $arguments = [],
		public readonly array $options = [],
	) {
	}

	/**
	 * @param string[] $argv Without the script name.
	 */
	public static function parse( array $argv ): self {
		$arguments = [];
		$options   = [];
		foreach ( $argv as $arg ) {
			if ( str_starts_with( $arg, '--' ) ) {
				$parts                = explode( '=', substr( $arg, 2 ), 2 );
				$options[ $parts[0] ] = $parts[1] ?? true;
			} else {
				$arguments[] = $arg;
			}
		}

		return new self( $arguments, $options );
	}

	public function argument( int $index ): ?string {
		return $this->arguments[ $index ] ?? null;
	}

	public function option( string $name ): ?string {
		$value = $this->options[ $name ] ?? null;

		return is_string( $value ) ? $value : null;
	}

	public function flag( string $name ): bool {
		return isset( $this->options[ $name ] );
	}

	public function json(): bool {
		$format = $this->option( 'format' ) ?? 'text';
		if ( ! in_array( $format, [ 'text', 'json' ], true ) ) {
			throw new RuntimeException( "Unknown format \"{$format}\"; use text or json." );
		}

		return $format === 'json';
	}

	/**
	 * @param string[] $allowed
	 */
	public function assertOptions( array $allowed ): void {
		$unknown = array_diff( array_keys( $this->options ), $allowed );
		if ( $unknown !== [] ) {
			throw new RuntimeException( 'Unknown option --' . implode( ', --', $unknown ) . '.' );
		}
	}
}

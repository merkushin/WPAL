<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Coverage;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use RuntimeException;

/**
 * `wpal.map.php`: which WordPress functions WPAL deliberately leaves out, and why.
 */
final class Map {
	/**
	 * @param array<string, string> $ignore      Function name or `/regex/` => reason.
	 * @param array<string, string> $ignoreFiles Source file, or directory ending in `/`, => reason.
	 */
	public function __construct(
		private array $ignore = [],
		private array $ignoreFiles = [],
	) {
	}

	public static function load( string $file ): self {
		if ( ! is_file( $file ) ) {
			return new self();
		}

		$data = require $file;
		if ( ! is_array( $data ) ) {
			throw new RuntimeException( "{$file} must return an array." );
		}

		return new self( $data['ignore'] ?? [], $data['ignore_files'] ?? [] );
	}

	/**
	 * Why a function isn't meant to be wrapped, or null if it should be.
	 */
	public function ignoreReason( FunctionSignature $function ): ?string {
		if ( $function->isDeprecated() ) {
			return 'deprecated';
		}
		if ( $function->private ) {
			return 'private';
		}

		foreach ( $this->ignore as $pattern => $reason ) {
			$matches = str_starts_with( $pattern, '/' )
				? preg_match( $pattern, $function->name ) === 1
				: strcasecmp( $pattern, $function->name ) === 0;
			if ( $matches ) {
				return $reason;
			}
		}

		foreach ( $this->ignoreFiles as $path => $reason ) {
			$file    = (string) $function->file;
			$matches = str_ends_with( $path, '/' ) ? str_starts_with( $file, $path ) : $file === $path;
			if ( $matches ) {
				return $reason;
			}
		}

		return null;
	}
}

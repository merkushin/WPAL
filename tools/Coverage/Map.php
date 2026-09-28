<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Coverage;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use RuntimeException;

/**
 * `wpal.map.php`: which service each WordPress function belongs to, and which WPAL deliberately leaves out.
 */
final class Map {
	/**
	 * @param array<string, string[]> $services    Service => function names or `/regex/` patterns.
	 * @param array<string, string>   $ignore      Function name or `/regex/` => reason.
	 * @param array<string, string>   $ignoreFiles Source file, or directory ending in `/`, => reason.
	 */
	public function __construct(
		private array $services = [],
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

		return new self( $data['services'] ?? [], $data['ignore'] ?? [], $data['ignore_files'] ?? [] );
	}

	/**
	 * Services the map assigns a function to. More than one is a mistake in the map.
	 *
	 * @return string[]
	 */
	public function servicesFor( string $function ): array {
		$found = [];
		foreach ( $this->services as $service => $patterns ) {
			foreach ( $patterns as $pattern ) {
				if ( $this->matches( $pattern, $function ) ) {
					$found[] = $service;
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * Function names listed literally (not via regex), with their service.
	 *
	 * @return array<string, string>
	 */
	public function listedFunctions(): array {
		$listed = [];
		foreach ( $this->services as $service => $patterns ) {
			foreach ( $patterns as $pattern ) {
				if ( ! str_starts_with( $pattern, '/' ) ) {
					$listed[ $pattern ] = $service;
				}
			}
		}

		return $listed;
	}

	/**
	 * Why a function isn't meant to be wrapped, or null if it should be.
	 */
	public function ignoreReason( FunctionSignature $function ): ?string {
		$automatic = $this->automaticIgnoreReason( $function );
		if ( $automatic !== null ) {
			return $automatic;
		}

		foreach ( $this->ignore as $pattern => $reason ) {
			if ( $this->matches( $pattern, $function->name ) ) {
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

	/**
	 * Reasons that apply regardless of the map: the function is deprecated or private.
	 */
	public function automaticIgnoreReason( FunctionSignature $function ): ?string {
		if ( $function->isDeprecated() ) {
			return 'deprecated';
		}
		if ( $function->private ) {
			return 'private';
		}

		return null;
	}

	private function matches( string $pattern, string $function ): bool {
		return str_starts_with( $pattern, '/' )
			? preg_match( $pattern, $function ) === 1
			: strcasecmp( $pattern, $function ) === 0;
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Coverage;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use RuntimeException;

/**
 * `wpal.map.php`: which service each WordPress function belongs to, and which WPAL deliberately leaves out.
 */
final class Map {
	/**
	 * @param array<string, string[]>               $services    Generated service => function names, in method order.
	 * @param array<string, string[]>               $planned     Service => function names or `/regex/` patterns.
	 * @param array<string, array<string, string>>  $types       Function => [ parameter or 'return' => native type ].
	 * @param string[]                              $extendable  Services whose `Wp*` class stays non-final.
	 * @param array<string, string>                 $ignore      Function name or `/regex/` => reason.
	 * @param array<string, string>                 $ignoreFiles Source file, or directory ending in `/`, => reason.
	 */
	public function __construct(
		private array $services = [],
		private array $planned = [],
		private array $types = [],
		private array $extendable = [],
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

		return new self(
			$data['services'] ?? [],
			$data['planned'] ?? [],
			$data['types'] ?? [],
			$data['extendable'] ?? [],
			$data['ignore'] ?? [],
			$data['ignore_files'] ?? [],
		);
	}

	/**
	 * @return array<string, string[]> Generated service => function names, in method order.
	 */
	public function services(): array {
		return $this->services;
	}

	/**
	 * Generated services that list a function. More than one is a mistake in the map.
	 *
	 * @return string[]
	 */
	public function servicesFor( string $function ): array {
		return $this->find( $this->services, $function );
	}

	/**
	 * Planned services that list a function. More than one is a mistake in the map.
	 *
	 * @return string[]
	 */
	public function plannedFor( string $function ): array {
		return $this->find( $this->planned, $function );
	}

	/**
	 * Function names listed literally (not via regex) in `services` and `planned`, with their service.
	 *
	 * @return array<int, array{function: string, service: string}>
	 */
	public function listedFunctions(): array {
		$listed = [];
		foreach ( [ $this->services, $this->planned ] as $groups ) {
			foreach ( $groups as $service => $patterns ) {
				foreach ( $patterns as $pattern ) {
					if ( ! str_starts_with( $pattern, '/' ) ) {
						$listed[] = [ 'function' => $pattern, 'service' => $service ];
					}
				}
			}
		}

		return $listed;
	}

	/**
	 * @return array<string, string> Parameter name or 'return' => native type.
	 */
	public function types( string $function ): array {
		return $this->types[ $function ] ?? [];
	}

	public function isExtendable( string $service ): bool {
		return in_array( $service, $this->extendable, true );
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

	/**
	 * @param array<string, string[]> $groups
	 * @return string[]
	 */
	private function find( array $groups, string $function ): array {
		$found = [];
		foreach ( $groups as $service => $patterns ) {
			foreach ( $patterns as $pattern ) {
				if ( $this->matches( $pattern, $function ) ) {
					$found[] = $service;
					break;
				}
			}
		}

		return $found;
	}

	private function matches( string $pattern, string $function ): bool {
		return str_starts_with( $pattern, '/' )
			? preg_match( $pattern, $function ) === 1
			: strcasecmp( $pattern, $function ) === 0;
	}
}

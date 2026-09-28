<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Coverage;

use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\Snapshot;

/**
 * Sorts every WordPress function into wrapped, planned, ignored (with a reason) or untriaged,
 * and reports mistakes in the map.
 */
final class Coverage {
	/**
	 * @param ServiceMethod[] $methods
	 * @return array{
	 *     total: int,
	 *     wrapped: array<string, string[]>,
	 *     planned: array<string, string[]>,
	 *     ignored: array<string, string[]>,
	 *     untriaged: array<string, string[]>,
	 *     problems: string[]
	 * } Wrapped and planned by service, ignored by reason, untriaged by source file; names sorted.
	 */
	public function compute( Snapshot $wordpress, array $methods, Map $map ): array {
		$wrappedIn = [];
		foreach ( $methods as $method ) {
			$wrappedIn[ strtolower( $method->signature->name ) ] = $method->service;
		}

		$wrapped   = [];
		$planned   = [];
		$ignored   = [];
		$untriaged = [];
		$problems  = [];

		foreach ( $wordpress->functions as $key => $function ) {
			$name     = $function->name;
			$services = $map->servicesFor( $name );
			if ( count( $services ) > 1 ) {
				$problems[] = "{$name}() is listed under " . implode( ' and ', $services ) . '.';
			}

			if ( isset( $wrappedIn[ $key ] ) ) {
				$service = $wrappedIn[ $key ];
				if ( $services !== [] && ! in_array( $service, $services, true ) ) {
					$problems[] = "{$name}() is listed under {$services[0]} but wrapped in {$service}.";
				}
				$wrapped[ $service ][] = $name;
				continue;
			}

			if ( $services !== [] ) {
				$automatic = $map->automaticIgnoreReason( $function );
				if ( $automatic !== null ) {
					$problems[]             = "{$name}() is listed under {$services[0]} but is {$automatic}.";
					$ignored[ $automatic ][] = $name;
					continue;
				}
				$planned[ $services[0] ][] = $name;
				continue;
			}

			$reason = $map->ignoreReason( $function );
			if ( $reason !== null ) {
				$ignored[ $reason ][] = $name;
				continue;
			}

			$untriaged[ (string) $function->file ][] = $name;
		}

		foreach ( $map->listedFunctions() as $name => $service ) {
			if ( $wordpress->get( $name ) === null ) {
				$problems[] = "{$name}() is listed under {$service} but WordPress {$wordpress->version} has no such function.";
			}
		}
		sort( $problems, SORT_STRING );

		return [
			'total'     => count( $wordpress->functions ),
			'wrapped'   => $this->sorted( $wrapped ),
			'planned'   => $this->sorted( $planned ),
			'ignored'   => $this->sorted( $ignored ),
			'untriaged' => $this->sorted( $untriaged ),
			'problems'  => $problems,
		];
	}

	/**
	 * @param array<string, string[]> $groups
	 * @return array<string, string[]>
	 */
	private function sorted( array $groups ): array {
		ksort( $groups, SORT_STRING );

		return array_map(
			static function ( array $names ): array {
				sort( $names, SORT_STRING );

				return $names;
			},
			$groups
		);
	}
}

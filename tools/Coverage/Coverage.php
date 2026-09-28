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
			$plans    = $map->plannedFor( $name );
			$listed   = array_merge( $services, $plans );
			if ( count( $listed ) > 1 ) {
				$problems[] = "{$name}() is listed under " . implode( ' and ', $listed ) . '.';
			}

			if ( isset( $wrappedIn[ $key ] ) ) {
				$service = $wrappedIn[ $key ];
				if ( $services === [] ) {
					$problems[] = "{$name}() is wrapped in {$service} but missing from services in wpal.map.php.";
				} elseif ( ! in_array( $service, $services, true ) ) {
					$problems[] = "{$name}() is listed under {$services[0]} but wrapped in {$service}.";
				}
				$wrapped[ $service ][] = $name;
				continue;
			}

			if ( $services !== [] ) {
				$problems[] = "{$name}() is listed under {$services[0]} but not generated; run bin/wpal fix.";
			}

			if ( $listed !== [] ) {
				$automatic = $map->automaticIgnoreReason( $function );
				if ( $automatic !== null ) {
					$problems[]              = "{$name}() is listed under {$listed[0]} but is {$automatic}.";
					$ignored[ $automatic ][] = $name;
					continue;
				}
				$planned[ $listed[0] ][] = $name;
				continue;
			}

			$reason = $map->ignoreReason( $function );
			if ( $reason !== null ) {
				$ignored[ $reason ][] = $name;
				continue;
			}

			$untriaged[ (string) $function->file ][] = $name;
		}

		foreach ( $methods as $method ) {
			if ( $wordpress->get( $method->signature->name ) === null ) {
				$problems[] = "{$method->label()} wraps a function WordPress {$wordpress->version} doesn't have.";
			}
		}
		foreach ( $map->listedFunctions() as $item ) {
			if ( $wordpress->get( $item['function'] ) === null ) {
				$problems[] = "{$item['function']}() is listed under {$item['service']} but WordPress {$wordpress->version} has no such function.";
			}
		}
		$problems = array_values( array_unique( $problems ) );
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

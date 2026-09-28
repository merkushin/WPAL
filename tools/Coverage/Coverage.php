<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Coverage;

use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\Snapshot;

/**
 * Sorts every WordPress function into wrapped, ignored (with a reason) or untriaged.
 */
final class Coverage {
	/**
	 * @param ServiceMethod[] $methods
	 * @return array{
	 *     total: int,
	 *     wrapped: array<string, string[]>,
	 *     ignored: array<string, string[]>,
	 *     untriaged: array<string, string[]>
	 * } Wrapped by service, ignored by reason, untriaged by source file; function names sorted.
	 */
	public function compute( Snapshot $wordpress, array $methods, Map $map ): array {
		$services = [];
		foreach ( $methods as $method ) {
			$services[ strtolower( $method->signature->name ) ] = $method->service;
		}

		$wrapped   = [];
		$ignored   = [];
		$untriaged = [];

		foreach ( $wordpress->functions as $key => $function ) {
			if ( isset( $services[ $key ] ) ) {
				$wrapped[ $services[ $key ] ][] = $function->name;
				continue;
			}

			$reason = $map->ignoreReason( $function );
			if ( $reason !== null ) {
				$ignored[ $reason ][] = $function->name;
				continue;
			}

			$untriaged[ (string) $function->file ][] = $function->name;
		}

		return [
			'total'     => count( $wordpress->functions ),
			'wrapped'   => $this->sorted( $wrapped ),
			'ignored'   => $this->sorted( $ignored ),
			'untriaged' => $this->sorted( $untriaged ),
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

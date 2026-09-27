<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Compare;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\Snapshot;

/**
 * What changed in WordPress's function API between two versions.
 */
final class SnapshotDiff {
	public function __construct( private SignatureComparer $comparer = new SignatureComparer() ) {
	}

	/**
	 * @param ServiceMethod[] $wrapped   Used to flag changes that affect `Service`.
	 * @param bool            $internals Include private and deprecated functions in "added".
	 * @return array{
	 *     added: array<int, array{function: FunctionSignature, service: ?string}>,
	 *     removed: array<int, array{function: FunctionSignature, service: ?string}>,
	 *     deprecated: array<int, array{function: FunctionSignature, service: ?string}>,
	 *     changed: array<int, array{function: FunctionSignature, service: ?string, differences: Difference[]}>
	 * }
	 */
	public function diff( Snapshot $from, Snapshot $to, array $wrapped = [], bool $internals = false ): array {
		$services = [];
		foreach ( $wrapped as $method ) {
			$services[ strtolower( $method->signature->name ) ] = $method->service;
		}

		$result = [ 'added' => [], 'removed' => [], 'deprecated' => [], 'changed' => [] ];

		foreach ( $to->functions as $key => $function ) {
			$old     = $from->functions[ $key ] ?? null;
			$service = $services[ $key ] ?? null;

			if ( $old === null ) {
				if ( $internals || ( ! $function->private && ! $function->isDeprecated() ) ) {
					$result['added'][] = [ 'function' => $function, 'service' => $service ];
				}
				continue;
			}

			if ( $function->isDeprecated() && ! $old->isDeprecated() ) {
				$result['deprecated'][] = [ 'function' => $function, 'service' => $service ];
			}

			$differences = $this->comparer->compare( $old, $function );
			if ( $differences !== [] ) {
				$result['changed'][] = [ 'function' => $function, 'service' => $service, 'differences' => $differences ];
			}
		}

		foreach ( $from->functions as $key => $function ) {
			if ( ! isset( $to->functions[ $key ] ) ) {
				$result['removed'][] = [ 'function' => $function, 'service' => $services[ $key ] ?? null ];
			}
		}

		return $result;
	}
}

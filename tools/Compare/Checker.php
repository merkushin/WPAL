<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Compare;

use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\Snapshot;

/**
 * Finds where `Service` methods no longer mirror the WordPress functions they wrap.
 */
final class Checker {
	public function __construct( private SignatureComparer $comparer = new SignatureComparer() ) {
	}

	/**
	 * @param ServiceMethod[] $methods
	 * @return array<int, array{method: ServiceMethod, differences: Difference[]}> Only methods that drift.
	 */
	public function check( Snapshot $wordpress, array $methods ): array {
		$results = [];
		foreach ( $methods as $method ) {
			$differences = $this->differences( $wordpress, $method );
			if ( $differences !== [] ) {
				$results[] = [ 'method' => $method, 'differences' => $differences ];
			}
		}

		return $results;
	}

	/**
	 * @return Difference[]
	 */
	private function differences( Snapshot $wordpress, ServiceMethod $method ): array {
		$function = $wordpress->get( $method->signature->name );
		if ( $function === null ) {
			return [ new Difference( Difference::REMOVED, "WordPress {$wordpress->version} has no function {$method->signature->name}()." ) ];
		}

		$differences = $this->comparer->compare( $method->signature, $function );

		if ( $function->isDeprecated() && ! $method->signature->isDeprecated() ) {
			$since         = $function->deprecated !== '' ? " since {$function->deprecated}" : '';
			$differences[] = new Difference( Difference::DEPRECATED, "Deprecated in WordPress{$since}; mark the method @deprecated." );
		}

		return $differences;
	}
}

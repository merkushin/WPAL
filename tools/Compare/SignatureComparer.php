<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Compare;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\Parameter;

/**
 * Compares parameter lists. Native types are ignored: `Service` may add types WordPress doesn't declare.
 */
final class SignatureComparer {
	/**
	 * @return Difference[] How `$right` differs from `$left`, position by position.
	 */
	public function compare( FunctionSignature $left, FunctionSignature $right ): array {
		$differences = [];
		$shared      = min( count( $left->parameters ), count( $right->parameters ) );

		for ( $i = 0; $i < $shared; $i++ ) {
			$l = $left->parameters[ $i ];
			$r = $right->parameters[ $i ];

			if ( $l->name !== $r->name ) {
				$differences[] = new Difference( Difference::PARAMETER_RENAMED, "Parameter \${$l->name} renamed to \${$r->name}.", $r->name );
			}
			if ( $l->byRef !== $r->byRef || $l->variadic !== $r->variadic ) {
				$differences[] = new Difference( Difference::PARAMETER_KIND, "Parameter {$l->describe()} became {$r->describe()}.", $r->name );
			}

			$default = $this->compareDefaults( $l, $r );
			if ( $default !== null ) {
				$differences[] = $default;
			}
		}

		foreach ( array_slice( $right->parameters, $shared ) as $r ) {
			$differences[] = new Difference( Difference::PARAMETER_ADDED, "Parameter {$r->describe()} added.", $r->name );
		}
		foreach ( array_slice( $left->parameters, $shared ) as $l ) {
			$differences[] = new Difference( Difference::PARAMETER_REMOVED, "Parameter {$l->describe()} removed.", $l->name );
		}

		return $differences;
	}

	private function compareDefaults( Parameter $l, Parameter $r ): ?Difference {
		if ( ! $l->hasDefault() && ! $r->hasDefault() ) {
			return null;
		}
		if ( ! $l->hasDefault() ) {
			return new Difference( Difference::DEFAULT_ADDED, "Parameter \${$r->name} gained default {$r->default}.", $r->name );
		}
		if ( ! $r->hasDefault() ) {
			return new Difference( Difference::DEFAULT_REMOVED, "Parameter \${$l->name} lost default {$l->default}.", $r->name );
		}
		if ( $this->sameDefault( $l, $r ) ) {
			return null;
		}

		return new Difference( Difference::DEFAULT_CHANGED, "Default of \${$r->name} changed from {$l->default} to {$r->default}.", $r->name );
	}

	private function sameDefault( Parameter $l, Parameter $r ): bool {
		if ( $l->defaultResolved && $r->defaultResolved ) {
			return $l->defaultValue === $r->defaultValue;
		}

		return $this->normalize( (string) $l->default ) === $this->normalize( (string) $r->default );
	}

	private function normalize( string $code ): string {
		$code = strtolower( preg_replace( '/\s+/', '', $code ) ?? $code );
		$code = str_replace( 'array()', '[]', $code );

		return ltrim( $code, '\\' );
	}
}

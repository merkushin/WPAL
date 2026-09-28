<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;

/**
 * Turns php-parser nodes into {@see Parameter}s, resolving default values against WordPress constants.
 */
final class SignatureBuilder {
	private Standard $printer;

	private ConstExprEvaluator $evaluator;

	/**
	 * @param array<string, mixed> $constants Constant values used to resolve defaults such as `OBJECT`.
	 */
	public function __construct( private array $constants = [] ) {
		$this->printer   = new Standard();
		$this->evaluator = new ConstExprEvaluator( function ( Expr $expr ) {
			if ( $expr instanceof Expr\ConstFetch ) {
				$name = ltrim( $expr->name->toString(), '\\' );
				if ( array_key_exists( $name, $this->constants ) ) {
					return $this->constants[ $name ];
				}
			}

			throw new ConstExprEvaluationException( 'Unresolvable expression' );
		} );
	}

	/**
	 * @param Node\Param[] $params
	 * @return Parameter[]
	 */
	public function parameters( array $params ): array {
		return array_map( fn ( Node\Param $param ): Parameter => $this->parameter( $param ), $params );
	}

	public function parameter( Node\Param $param ): Parameter {
		$name = $param->var instanceof Expr\Variable && is_string( $param->var->name ) ? $param->var->name : '';

		$default  = null;
		$resolved = false;
		$value    = null;
		if ( $param->default !== null ) {
			$default = $this->printer->prettyPrintExpr( $param->default );
			try {
				$value    = $this->evaluator->evaluateDirectly( $param->default );
				$resolved = true;
			} catch ( ConstExprEvaluationException ) {
				$resolved = false;
			}
		}

		return new Parameter(
			$name,
			$this->type( $param->type ),
			$default,
			$resolved,
			$value,
			$param->byRef,
			$param->variadic,
		);
	}

	public function type( Node\Identifier|Node\Name|Node\ComplexType|null $type ): ?string {
		if ( $type === null ) {
			return null;
		}
		if ( $type instanceof Node\NullableType ) {
			return '?' . $this->type( $type->type );
		}
		if ( $type instanceof Node\UnionType ) {
			return implode( '|', array_map( fn ( $t ): string => (string) $this->type( $t ), $type->types ) );
		}
		if ( $type instanceof Node\IntersectionType ) {
			return implode( '&', array_map( fn ( $t ): string => (string) $this->type( $t ), $type->types ) );
		}
		if ( $type instanceof Node\Identifier || $type instanceof Node\Name ) {
			return $type->toString();
		}

		return null;
	}

	/**
	 * Evaluates a constant expression, e.g. the value passed to `define()`.
	 *
	 * @return array{0: bool, 1: mixed} Whether it resolved, and the value.
	 */
	public function evaluate( Expr $expr ): array {
		try {
			return [ true, $this->evaluator->evaluateDirectly( $expr ) ];
		} catch ( ConstExprEvaluationException ) {
			return [ false, null ];
		}
	}
}

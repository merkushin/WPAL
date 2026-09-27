<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\Parameter;
use Merkushin\Wpal\Tools\Compare\Difference;
use Merkushin\Wpal\Tools\Compare\SignatureComparer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Tools\Compare\SignatureComparer
 * @covers \Merkushin\Wpal\Tools\Compare\Difference
 */
class SignatureComparerTest extends TestCase
{
	public function testCompare_WhenIdentical_ReturnsNothing(): void
	{
		$signature = new FunctionSignature( 'f', [ new Parameter( 'a' ), new Parameter( 'b', null, "'x'", true, 'x' ) ] );

		self::assertSame( [], ( new SignatureComparer() )->compare( $signature, $signature ) );
	}

	public function testCompare_WhenOnlyNativeTypesDiffer_ReturnsNothing(): void
	{
		$left  = new FunctionSignature( 'f', [ new Parameter( 'hook_name', 'string' ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'hook_name' ) ] );

		self::assertSame( [], ( new SignatureComparer() )->compare( $left, $right ) );
	}

	public function testCompare_WhenParameterAddedOrRemoved_ReportsIt(): void
	{
		$short = new FunctionSignature( 'f', [ new Parameter( 'a' ) ] );
		$long  = new FunctionSignature( 'f', [ new Parameter( 'a' ), new Parameter( 'priority', null, 'false', true, false ) ] );

		self::assertSame( [ Difference::PARAMETER_ADDED ], $this->kinds( $short, $long ) );
		self::assertSame( [ Difference::PARAMETER_REMOVED ], $this->kinds( $long, $short ) );
	}

	public function testCompare_WhenParameterRenamed_ReportsIt(): void
	{
		$left  = new FunctionSignature( 'f', [ new Parameter( 'function', null, "''", true, '' ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'callback', null, "''", true, '' ) ] );

		$differences = ( new SignatureComparer() )->compare( $left, $right );

		self::assertSame( [ Difference::PARAMETER_RENAMED ], array_map( static fn ( Difference $d ): string => $d->kind, $differences ) );
		self::assertSame( 'callback', $differences[0]->parameter );
	}

	public function testCompare_WhenByRefOrVariadicChanges_ReportsKind(): void
	{
		$left  = new FunctionSignature( 'f', [ new Parameter( 'a' ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'a', null, null, false, null, true ) ] );

		self::assertSame( [ Difference::PARAMETER_KIND ], $this->kinds( $left, $right ) );
	}

	public function testCompare_WhenDefaultsHaveSameValue_ReturnsNothing(): void
	{
		// 'OBJECT' in a Service vs WordPress's OBJECT constant, whose value is 'OBJECT'.
		$left  = new FunctionSignature( 'f', [ new Parameter( 'output', null, "'OBJECT'", true, 'OBJECT' ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'output', null, 'OBJECT', true, 'OBJECT' ) ] );

		self::assertSame( [], $this->kinds( $left, $right ) );
	}

	public function testCompare_WhenUnresolvedDefaultsDifferOnlyInSpelling_ReturnsNothing(): void
	{
		$left  = new FunctionSignature( 'f', [ new Parameter( 'a', null, '[]' ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'a', null, 'array()' ) ] );

		self::assertSame( [], $this->kinds( $left, $right ) );
	}

	public function testCompare_WhenDefaultValueChanges_ReportsIt(): void
	{
		$left  = new FunctionSignature( 'f', [ new Parameter( 'path', null, 'null', true, null ) ] );
		$right = new FunctionSignature( 'f', [ new Parameter( 'path', null, "''", true, '' ) ] );

		self::assertSame( [ Difference::DEFAULT_CHANGED ], $this->kinds( $left, $right ) );
	}

	public function testCompare_WhenDefaultAddedOrRemoved_ReportsIt(): void
	{
		$required = new FunctionSignature( 'f', [ new Parameter( 'post' ) ] );
		$optional = new FunctionSignature( 'f', [ new Parameter( 'post', null, 'null', true, null ) ] );

		self::assertSame( [ Difference::DEFAULT_ADDED ], $this->kinds( $required, $optional ) );
		self::assertSame( [ Difference::DEFAULT_REMOVED ], $this->kinds( $optional, $required ) );
	}

	/**
	 * @return string[]
	 */
	private function kinds( FunctionSignature $left, FunctionSignature $right ): array
	{
		return array_map( static fn ( Difference $d ): string => $d->kind, ( new SignatureComparer() )->compare( $left, $right ) );
	}
}

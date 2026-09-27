<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\Parameter;
use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\Snapshot;
use Merkushin\Wpal\Tools\Compare\SnapshotDiff;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Tools\Compare\SnapshotDiff
 */
class SnapshotDiffTest extends TestCase
{
	public function testDiff_WhenVersionsDiffer_GroupsChanges(): void
	{
		$from = new Snapshot( '1.0', [
			'kept'      => new FunctionSignature( 'kept' ),
			'dropped'   => new FunctionSignature( 'dropped' ),
			'aging'     => new FunctionSignature( 'aging' ),
			'has_thing' => new FunctionSignature( 'has_thing', [ new Parameter( 'name' ) ] ),
		] );
		$to   = new Snapshot( '2.0', [
			'kept'      => new FunctionSignature( 'kept' ),
			'aging'     => new FunctionSignature( 'aging', [], null, null, null, '2.0.0' ),
			'has_thing' => new FunctionSignature( 'has_thing', [ new Parameter( 'name' ), new Parameter( 'priority', null, 'false', true, false ) ] ),
			'brand_new' => new FunctionSignature( 'brand_new' ),
			'_internal' => new FunctionSignature( '_internal', [], null, null, null, null, true ),
		] );
		$wrapped = [ new ServiceMethod( 'Things', new FunctionSignature( 'has_thing' ), 'Things.php' ) ];

		$diff = ( new SnapshotDiff() )->diff( $from, $to, $wrapped );

		self::assertSame( [ 'brand_new' ], $this->names( $diff['added'] ) );
		self::assertSame( [ 'dropped' ], $this->names( $diff['removed'] ) );
		self::assertSame( [ 'aging' ], $this->names( $diff['deprecated'] ) );
		self::assertSame( [ 'has_thing' ], $this->names( $diff['changed'] ) );
		self::assertSame( 'Things', $diff['changed'][0]['service'] );
	}

	public function testDiff_WhenInternalsRequested_ListsPrivateAdditions(): void
	{
		$from = new Snapshot( '1.0', [] );
		$to   = new Snapshot( '2.0', [ '_internal' => new FunctionSignature( '_internal', [], null, null, null, null, true ) ] );

		self::assertSame( [], ( new SnapshotDiff() )->diff( $from, $to )['added'] );
		self::assertSame( [ '_internal' ], $this->names( ( new SnapshotDiff() )->diff( $from, $to, [], true )['added'] ) );
	}

	/**
	 * @param array<int, array{function: FunctionSignature}> $items
	 * @return string[]
	 */
	private function names( array $items ): array
	{
		return array_map( static fn ( array $item ): string => $item['function']->name, $items );
	}
}

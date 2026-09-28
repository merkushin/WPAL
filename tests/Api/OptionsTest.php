<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use Merkushin\Wpal\Api\Testing\FakeOptions;
use Merkushin\Wpal\Api\WordPress\WordPressOptions;
use Merkushin\Wpal\Service\Options as OptionsService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressOptions
 * @covers \Merkushin\Wpal\Api\Testing\FakeOptions
 * @covers \Merkushin\Wpal\Api\Options\TypedGetters
 */
class OptionsTest extends TestCase
{
	public function testGet_WhenOptionMissing_ReturnsNullInsteadOfFalse(): void
	{
		$service = $this->createMock( OptionsService::class );
		$service->method( 'get_option' )->with( 'missing' )->willReturn( false );

		self::assertNull( ( new WordPressOptions( $service ) )->get( 'missing' ) );
	}

	public function testGet_WhenDefaultGiven_PassesItToWordPress(): void
	{
		$service = $this->createMock( OptionsService::class );
		$service->expects( self::once() )->method( 'get_option' )->with( 'limit', 10 )->willReturn( 10 );

		self::assertSame( 10, ( new WordPressOptions( $service ) )->get( 'limit', 10 ) );
	}

	public function testHas_WhenWordPressReturnsTheSentinel_ReturnsFalse(): void
	{
		$service = $this->createMock( OptionsService::class );
		$service->method( 'get_option' )->willReturnCallback( static fn ( string $name, mixed $default ): mixed => $name === 'exists' ? '' : $default );
		$options = new WordPressOptions( $service );

		self::assertTrue( $options->has( 'exists' ) );
		self::assertFalse( $options->has( 'missing' ) );
	}

	public function testSetAddDelete_WhenCalled_PassAutoloadAndReportResults(): void
	{
		$service = $this->createMock( OptionsService::class );
		$service->expects( self::once() )->method( 'update_option' )->with( 'a', [ 1 ], false )->willReturn( true );
		$service->expects( self::once() )->method( 'add_option' )->with( 'b', 'x', '', true )->willReturn( false );
		$service->expects( self::once() )->method( 'delete_option' )->with( 'c' )->willReturn( true );
		$options = new WordPressOptions( $service );

		$options->set( 'a', [ 1 ], autoload: false );

		self::assertFalse( $options->add( 'b', 'x', autoload: true ) );
		self::assertTrue( $options->delete( 'c' ) );
	}

	/**
	 * @dataProvider typedValues
	 */
	public function testTypedGetters_WhenValuesAreStoredAsStrings_ConvertThem( mixed $stored, string $getter, mixed $expected ): void
	{
		$options = new FakeOptions( [ 'value' => $stored ] );

		self::assertSame( $expected, $options->$getter( 'value' ) );
	}

	/**
	 * @return array<string, array{mixed, string, mixed}>
	 */
	public static function typedValues(): array
	{
		return [
			'numeric string to int'   => [ '42', 'int', 42 ],
			'non-numeric int default' => [ 'abc', 'int', 0 ],
			'"1" to true'             => [ '1', 'bool', true ],
			'"no" to false'           => [ 'no', 'bool', false ],
			'unknown bool default'    => [ 'maybe', 'bool', false ],
			'int to string'           => [ 7, 'string', '7' ],
			'array stays array'       => [ [ 'a' ], 'array', [ 'a' ] ],
			'string array default'    => [ 'a', 'array', [] ],
		];
	}

	public function testFake_WhenUsed_BehavesLikeOptions(): void
	{
		$options = new FakeOptions();

		self::assertTrue( $options->add( 'a', 1 ) );
		self::assertFalse( $options->add( 'a', 2 ) );
		self::assertSame( 1, $options->get( 'a' ) );
		self::assertSame( 'fallback', $options->get( 'b', 'fallback' ) );
		self::assertTrue( $options->delete( 'a' ) );
		self::assertFalse( $options->has( 'a' ) );
	}
}

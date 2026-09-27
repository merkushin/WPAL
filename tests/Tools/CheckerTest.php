<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\ServiceParser;
use Merkushin\Wpal\Tools\Api\WordPressParser;
use Merkushin\Wpal\Tools\Compare\Checker;
use Merkushin\Wpal\Tools\Compare\Difference;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Tools\Compare\Checker
 * @covers \Merkushin\Wpal\Tools\Api\ServiceParser
 */
class CheckerTest extends TestCase
{
	/** @var array<string, string[]> Method name => difference kinds. */
	private static array $drift;

	public static function setUpBeforeClass(): void
	{
		$wordpress = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
		$methods   = ( new ServiceParser() )->parse( __DIR__ . '/fixtures/service', $wordpress->constants );

		self::$drift = [];
		foreach ( ( new Checker() )->check( $wordpress, $methods ) as $item ) {
			self::$drift[ $item['method']->signature->name ] = array_map( static fn ( Difference $d ): string => $d->kind, $item['differences'] );
		}
	}

	public function testCheck_WhenWordPressAddedParameter_ReportsIt(): void
	{
		self::assertSame( [ Difference::PARAMETER_ADDED ], self::$drift['has_filter'] );
	}

	public function testCheck_WhenParameterRenamed_ReportsIt(): void
	{
		self::assertSame( [ Difference::PARAMETER_RENAMED ], self::$drift['do_action'] );
		self::assertSame( [ Difference::PARAMETER_RENAMED ], self::$drift['add_menu_page'] );
	}

	public function testCheck_WhenOnlyTypesOrEquivalentDefaultsDiffer_ReportsNothing(): void
	{
		self::assertArrayNotHasKey( 'get_thing', self::$drift );
	}

	public function testCheck_WhenWordPressDeprecatedFunction_ReportsIt(): void
	{
		self::assertSame( [ Difference::DEPRECATED ], self::$drift['old_function'] );
	}

	public function testCheck_WhenMethodAlreadyMarkedDeprecated_ReportsNothing(): void
	{
		self::assertArrayNotHasKey( 'renamed_function', self::$drift );
	}

	public function testCheck_WhenFunctionGoneFromWordPress_ReportsRemoved(): void
	{
		self::assertSame( [ Difference::REMOVED ], self::$drift['gone_function'] );
	}
}

<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\WordPressParser;
use Merkushin\Wpal\Tools\Coverage\Map;
use Merkushin\Wpal\Tools\Generate\ServiceGenerator;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Merkushin\Wpal\Tools\Generate\ServiceGenerator
 */
class ServiceGeneratorTest extends TestCase
{
	/** @var array<string, string> */
	private static array $files;

	public static function setUpBeforeClass(): void
	{
		$map = new Map(
			[
				'Things'    => [ 'get_thing', 'do_action', 'has_filter' ],
				'PostLinks' => [ 'add_menu_page' ],
			],
			[],
			[ 'has_filter' => [ 'hook_name' => 'string', 'return' => 'bool' ] ],
			[ 'PostLinks' ]
		);

		self::$files = ( new ServiceGenerator( ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' ), $map ) )->generate();
	}

	public function testGenerate_WhenCalled_WritesServicesFactoryAndTest(): void
	{
		self::assertSame(
			[
				'src/Service/PostLinks.php',
				'src/Service/WpPostLinks.php',
				'src/Service/Things.php',
				'src/Service/WpThings.php',
				'src/ServiceFactory.php',
				'tests/ServiceFactoryTest.php',
			],
			array_keys( self::$files )
		);
	}

	public function testGenerate_WhenCalled_ProducesValidPhpMarkedAsGenerated(): void
	{
		$parser = ( new ParserFactory() )->createForVersion( \PhpParser\PhpVersion::fromString( '7.4' ) );
		foreach ( self::$files as $path => $code ) {
			self::assertNotEmpty( $parser->parse( $code ), $path );
			self::assertStringContainsString( ServiceGenerator::MARKER, $code, $path );
		}
	}

	public function testGenerate_WhenDefaultIsConstant_WritesItsValue(): void
	{
		self::assertStringContainsString(
			"public function get_thing( \$id, \$output = 'OBJECT', array \$args = array(), &\$found = null );",
			self::$files['src/Service/Things.php']
		);
	}

	public function testGenerate_WhenTypesPinned_AddsThem(): void
	{
		self::assertStringContainsString(
			'public function has_filter( string $hook_name, $callback = false, $priority = false ): bool;',
			self::$files['src/Service/Things.php']
		);
	}

	public function testGenerate_WhenDocNamesClasses_ImportsGlobalOnesAndQualifiesNamespacedOnes(): void
	{
		$interface = self::$files['src/Service/Things.php'];

		self::assertStringContainsString( "use stdClass;\nuse WP_Post;\n", $interface );
		self::assertStringContainsString( "\t * @return WP_Post|stdClass|\\SimplePie\\SimplePie|null The thing.\n", $interface );
	}

	public function testGenerate_WhenFunctionReturnsValue_ReturnsIt(): void
	{
		$implementation = self::$files['src/Service/WpThings.php'];

		self::assertStringContainsString( 'return get_thing( $id, $output, $args, $found );', $implementation );
		self::assertStringContainsString( 'return has_filter( $hook_name, $callback, $priority );', $implementation );
	}

	public function testGenerate_WhenFunctionDocumentsNoReturn_OnlyCallsIt(): void
	{
		self::assertStringContainsString( "\t\tdo_action( \$hook_name, ...\$arg );\n", self::$files['src/Service/WpThings.php'] );
	}

	public function testGenerate_WhenServiceIsExtendable_LeavesClassOpen(): void
	{
		self::assertStringContainsString( "\nfinal class WpThings implements Things {", self::$files['src/Service/WpThings.php'] );
		self::assertStringContainsString( "\nclass WpPostLinks implements PostLinks {", self::$files['src/Service/WpPostLinks.php'] );
	}

	public function testGenerate_WhenCalled_AddsFactoryMethodsPerService(): void
	{
		$factory = self::$files['src/ServiceFactory.php'];

		self::assertStringContainsString( 'public static function create_post_links(): PostLinks {', $factory );
		self::assertStringContainsString( 'public static function set_custom_things( ?Things $custom_things ): void {', $factory );
		self::assertStringContainsString( 'ServiceFactory::set_custom_post_links( null );', self::$files['tests/ServiceFactoryTest.php'] );
	}

	public function testGenerate_WhenMapListsUnknownFunction_Throws(): void
	{
		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'wpal.map.php lists nope() under Things' );

		( new ServiceGenerator( ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' ), new Map( [ 'Things' => [ 'nope' ] ] ) ) )->generate();
	}
}

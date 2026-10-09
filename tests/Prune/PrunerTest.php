<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Prune;

use FilesystemIterator;
use InvalidArgumentException;
use Merkushin\Wpal\Prune\Pruner;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

require_once dirname( __DIR__, 2 ) . '/bin/Pruner.php';

/**
 * @covers \Merkushin\Wpal\Prune\Pruner
 */
class PrunerTest extends TestCase
{
	private const SRC = __DIR__ . '/../../src';

	/** @var string[] */
	private $temporary = [];

	protected function tearDown(): void
	{
		foreach ( $this->temporary as $directory ) {
			$this->remove( $directory );
		}
	}

	public function testServices_WhenCalled_ListsServicesThenApiServices(): void
	{
		$services = ( new Pruner( self::SRC ) )->services();

		self::assertContains( 'Hooks', $services );
		self::assertContains( 'PostTemplate', $services );
		self::assertContains( 'api:Hooks', $services );
		self::assertNotContains( 'WpHooks', $services );
		self::assertNotContains( 'api:Exception', $services );
		self::assertSame( 'api:', substr( (string) end( $services ), 0, 4 ) );
	}

	public function testPlan_WithAService_KeepsItsInterfaceAndClassButNoApi(): void
	{
		$plan = ( new Pruner( self::SRC ) )->plan( [ 'hooks', 'Options' ] );

		self::assertSame( [ 'Hooks', 'Options' ], $plan['services'] );
		self::assertSame(
			[ 'Service/Hooks.php', 'Service/Options.php', 'Service/WpHooks.php', 'Service/WpOptions.php', 'ServiceFactory.php' ],
			$plan['keep']
		);
		self::assertContains( 'Wpal.php', $plan['remove'] );
		self::assertContains( 'Api/Hooks.php', $plan['remove'] );
	}

	public function testPlan_WithAnApiService_KeepsWhatItNeeds(): void
	{
		$plan = ( new Pruner( self::SRC ) )->plan( [ 'api:Abilities' ] );

		foreach ( [
			'Api/Abilities.php',
			'Api/Abilities/Ability.php',
			'Api/Exception/WpalException.php',
			'Api/Testing/FakeAbilities.php',
			'Api/WordPress/WordPressAbilities.php',
			'Service/Abilities.php',
			'Service/WpAbilities.php',
			'Service/Capabilities.php',
			'Service/WpCapabilities.php',
			'Service/Hooks.php',
			'Service/WpHooks.php',
			'ServiceFactory.php',
			'Wpal.php',
		] as $file ) {
			self::assertContains( $file, $plan['keep'] );
		}
		self::assertContains( 'Api/Posts.php', $plan['remove'] );
		self::assertContains( 'Service/Posts.php', $plan['remove'] );
	}

	public function testPlan_WithEveryService_RemovesNothing(): void
	{
		$pruner = new Pruner( self::SRC );

		self::assertSame( [], $pruner->plan( $pruner->services() )['remove'] );
	}

	public function testPlan_WithAnUnknownService_Throws(): void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'There is no service named Teleport.' );

		( new Pruner( self::SRC ) )->plan( [ 'Hooks', 'Teleport' ] );
	}

	public function testConstruct_WithAnotherDirectory_Throws(): void
	{
		$this->expectException( InvalidArgumentException::class );

		new Pruner( __DIR__ );
	}

	public function testScan_WhenCodeUsesWpal_FindsServicesInBothLayers(): void
	{
		$plugin = $this->plugin(
			[
				'Plugin.php'      => <<<'PHP'
					<?php
					namespace Acme\Plugin;

					use Merkushin\Wpal\Service\{Hooks, WpOptions as Settings};
					use Merkushin\Wpal\ServiceFactory;

					final class Plugin {
						public function __construct( Hooks $hooks, Settings $settings ) {}

						public function title(): string {
							return ServiceFactory::create_post_template()->get_the_title();
						}
					}
					PHP,
				'Admin/Panel.php' => <<<'PHP'
					<?php
					namespace Acme\Plugin\Admin;

					use Merkushin\Wpal\Api\Posts\SortBy;
					use Merkushin\Wpal\Wpal;

					function panel( Wpal $wp ): void {
						$wp->assets()->script( 'panel' )->enqueue();
						$wp->posts()->query()->sortBy( SortBy::Modified )->get();
						// $wp->ai() is only mentioned in a comment, so it isn't kept.
						\Merkushin\Wpal\ServiceFactory::create_transient();
					}
					PHP,
				'readme.txt'      => 'Merkushin\Wpal\Service\Cron',
			]
		);

		$services = ( new Pruner( self::SRC ) )->scan( [ $plugin ] );

		self::assertSame( [ 'Hooks', 'Options', 'PostTemplate', 'Transient', 'api:Assets', 'api:Posts' ], $services );
	}

	public function testScan_WithoutWpal_IgnoresAccessorLookalikes(): void
	{
		$plugin = $this->plugin( [ 'Plugin.php' => '<?php $cache->posts(); Merkushin\Wpal\ServiceFactory::create_hooks();' ] );

		self::assertSame( [ 'Hooks' ], ( new Pruner( self::SRC ) )->scan( [ $plugin ] ) );
	}

	public function testScan_WhenCodeRefersToMissingClasses_Throws(): void
	{
		$plugin = $this->plugin(
			[ 'Plugin.php' => '<?php use Merkushin\Wpal\Service\Teleport; Merkushin\Wpal\ServiceFactory::create_beam();' ]
		);

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Service\Teleport, ServiceFactory::create_beam()' );

		( new Pruner( self::SRC ) )->scan( [ $plugin ] );
	}

	public function testPrune_WithAService_RemovesTheRestAndEmptyDirectories(): void
	{
		$src = $this->copy();

		$plan = ( new Pruner( $src ) )->prune( [ 'Hooks' ] );

		self::assertSame( $plan['keep'], $this->files( $src ) );
		self::assertDirectoryDoesNotExist( $src . '/Api' );
		self::assertSame( 'ok', $this->run_php( $src, 'Merkushin\Wpal\ServiceFactory::create_hooks(); echo "ok";' ) );
	}

	/**
	 * Every class a pruned copy keeps must load: nothing it extends, implements or uses was removed.
	 */
	public function testPrune_ForEachService_KeepsEverythingItsClassesNeed(): void
	{
		if ( PHP_VERSION_ID < 80400 ) {
			self::markTestSkipped( 'The Api layer needs PHP 8.4.' );
		}

		foreach ( ( new Pruner( self::SRC ) )->services() as $service ) {
			if ( strpos( $service, 'api:' ) !== 0 && ! in_array( $service, [ 'Hooks', 'Posts' ], true ) ) {
				continue;
			}

			$src  = $this->copy();
			$plan = ( new Pruner( $src ) )->prune( [ $service ] );

			$classes = [];
			foreach ( $plan['keep'] as $file ) {
				$classes[] = 'Merkushin\\Wpal\\' . str_replace( '/', '\\', substr( $file, 0, -4 ) );
			}
			$code = 'foreach ( ' . var_export( $classes, true ) . ' as $class ) {'
				. ' if ( ! class_exists( $class ) && ! interface_exists( $class ) && ! trait_exists( $class ) && ! enum_exists( $class ) ) { echo $class; } }';
			if ( strpos( $service, 'api:' ) === 0 ) {
				$code .= ' ( new Merkushin\Wpal\Wpal() )->' . lcfirst( substr( $service, 4 ) ) . '();';
			}

			self::assertSame( '', $this->run_php( $src, $code ), $service );
		}
	}

	public function testCli_WithDryRun_PrintsThePlanAsJson(): void
	{
		$package = dirname( $this->copy() );

		$output = $this->run_cli( [ '--path=' . $package, '--dry-run', '--format=json', 'Hooks,Options' ], $exit );
		$plan   = json_decode( $output, true );

		self::assertSame( 0, $exit );
		self::assertSame( [ 'Hooks', 'Options' ], $plan['services'] );
		self::assertTrue( $plan['dry_run'] );
		self::assertFileExists( $package . '/src/Service/Posts.php' );
	}

	public function testCli_WithAnUnknownService_ExitsWithTwo(): void
	{
		$package = dirname( $this->copy() );

		$this->run_cli( [ '--path=' . $package, 'Teleport' ], $exit );

		self::assertSame( 2, $exit );
	}

	/**
	 * @param array<string, string> $files
	 */
	private function plugin( array $files ): string
	{
		$directory = $this->temporary_directory();
		foreach ( $files as $file => $code ) {
			if ( ! is_dir( dirname( $directory . '/' . $file ) ) ) {
				mkdir( dirname( $directory . '/' . $file ), 0777, true );
			}
			file_put_contents( $directory . '/' . $file, $code );
		}

		return $directory;
	}

	/**
	 * Copies src/ into a temporary package directory, returning the copy's src/.
	 */
	private function copy(): string
	{
		$target = $this->temporary_directory() . '/src';
		$source = (string) realpath( self::SRC );
		$files  = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
		mkdir( $target );
		foreach ( $files as $file ) {
			$path = $target . substr( $file->getPathname(), strlen( $source ) );
			if ( $file->isDir() ) {
				mkdir( $path );
			} else {
				copy( $file->getPathname(), $path );
			}
		}

		return $target;
	}

	/**
	 * @return string[]
	 */
	private function files( string $src ): array
	{
		$files = [];
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $src, FilesystemIterator::SKIP_DOTS ) ) as $file ) {
			$files[] = substr( $file->getPathname(), strlen( $src ) + 1 );
		}
		sort( $files );

		return $files;
	}

	/**
	 * Runs code in a separate PHP process that autoloads WPAL from the given src/ only.
	 */
	private function run_php( string $src, string $code ): string
	{
		$autoload = 'spl_autoload_register( function ( $class ) {'
			. ' $file = ' . var_export( $src, true ) . ' . "/" . str_replace( "\\\\", "/", substr( $class, strlen( "Merkushin\\\\Wpal\\\\" ) ) ) . ".php";'
			. ' if ( strpos( $class, "Merkushin\\\\Wpal\\\\" ) === 0 && is_file( $file ) ) { require $file; } } );';
		$script   = $this->temporary_directory() . '/run.php';
		file_put_contents( $script, "<?php\n" . $autoload . "\n" . $code );

		exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $script ) . ' 2>&1', $output );

		return implode( "\n", $output );
	}

	/**
	 * @param string[] $arguments
	 */
	private function run_cli( array $arguments, ?int &$exit ): string
	{
		$command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( dirname( __DIR__, 2 ) . '/bin/wpal-prune' );
		foreach ( $arguments as $argument ) {
			$command .= ' ' . escapeshellarg( $argument );
		}

		exec( $command . ' 2>/dev/null', $output, $exit );

		return implode( "\n", $output );
	}

	private function temporary_directory(): string
	{
		$directory = sys_get_temp_dir() . '/wpal-prune-' . bin2hex( random_bytes( 6 ) );
		mkdir( $directory );
		$this->temporary[] = $directory;

		return $directory;
	}

	private function remove( string $directory ): void
	{
		if ( ! is_dir( $directory ) ) {
			return;
		}

		$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $files as $file ) {
			$file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
		}
		rmdir( $directory );
	}
}

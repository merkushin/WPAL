<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use InvalidArgumentException;
use Merkushin\Wpal\Api\Assets\InlinePosition;
use Merkushin\Wpal\Api\Testing\FakeAssets;
use Merkushin\Wpal\Api\WordPress\WordPressAssets;
use Merkushin\Wpal\Service\Assets as AssetsService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressAssets
 * @covers \Merkushin\Wpal\Api\Testing\FakeAssets
 * @covers \Merkushin\Wpal\Api\Assets\Script
 * @covers \Merkushin\Wpal\Api\Assets\Style
 */
class AssetsTest extends TestCase
{
	public function testEnqueueScript_WhenSourceGiven_RegistersWithArgsThenEnqueues(): void
	{
		$service = $this->createMock( AssetsService::class );
		$service->expects( self::once() )->method( 'wp_register_script' )
			->with( 'app', 'https://example.test/app.js', [ 'wp-element' ], null, [ 'in_footer' => true, 'strategy' => 'defer' ] );
		$service->expects( self::once() )->method( 'wp_enqueue_script' )->with( 'app' );

		( new WordPressAssets( $service ) )->script( 'app' )
			->src( 'https://example.test/app.js' )
			->deps( 'wp-element' )
			->version( null )
			->inFooter()
			->defer()
			->enqueue();
	}

	public function testEnqueueScript_WhenDataGiven_AddsSafeJsonBeforeTheScript(): void
	{
		$service = $this->createMock( AssetsService::class );
		$service->expects( self::once() )->method( 'wp_add_inline_script' )
			->with( 'app', 'var appData = {"html":"\\u003C/script\\u003E","n":1};', 'before' );
		$service->expects( self::never() )->method( 'wp_register_script' );

		( new WordPressAssets( $service ) )->script( 'app' )->data( 'appData', [ 'html' => '</script>', 'n' => 1 ] )->enqueue();
	}

	public function testRegisterScript_WhenTranslationsGiven_SetsThem(): void
	{
		$service = $this->createMock( AssetsService::class );
		$service->expects( self::once() )->method( 'wp_register_script' )->with( 'app', false, [], false, [ 'in_footer' => false ] );
		$service->expects( self::once() )->method( 'wp_set_script_translations' )->with( 'app', 'my-plugin', '' );

		( new WordPressAssets( $service ) )->script( 'app' )->translations( 'my-plugin' )->register();
	}

	public function testEnqueueStyle_WhenConfigured_PassesMediaAndInlineCss(): void
	{
		$service = $this->createMock( AssetsService::class );
		$service->expects( self::once() )->method( 'wp_register_style' )->with( 'print', '/print.css', [], '1.2', 'print' );
		$service->expects( self::once() )->method( 'wp_add_inline_style' )->with( 'print', 'body{color:#000}' );
		$service->expects( self::once() )->method( 'wp_enqueue_style' )->with( 'print' );

		( new WordPressAssets( $service ) )->style( 'print' )->src( '/print.css' )->version( '1.2' )->media( 'print' )->inline( 'body{color:#000}' )->enqueue();
	}

	public function testScript_WhenHandleEmpty_Throws(): void
	{
		$this->expectException( InvalidArgumentException::class );

		( new FakeAssets() )->script( '' );
	}

	public function testFake_WhenScriptsEnqueuedAndDequeued_TracksThem(): void
	{
		$assets = new FakeAssets();

		$assets->script( 'app' )->src( '/app.js' )->inline( 'init();', InlinePosition::After )->enqueue();
		$assets->style( 'app' )->src( '/app.css' )->register();

		self::assertTrue( $assets->isScriptEnqueued( 'app' ) );
		self::assertFalse( $assets->isStyleEnqueued( 'app' ) );
		self::assertSame( '/app.js', $assets->scripts['app']->src );
		self::assertSame( 'init();', $assets->scripts['app']->inline[0]['code'] );

		$assets->dequeueScript( 'app' );

		self::assertFalse( $assets->isScriptEnqueued( 'app' ) );
	}
}

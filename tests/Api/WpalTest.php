<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use Merkushin\Wpal\Api\Testing\FakeOptions;
use Merkushin\Wpal\Api\WordPress\WordPressAssets;
use Merkushin\Wpal\Api\WordPress\WordPressHooks;
use Merkushin\Wpal\Api\WordPress\WordPressPosts;
use Merkushin\Wpal\Service\Options as OptionsService;
use Merkushin\Wpal\ServiceFactory;
use Merkushin\Wpal\Wpal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Wpal
 */
class WpalTest extends TestCase
{
	protected function tearDown(): void
	{
		ServiceFactory::set_custom_options( null );
	}

	public function testServices_WhenNotGiven_AreBuiltOnWordPressOnce(): void
	{
		$wp = new Wpal();

		self::assertInstanceOf( WordPressHooks::class, $wp->hooks() );
		self::assertInstanceOf( WordPressAssets::class, $wp->assets() );
		self::assertInstanceOf( WordPressPosts::class, $wp->posts() );
		self::assertSame( $wp->posts(), $wp->posts() );
	}

	public function testServices_WhenGiven_AreUsed(): void
	{
		$options = new FakeOptions( [ 'a' => 1 ] );

		self::assertSame( $options, ( new Wpal( options: $options ) )->options() );
	}

	public function testServices_WhenServiceFactoryHasCustomService_UseIt(): void
	{
		$service = $this->createMock( OptionsService::class );
		$service->expects( self::once() )->method( 'get_option' )->with( 'blogname' )->willReturn( 'My site' );
		ServiceFactory::set_custom_options( $service );

		self::assertSame( 'My site', ( new Wpal() )->options()->get( 'blogname' ) );
	}
}

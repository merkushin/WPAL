<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use Merkushin\Wpal\Api\Testing\FakeHooks;
use Merkushin\Wpal\Api\WordPress\WordPressHooks;
use Merkushin\Wpal\Service\Hooks as HooksService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressHooks
 * @covers \Merkushin\Wpal\Api\Testing\FakeHooks
 * @covers \Merkushin\Wpal\Api\CallbackArity
 * @covers \Merkushin\Wpal\Api\Subscription
 */
class HooksTest extends TestCase
{
	public function testOnAction_WhenCalled_PassesArgumentCountFromCallbackSignature(): void
	{
		$service = $this->createMock( HooksService::class );
		$service->expects( self::exactly( 3 ) )
			->method( 'add_action' )
			->willReturnCallback(
				static function ( string $hook, callable $callback, int $priority, int $accepted ): bool {
					self::assertSame( [ 'init', 5 ], [ $hook, $priority ] );
					self::assertContains( $accepted, [ 0, 2, PHP_INT_MAX ] );

					return true;
				}
			);
		$hooks = new WordPressHooks( $service );

		$hooks->onAction( 'init', static function (): void {}, 5 );
		$hooks->onAction( 'init', static function ( int $a, int $b ): void {}, 5 );
		$hooks->onAction( 'init', static function ( mixed ...$all ): void {}, 5 );
	}

	public function testSubscriptionRemove_WhenCalledTwice_DetachesOnce(): void
	{
		$callback = static fn ( string $value ): string => $value;
		$service  = $this->createMock( HooksService::class );
		$service->method( 'add_filter' )->willReturn( true );
		$service->expects( self::once() )->method( 'remove_filter' )->with( 'the_title', $callback, 20 )->willReturn( true );

		$subscription = ( new WordPressHooks( $service ) )->onFilter( 'the_title', $callback, 20 );
		$subscription->remove();
		$subscription->remove();

		self::assertFalse( $subscription->isActive() );
	}

	public function testApplyFilters_WhenCalled_DelegatesToWordPress(): void
	{
		$service = $this->createMock( HooksService::class );
		$service->expects( self::once() )->method( 'apply_filters' )->with( 'the_title', 'Hi', 42 )->willReturn( 'Hello' );

		self::assertSame( 'Hello', ( new WordPressHooks( $service ) )->applyFilters( 'the_title', 'Hi', 42 ) );
	}

	public function testFakeDoAction_WhenCallbacksAttached_RunsThemByPriorityWithDeclaredArguments(): void
	{
		$hooks = new FakeHooks();
		$calls = [];
		$hooks->onAction( 'save', static function ( int $id ) use ( &$calls ): void { $calls[] = "late {$id}"; }, 20 );
		$hooks->onAction( 'save', static function () use ( &$calls ): void { $calls[] = 'no args'; } );
		$hooks->onAction( 'save', static function ( int $id, string $status ) use ( &$calls ): void { $calls[] = "{$id} {$status}"; } );

		$hooks->doAction( 'save', 7, 'publish' );

		self::assertSame( [ 'no args', '7 publish', 'late 7' ], $calls );
		self::assertSame( 1, $hooks->didAction( 'save' ) );
	}

	public function testFakeApplyFilters_WhenFiltersAttached_ChainsThem(): void
	{
		$hooks = new FakeHooks();
		$hooks->onFilter( 'title', static fn ( string $title ): string => $title . '!' );
		$hooks->onFilter( 'title', static fn ( string $title, string $suffix ): string => $title . $suffix, 5 );

		self::assertSame( 'Hi?!', $hooks->applyFilters( 'title', 'Hi', '?' ) );
	}

	public function testFakeSubscriptionRemove_WhenCalled_StopsCallback(): void
	{
		$hooks        = new FakeHooks();
		$subscription = $hooks->onFilter( 'title', static fn ( string $title ): string => strtoupper( $title ) );

		$subscription->remove();

		self::assertFalse( $hooks->hasFilter( 'title' ) );
		self::assertSame( 'hi', $hooks->applyFilters( 'title', 'hi' ) );
	}
}

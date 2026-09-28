<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use InvalidArgumentException;
use Merkushin\Wpal\Api\Exception\AbilityNotFound;
use Merkushin\Wpal\Api\Exception\RegistrationClosed;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Api\Testing\FakeAbilities;
use Merkushin\Wpal\Api\WordPress\WordPressAbilities;
use Merkushin\Wpal\Service\Abilities as AbilitiesService;
use Merkushin\Wpal\Service\Capabilities as CapabilitiesService;
use Merkushin\Wpal\Service\Hooks as HooksService;
use Merkushin\Wpal\Tests\Api\Stubs\Stubs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressAbilities
 * @covers \Merkushin\Wpal\Api\Testing\FakeAbilities
 * @covers \Merkushin\Wpal\Api\Abilities\Ability
 * @covers \Merkushin\Wpal\Api\Abilities\AbilityInfo
 * @covers \Merkushin\Wpal\Api\Exception\AbilityNotFound
 * @covers \Merkushin\Wpal\Api\Exception\RegistrationClosed
 */
class AbilitiesTest extends TestCase
{
	/** @var AbilitiesService&MockObject */
	private AbilitiesService $service;

	/** @var HooksService&MockObject */
	private HooksService $hooks;

	/** @var CapabilitiesService&MockObject */
	private CapabilitiesService $capabilities;

	public static function setUpBeforeClass(): void
	{
		Stubs::load( 'WP_Error', 'WP_Ability' );
	}

	protected function setUp(): void
	{
		$this->service      = $this->createMock( AbilitiesService::class );
		$this->hooks        = $this->createMock( HooksService::class );
		$this->capabilities = $this->createMock( CapabilitiesService::class );
	}

	public function testRegister_WhenAbilitiesAreBeingRegistered_RegistersNowWithWordPressArgs(): void
	{
		$this->hooks->method( 'doing_action' )->with( 'wp_abilities_api_init' )->willReturn( true );
		$this->service->expects( self::once() )->method( 'wp_register_ability' )->with(
			'my-plugin/summarize',
			self::callback(
				static function ( array $args ): bool {
					self::assertSame( [ 'Summarize', 'Summarizes a post.', 'content' ], [ $args['label'], $args['description'], $args['category'] ] );
					self::assertSame( [ 'type' => 'object' ], $args['input_schema'] );
					self::assertArrayNotHasKey( 'output_schema', $args );
					self::assertSame( [ 'readonly' => true ], $args['meta']['annotations'] );
					self::assertTrue( $args['meta']['public'] );
					self::assertSame( 'x', $args['meta']['source'] );
					self::assertSame( 'done', ( $args['execute_callback'] )( [] ) );

					return true;
				}
			)
		);

		$this->summarize( $this->wordPress() )->register();
	}

	public function testRegister_WhenRegistrationHasNotStarted_WaitsForTheHook(): void
	{
		$this->hooks->method( 'doing_action' )->willReturn( false );
		$this->hooks->method( 'did_action' )->willReturn( 0 );
		$this->hooks->expects( self::once() )->method( 'add_action' )->with( 'wp_abilities_api_init', self::isInstanceOf( \Closure::class ), 10, 0 );
		$this->service->expects( self::never() )->method( 'wp_register_ability' );

		$this->summarize( $this->wordPress() )->register();
	}

	public function testRegister_WhenRegistrationIsOver_Throws(): void
	{
		$this->hooks->method( 'doing_action' )->willReturn( false );
		$this->hooks->method( 'did_action' )->willReturn( 1 );

		$this->expectException( RegistrationClosed::class );

		$this->summarize( $this->wordPress() )->register();
	}

	public function testRequireCapability_WhenChecked_AsksWordPress(): void
	{
		$this->hooks->method( 'doing_action' )->willReturn( true );
		$this->capabilities->expects( self::once() )->method( 'current_user_can' )->with( 'edit_posts' )->willReturn( true );
		$this->service->method( 'wp_register_ability' )->willReturnCallback(
			static function ( string $name, array $args ): ?\WP_Ability {
				self::assertTrue( ( $args['permission_callback'] )() );

				return null;
			}
		);

		$this->wordPress()->define( 'my-plugin/edit' )->label( 'Edit' )->description( 'Edits.' )->category( 'content' )
			->requireCapability( 'edit_posts' )->execute( static fn (): bool => true )->register();
	}

	public function testRegister_WhenPartsMissing_ListsThem(): void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Ability my-plugin/x is missing: description, category, execute, permission or requireCapability.' );

		( new FakeAbilities() )->define( 'my-plugin/x' )->label( 'X' )->register();
	}

	public function testDefine_WhenNameInvalid_Throws(): void
	{
		$this->expectException( InvalidArgumentException::class );

		( new FakeAbilities() )->define( 'No Namespace' );
	}

	public function testExecute_WhenWordPressReturnsError_Throws(): void
	{
		$this->service->method( 'wp_get_ability' )->willReturn( Stubs::ability( 'my-plugin/x', [], Stubs::error( 'ability_invalid_permissions', 'Nope.' ) ) );

		try {
			$this->wordPress()->execute( 'my-plugin/x', [ 'id' => 1 ] );
			self::fail( 'Expected WordPressError.' );
		} catch ( WordPressError $e ) {
			self::assertSame( 'ability_invalid_permissions', $e->errorCode );
		}
	}

	public function testExecuteAndFind_WhenAbilityMissing_Throw(): void
	{
		$this->service->method( 'wp_get_ability' )->willReturn( null );

		self::assertNull( $this->wordPress()->find( 'my-plugin/x' ) );
		$this->expectException( AbilityNotFound::class );
		$this->wordPress()->execute( 'my-plugin/x' );
	}

	public function testAll_WhenFilteredByCategory_MapsAbilities(): void
	{
		$this->service->method( 'wp_get_abilities' )->willReturn(
			[
				Stubs::ability( 'a/one', [ 'label' => 'One', 'category' => 'content', 'meta' => [ 'annotations' => [ 'readonly' => true ], 'public' => true ] ] ),
				Stubs::ability( 'a/two', [ 'label' => 'Two', 'category' => 'site' ] ),
			]
		);

		$abilities = $this->wordPress()->all( 'content' );

		self::assertCount( 1, $abilities );
		self::assertSame( [ 'a/one', 'One', true, true, null ], [ $abilities[0]->name, $abilities[0]->label, $abilities[0]->readonly, $abilities[0]->public, $abilities[0]->destructive ] );
	}

	public function testFake_WhenExecuted_ChecksCapabilitiesAndRuns(): void
	{
		$abilities = new FakeAbilities();
		$this->summarize( $abilities )->register();

		try {
			$abilities->execute( 'my-plugin/summarize', [ 'id' => 1 ] );
			self::fail( 'Expected permission error.' );
		} catch ( WordPressError $e ) {
			self::assertSame( 'ability_invalid_permissions', $e->errorCode );
		}

		$abilities->actAs( [ 'read' ] );

		self::assertSame( 'done', $abilities->execute( 'my-plugin/summarize', [ 'id' => 1 ] ) );
		self::assertTrue( $abilities->get( 'my-plugin/summarize' )->readonly );
	}

	private function wordPress(): WordPressAbilities
	{
		return new WordPressAbilities( $this->service, $this->hooks, $this->capabilities );
	}

	private function summarize( \Merkushin\Wpal\Api\Abilities $abilities ): \Merkushin\Wpal\Api\Abilities\Ability
	{
		return $abilities->define( 'my-plugin/summarize' )
			->label( 'Summarize' )
			->description( 'Summarizes a post.' )
			->category( 'content' )
			->input( [ 'type' => 'object' ] )
			->readonly()
			->public()
			->meta( [ 'source' => 'x' ] )
			->requireCapability( 'read' )
			->execute( static fn ( array $input ): string => 'done' );
	}
}

<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\Snapshot;
use Merkushin\Wpal\Tools\Api\WordPressParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Tools\Api\WordPressParser
 * @covers \Merkushin\Wpal\Tools\Api\SignatureBuilder
 * @covers \Merkushin\Wpal\Tools\Api\DocBlock
 */
class WordPressParserTest extends TestCase
{
	private static Snapshot $snapshot;

	public static function setUpBeforeClass(): void
	{
		self::$snapshot = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
	}

	public function testParse_WhenCalled_ReadsVersion(): void
	{
		self::assertSame( '9.9.1', self::$snapshot->version );
	}

	public function testParse_WhenFunctionHasParameters_RecordsThemInOrder(): void
	{
		$function = self::fn( 'has_filter' );

		self::assertSame( 'has_filter($hook_name, $callback = false, $priority = false)', $function->describe() );
		self::assertSame( 'wp-includes/plugin.php', $function->file );
		self::assertSame( '2.5.0', $function->since );
	}

	public function testParse_WhenNoopStandInExists_KeepsRealImplementation(): void
	{
		self::assertSame( 'wp-includes/plugin.php', self::fn( 'has_filter' )->file );
	}

	public function testParse_WhenFunctionIsVariadic_MarksParameter(): void
	{
		$parameter = self::fn( 'do_action' )->parameters[1];

		self::assertTrue( $parameter->variadic );
		self::assertSame( 'arg', $parameter->name );
	}

	public function testParse_WhenDefaultIsConstant_ResolvesItsValue(): void
	{
		$parameters = self::fn( 'get_thing' )->parameters;

		self::assertSame( 'OBJECT', $parameters[1]->default );
		self::assertTrue( $parameters[1]->defaultResolved );
		self::assertSame( 'OBJECT', $parameters[1]->defaultValue );
		self::assertSame( 'array', $parameters[2]->type );
		self::assertSame( [], $parameters[2]->defaultValue );
		self::assertTrue( $parameters[3]->byRef );
	}

	public function testParse_WhenConstantDefinedInsideFunction_ResolvesIt(): void
	{
		self::assertSame( 3600, self::$snapshot->constants['HOUR_IN_SECONDS'] );
	}

	public function testParse_WhenDocHasDeprecatedTag_RecordsVersion(): void
	{
		self::assertSame( '6.2.0', self::fn( 'old_function' )->deprecated );
	}

	public function testParse_WhenFunctionCallsDeprecatedFunction_MarksDeprecated(): void
	{
		self::assertTrue( self::fn( 'renamed_function' )->isDeprecated() );
	}

	public function testParse_WhenPrivate_MarksPrivate(): void
	{
		self::assertTrue( self::fn( '_private_helper' )->private );
		self::assertTrue( self::fn( 'internal_but_unprefixed' )->private );
		self::assertFalse( self::fn( 'get_thing' )->private );
	}

	public function testParse_WhenGuardedByFunctionExists_MarksPluggable(): void
	{
		self::assertTrue( self::fn( 'wp_mail' )->pluggable );
		self::assertFalse( self::fn( 'get_thing' )->pluggable );
	}

	public function testParse_WhenBundledLibraryOrClassMethod_SkipsIt(): void
	{
		self::assertNull( self::$snapshot->get( 'sodium_crypto_box' ) );
		self::assertSame( 'wp-includes/functions.php', self::fn( 'get_thing' )->file );
	}

	public function testParse_WhenAdminFunction_IncludesIt(): void
	{
		self::assertSame( 'wp-admin/includes/plugin.php', self::fn( 'add_menu_page' )->file );
	}

	private static function fn( string $name ): FunctionSignature
	{
		$function = self::$snapshot->get( $name );
		self::assertNotNull( $function, "{$name}() not found." );

		return $function;
	}

	public function testSaveAndLoad_WhenRoundTripped_KeepsFunctions(): void
	{
		$file = sys_get_temp_dir() . '/wpal-snapshot-' . uniqid() . '.json';
		self::$snapshot->save( $file );

		$loaded = Snapshot::load( $file );
		unlink( $file );

		self::assertSame( self::$snapshot->version, $loaded->version );
		self::assertEquals( self::$snapshot->functions, $loaded->functions );
		self::assertSame( self::$snapshot->constants, $loaded->constants );
	}
}

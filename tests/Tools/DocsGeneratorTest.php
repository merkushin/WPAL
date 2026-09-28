<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\Snapshot;
use Merkushin\Wpal\Tools\Coverage\Map;
use Merkushin\Wpal\Tools\Docs\DocsGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Generates docs from this repository's own src/Api and a small map.
 *
 * @covers \Merkushin\Wpal\Tools\Docs\DocsGenerator
 */
class DocsGeneratorTest extends TestCase
{
	/** @var array<string, string> */
	private static array $docs;

	public static function setUpBeforeClass(): void
	{
		$wordpress = new Snapshot( '9.9', [ 'get_the_id' => new FunctionSignature( 'get_the_ID' ), 'do_action' => new FunctionSignature( 'do_action' ) ] );
		$map       = new Map( [ 'Hooks' => [ 'do_action' ], 'PostTemplate' => [ 'get_the_id' ] ] );

		self::$docs = ( new DocsGenerator( dirname( __DIR__, 2 ), $map, $wordpress ) )->generate();
	}

	public function testApiReference_WhenGenerated_StartsWithEntryPointAndDocumentsSignatures(): void
	{
		$api = self::$docs['docs/api.md'];

		self::assertStringContainsString( "## Wpal\n", $api );
		self::assertLessThan( strpos( $api, '## Api\\Hooks' ), strpos( $api, '## Wpal' ) );
		self::assertStringContainsString( '- `define( string $name ): Ability`', $api );
		self::assertStringContainsString( '- `all( ?string $category = null ): list<AbilityInfo>`', $api );
		self::assertStringContainsString( "```php\n\$wp->abilities()->define(", $api );
		self::assertStringNotContainsString( 'WordPressHooks', $api );
		self::assertStringNotContainsString( 'CallbackArity', $api );
	}

	public function testServiceIndex_WhenGenerated_ListsServicesWithWordPressSpelling(): void
	{
		$services = self::$docs['docs/services.md'];

		self::assertStringContainsString( '2 methods in 2 services, one per WordPress 9.9 function', $services );
		self::assertStringContainsString( "## PostTemplate\n\n`get_the_ID`", $services );
	}

	public function testLlmsTxt_WhenGenerated_SummarizesAndLinksDocs(): void
	{
		$llms = self::$docs['llms.txt'];

		self::assertStringStartsWith( "# WPAL\n\n> ", $llms );
		self::assertStringContainsString( 'wraps all 2 public WordPress 9.9 functions in 2 services', $llms );
		self::assertStringContainsString( '[Api reference](https://github.com/merkushin/WPAL/blob/main/docs/api.md)', $llms );
	}
}

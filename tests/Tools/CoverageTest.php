<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Tools;

use Merkushin\Wpal\Tools\Api\ServiceParser;
use Merkushin\Wpal\Tools\Api\WordPressParser;
use Merkushin\Wpal\Tools\Coverage\Coverage;
use Merkushin\Wpal\Tools\Coverage\Map;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Tools\Coverage\Coverage
 * @covers \Merkushin\Wpal\Tools\Coverage\Map
 */
class CoverageTest extends TestCase
{
	/** The fixture service's methods, as `services` would list them. */
	private const HOOKS = [
		'Hooks' => [ 'has_filter', 'do_action', 'get_thing', 'old_function', 'renamed_function', 'add_menu_page' ],
	];

	public function testCompute_WhenCalled_SortsFunctionsIntoWrappedIgnoredAndUntriaged(): void
	{
		$wordpress = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
		$methods   = ( new ServiceParser() )->parse( __DIR__ . '/fixtures/service', $wordpress->constants );
		$map       = new Map( [], [], [], [], [ '/^wp_ajax_/' => 'admin AJAX handler' ], [ 'wp-includes/pluggable.php' => 'pluggable' ] );

		$coverage = ( new Coverage() )->compute( $wordpress, $methods, $map );

		self::assertSame( count( $wordpress->functions ), $coverage['total'] );
		self::assertSame( [ 'add_menu_page', 'do_action', 'get_thing', 'has_filter', 'old_function', 'renamed_function' ], $coverage['wrapped']['Hooks'] );
		self::assertSame(
			[
				'admin AJAX handler' => [ 'wp_ajax_do_something' ],
				'pluggable'          => [ 'wp_mail' ],
				'private'            => [ '_private_helper', 'internal_but_unprefixed' ],
			],
			$coverage['ignored']
		);
		self::assertSame( [ 'wp-includes/functions.php' => [ 'get_the_ID', 'wp_initial_constants' ] ], $coverage['untriaged'] );
	}

	public function testCompute_WhenMapAssignsUnwrappedFunction_ReportsItAsPlanned(): void
	{
		$coverage = $this->compute( new Map( self::HOOKS, [ 'Mail' => [ 'wp_mail' ], 'Setup' => [ '/^wp_initial_/' ] ] ) );

		self::assertSame( [ 'Mail' => [ 'wp_mail' ], 'Setup' => [ 'wp_initial_constants' ] ], $coverage['planned'] );
		self::assertSame( [ 'get_the_ID' ], $coverage['untriaged']['wp-includes/functions.php'] );
		self::assertSame( [ 'Hooks::gone_function() wraps a function WordPress 9.9.1 doesn\'t have.' ], $coverage['problems'] );
	}

	public function testCompute_WhenMapHasMistakes_ReportsProblems(): void
	{
		$coverage = $this->compute(
			new Map(
				[
					'Hooks'   => [ 'do_action', 'get_thing', 'old_function', 'renamed_function', 'gone_function', 'add_menu_page' ],
					'Filters' => [ 'has_filter' ],
					'Setup'   => [ 'wp_initial_constants' ],
				],
				[
					'Mail'    => [ 'wp_mail', 'no_such_function' ],
					'Email'   => [ 'wp_mail' ],
					'Legacy'  => [ '_private_helper' ],
				]
			)
		);

		self::assertSame(
			[
				'Hooks::gone_function() wraps a function WordPress 9.9.1 doesn\'t have.',
				'_private_helper() is listed under Legacy but is private.',
				'gone_function() is listed under Hooks but WordPress 9.9.1 has no such function.',
				'has_filter() is listed under Filters but wrapped in Hooks.',
				'no_such_function() is listed under Mail but WordPress 9.9.1 has no such function.',
				'wp_initial_constants() is listed under Setup but not generated; run bin/wpal fix.',
				'wp_mail() is listed under Mail and Email.',
			],
			$coverage['problems']
		);
	}

	/**
	 * @return array{planned: array<string, string[]>, untriaged: array<string, string[]>, problems: string[]}
	 */
	private function compute( Map $map ): array
	{
		$wordpress = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
		$methods   = ( new ServiceParser() )->parse( __DIR__ . '/fixtures/service', $wordpress->constants );

		return ( new Coverage() )->compute( $wordpress, $methods, $map );
	}

	public function testIgnoreReason_WhenDeprecated_PrefersDeprecated(): void
	{
		$wordpress  = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
		$deprecated = $wordpress->get( 'old_function' );
		$public     = $wordpress->get( 'get_thing' );
		self::assertNotNull( $deprecated );
		self::assertNotNull( $public );

		self::assertSame( 'deprecated', ( new Map() )->ignoreReason( $deprecated ) );
		self::assertNull( ( new Map() )->ignoreReason( $public ) );
	}
}

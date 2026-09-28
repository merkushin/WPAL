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
	public function testCompute_WhenCalled_SortsFunctionsIntoWrappedIgnoredAndUntriaged(): void
	{
		$wordpress = ( new WordPressParser() )->parse( __DIR__ . '/fixtures/wordpress' );
		$methods   = ( new ServiceParser() )->parse( __DIR__ . '/fixtures/service', $wordpress->constants );
		$map       = new Map( [ '/^wp_ajax_/' => 'admin AJAX handler' ], [ 'wp-includes/pluggable.php' => 'pluggable' ] );

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
		self::assertSame( [ 'wp-includes/functions.php' => [ 'wp_initial_constants' ] ], $coverage['untriaged'] );
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

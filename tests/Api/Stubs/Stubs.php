<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tests\Api\Stubs;

/**
 * Builds stand-ins for WordPress classes the Api layer receives, so tests run without WordPress.
 *
 * PHPStan only scans this file: tests stay typed against WordPress's real classes.
 */
final class Stubs {
	public static function load( string ...$classes ): void {
		foreach ( $classes as $class ) {
			if ( ! class_exists( $class ) ) {
				require __DIR__ . "/{$class}.php";
			}
		}
	}

	/**
	 * @param array<string, mixed> $args wp_register_ability() arguments.
	 * @param mixed                $result What execute() returns.
	 */
	public static function ability( string $name, array $args = [], mixed $result = null ): \WP_Ability {
		self::load( 'WP_Ability' );

		return new \WP_Ability( $name, $args, $result );
	}

	/**
	 * @param mixed $result What generate_text() returns.
	 */
	public static function promptBuilder( mixed $result = '' ): \WP_AI_Client_Prompt_Builder {
		self::load( 'WP_AI_Client_Prompt_Builder' );

		return new \WP_AI_Client_Prompt_Builder( $result );
	}

	/**
	 * Methods called on a prompt builder from promptBuilder(), with their arguments.
	 *
	 * @return list<array{string, list<mixed>}>
	 */
	public static function calls( \WP_AI_Client_Prompt_Builder $builder ): array {
		return $builder->calls;
	}

	public static function error( string $code, string $message ): \WP_Error {
		self::load( 'WP_Error' );

		return new \WP_Error( $code, $message );
	}
}

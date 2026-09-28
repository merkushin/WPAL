<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Exception;

use RuntimeException;

/**
 * WordPress reported a failure, usually as a `WP_Error`.
 */
final class WordPressError extends RuntimeException implements WpalException {
	/**
	 * @param string $errorCode WordPress's error code, e.g. `invalid_post_type`.
	 * @param mixed  $data      WordPress's error data.
	 */
	public function __construct(
		string $message,
		public readonly string $errorCode = '',
		public readonly mixed $data = null,
	) {
		parent::__construct( $message );
	}

	/**
	 * @param object $error A `WP_Error`.
	 */
	public static function fromWpError( object $error ): self {
		$code    = method_exists( $error, 'get_error_code' ) ? (string) $error->get_error_code() : '';
		$message = method_exists( $error, 'get_error_message' ) ? (string) $error->get_error_message() : '';
		$data    = method_exists( $error, 'get_error_data' ) ? $error->get_error_data() : null;

		return new self( $message !== '' ? $message : "WordPress error {$code}.", $code, $data );
	}

	/**
	 * Whether a WordPress return value is a `WP_Error`.
	 */
	public static function isWpError( mixed $value ): bool {
		return is_object( $value ) && is_a( $value, 'WP_Error' );
	}
}

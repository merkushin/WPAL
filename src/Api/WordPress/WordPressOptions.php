<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use Merkushin\Wpal\Api\Options;
use Merkushin\Wpal\Api\Options\TypedGetters;
use Merkushin\Wpal\Service\Options as OptionsService;
use stdClass;

final class WordPressOptions implements Options {
	use TypedGetters;

	public function __construct( private readonly OptionsService $options ) {
	}

	public function get( string $name, mixed $default = null ): mixed {
		// Without a default, WordPress applies a default registered with register_setting().
		if ( $default === null ) {
			$value = $this->options->get_option( $name );

			return $value === false ? null : $value;
		}

		return $this->options->get_option( $name, $default );
	}

	public function has( string $name ): bool {
		// A unique default tells a missing option apart from any stored value, and skips registered defaults.
		$missing = new stdClass();

		return $this->options->get_option( $name, $missing ) !== $missing;
	}

	public function set( string $name, mixed $value, ?bool $autoload = null ): void {
		// WordPress returns false both on failure and when the value is unchanged, so there is nothing to report.
		$this->options->update_option( $name, $value, $autoload );
	}

	public function add( string $name, mixed $value, ?bool $autoload = null ): bool {
		return (bool) $this->options->add_option( $name, $value, '', $autoload );
	}

	public function delete( string $name ): bool {
		return (bool) $this->options->delete_option( $name );
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Assets;

use JsonException;
use InvalidArgumentException;
use Merkushin\Wpal\Api\Assets;

/**
 * A script to register or enqueue. Configure it, then call register() or enqueue().
 *
 *     $wp->assets()->script( 'my-app' )->src( $url )->deps( 'wp-element' )->defer()->enqueue();
 */
final class Script {
	/** @var non-empty-string */
	public readonly string $handle;

	/** @var non-empty-string|null */
	public private(set) ?string $src = null;

	/** @var list<non-empty-string> */
	public private(set) array $deps = [];

	/** WordPress's version when false (its default), none when null. */
	public private(set) string|false|null $version = false;

	public private(set) bool $inFooter = false;

	public private(set) ?LoadingStrategy $strategy = null;

	/** @var array<int, array{code: string, position: InlinePosition}> */
	public private(set) array $inline = [];

	/** Text domain and optional path for wp_set_script_translations(). */
	public private(set) ?string $translationDomain = null;

	public private(set) ?string $translationPath = null;

	/**
	 * @throws InvalidArgumentException When `$handle` is empty.
	 */
	public function __construct( string $handle, private readonly Assets $assets ) {
		$this->handle = self::nonEmpty( $handle, 'handle' );
	}

	/**
	 * @throws InvalidArgumentException When `$url` is empty.
	 */
	public function src( string $url ): self {
		$this->src = self::nonEmpty( $url, 'source URL' );

		return $this;
	}

	/**
	 * @throws InvalidArgumentException When a handle is empty.
	 */
	public function deps( string ...$handles ): self {
		$this->deps = array_values( array_map( static fn ( string $handle ): string => self::nonEmpty( $handle, 'dependency handle' ), $handles ) );

		return $this;
	}

	/**
	 * @param string|null $version Null to add no version to the URL.
	 */
	public function version( ?string $version ): self {
		$this->version = $version;

		return $this;
	}

	public function inFooter( bool $inFooter = true ): self {
		$this->inFooter = $inFooter;

		return $this;
	}

	public function defer(): self {
		$this->strategy = LoadingStrategy::Defer;

		return $this;
	}

	public function async(): self {
		$this->strategy = LoadingStrategy::Async;

		return $this;
	}

	public function inline( string $code, InlinePosition $position = InlinePosition::After ): self {
		$this->inline[] = [ 'code' => $code, 'position' => $position ];

		return $this;
	}

	/**
	 * Makes `$data` available to the script as the global `$name`, JSON-encoded safely for a <script> tag.
	 * Replaces wp_localize_script(), which turns every value into a string.
	 *
	 * @param array<mixed> $data
	 * @throws JsonException When `$data` can't be encoded.
	 */
	public function data( string $name, array $data ): self {
		$json = json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );

		return $this->inline( 'var ' . $name . ' = ' . $json . ';', InlinePosition::Before );
	}

	public function translations( string $domain, ?string $path = null ): self {
		$this->translationDomain = $domain;
		$this->translationPath   = $path;

		return $this;
	}

	public function register(): void {
		$this->assets->registerScript( $this );
	}

	public function enqueue(): void {
		$this->assets->enqueueScript( $this );
	}

	/**
	 * @return non-empty-string
	 */
	private static function nonEmpty( string $value, string $what ): string {
		if ( $value === '' ) {
			throw new InvalidArgumentException( "A script's {$what} can't be empty." );
		}

		return $value;
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Assets;

use InvalidArgumentException;
use Merkushin\Wpal\Api\Assets;

/**
 * A stylesheet to register or enqueue. Configure it, then call register() or enqueue().
 *
 *     $wp->assets()->style( 'my-app' )->src( $url )->media( 'print' )->enqueue();
 */
final class Style {
	/** @var non-empty-string */
	public readonly string $handle;

	/** @var non-empty-string|null */
	public private(set) ?string $src = null;

	/** @var list<non-empty-string> */
	public private(set) array $deps = [];

	/** WordPress's version when false (its default), none when null. */
	public private(set) string|false|null $version = false;

	public private(set) string $media = 'all';

	/** @var string[] */
	public private(set) array $inline = [];

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

	/**
	 * @param string $media A media type or query, e.g. `print` or `(max-width: 640px)`.
	 */
	public function media( string $media ): self {
		$this->media = $media;

		return $this;
	}

	public function inline( string $css ): self {
		$this->inline[] = $css;

		return $this;
	}

	public function register(): void {
		$this->assets->registerStyle( $this );
	}

	public function enqueue(): void {
		$this->assets->enqueueStyle( $this );
	}

	/**
	 * @return non-empty-string
	 */
	private static function nonEmpty( string $value, string $what ): string {
		if ( $value === '' ) {
			throw new InvalidArgumentException( "A stylesheet's {$what} can't be empty." );
		}

		return $value;
	}
}

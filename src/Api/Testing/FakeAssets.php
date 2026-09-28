<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use Merkushin\Wpal\Api\Assets;
use Merkushin\Wpal\Api\Assets\Script;
use Merkushin\Wpal\Api\Assets\Style;

/**
 * Records scripts and styles instead of printing them, for tests.
 */
final class FakeAssets implements Assets {
	/** @var array<string, Script> Registered scripts by handle. */
	public private(set) array $scripts = [];

	/** @var array<string, Style> Registered styles by handle. */
	public private(set) array $styles = [];

	/** @var array<string, true> */
	private array $enqueuedScripts = [];

	/** @var array<string, true> */
	private array $enqueuedStyles = [];

	public function script( string $handle ): Script {
		return new Script( $handle, $this );
	}

	public function style( string $handle ): Style {
		return new Style( $handle, $this );
	}

	public function registerScript( Script $script ): void {
		$this->scripts[ $script->handle ] ??= $script;
	}

	public function enqueueScript( Script $script ): void {
		if ( $script->src !== null ) {
			$this->registerScript( $script );
		}
		$this->enqueuedScripts[ $script->handle ] = true;
	}

	public function registerStyle( Style $style ): void {
		$this->styles[ $style->handle ] ??= $style;
	}

	public function enqueueStyle( Style $style ): void {
		if ( $style->src !== null ) {
			$this->registerStyle( $style );
		}
		$this->enqueuedStyles[ $style->handle ] = true;
	}

	public function isScriptEnqueued( string $handle ): bool {
		return isset( $this->enqueuedScripts[ $handle ] );
	}

	public function isStyleEnqueued( string $handle ): bool {
		return isset( $this->enqueuedStyles[ $handle ] );
	}

	public function dequeueScript( string $handle ): void {
		unset( $this->enqueuedScripts[ $handle ] );
	}

	public function dequeueStyle( string $handle ): void {
		unset( $this->enqueuedStyles[ $handle ] );
	}
}

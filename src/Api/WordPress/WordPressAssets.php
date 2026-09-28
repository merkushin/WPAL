<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use Merkushin\Wpal\Api\Assets;
use Merkushin\Wpal\Api\Assets\Script;
use Merkushin\Wpal\Api\Assets\Style;
use Merkushin\Wpal\Service\Assets as AssetsService;

final class WordPressAssets implements Assets {
	public function __construct( private readonly AssetsService $assets ) {
	}

	public function script( string $handle ): Script {
		return new Script( $handle, $this );
	}

	public function style( string $handle ): Style {
		return new Style( $handle, $this );
	}

	public function registerScript( Script $script ): void {
		$this->assets->wp_register_script( $script->handle, $script->src ?? false, $script->deps, $script->version, $this->scriptArgs( $script ) );
		$this->addScriptExtras( $script );
	}

	public function enqueueScript( Script $script ): void {
		if ( $script->src !== null ) {
			$this->registerScript( $script );
		} else {
			$this->addScriptExtras( $script );
		}
		$this->assets->wp_enqueue_script( $script->handle );
	}

	public function registerStyle( Style $style ): void {
		$this->assets->wp_register_style( $style->handle, $style->src ?? false, $style->deps, $style->version, $style->media );
		$this->addStyleExtras( $style );
	}

	public function enqueueStyle( Style $style ): void {
		if ( $style->src !== null ) {
			$this->registerStyle( $style );
		} else {
			$this->addStyleExtras( $style );
		}
		$this->assets->wp_enqueue_style( $style->handle );
	}

	public function isScriptEnqueued( string $handle ): bool {
		return (bool) $this->assets->wp_script_is( $handle, 'enqueued' );
	}

	public function isStyleEnqueued( string $handle ): bool {
		return (bool) $this->assets->wp_style_is( $handle, 'enqueued' );
	}

	public function dequeueScript( string $handle ): void {
		$this->assets->wp_dequeue_script( $handle );
	}

	public function dequeueStyle( string $handle ): void {
		$this->assets->wp_dequeue_style( $handle );
	}

	/**
	 * @return array{in_footer: bool, strategy?: 'defer'|'async'}
	 */
	private function scriptArgs( Script $script ): array {
		$args = [ 'in_footer' => $script->inFooter ];
		if ( $script->strategy !== null ) {
			$args['strategy'] = $script->strategy->value;
		}

		return $args;
	}

	private function addScriptExtras( Script $script ): void {
		foreach ( $script->inline as $inline ) {
			$this->assets->wp_add_inline_script( $script->handle, $inline['code'], $inline['position']->value );
		}
		if ( $script->translationDomain !== null ) {
			$this->assets->wp_set_script_translations( $script->handle, $script->translationDomain, $script->translationPath ?? '' );
		}
	}

	private function addStyleExtras( Style $style ): void {
		foreach ( $style->inline as $css ) {
			$this->assets->wp_add_inline_style( $style->handle, $css );
		}
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Merkushin\Wpal\Api\Assets\Script;
use Merkushin\Wpal\Api\Assets\Style;

/**
 * Scripts and stylesheets.
 */
interface Assets {
	/**
	 * Starts describing a script; finish with register() or enqueue().
	 */
	#[\NoDiscard]
	public function script( string $handle ): Script;

	/**
	 * Starts describing a stylesheet; finish with register() or enqueue().
	 */
	#[\NoDiscard]
	public function style( string $handle ): Style;

	public function registerScript( Script $script ): void;

	/**
	 * Registers the script if it has a source, then enqueues it.
	 */
	public function enqueueScript( Script $script ): void;

	public function registerStyle( Style $style ): void;

	/**
	 * Registers the stylesheet if it has a source, then enqueues it.
	 */
	public function enqueueStyle( Style $style ): void;

	public function isScriptEnqueued( string $handle ): bool;

	public function isStyleEnqueued( string $handle ): bool;

	public function dequeueScript( string $handle ): void;

	public function dequeueStyle( string $handle ): void;
}

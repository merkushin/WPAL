<?php declare( strict_types=1 );

namespace Merkushin\Wpal;

use Merkushin\Wpal\Api\Assets;
use Merkushin\Wpal\Api\Hooks;
use Merkushin\Wpal\Api\Options;
use Merkushin\Wpal\Api\Posts;
use Merkushin\Wpal\Api\WordPress\WordPressAssets;
use Merkushin\Wpal\Api\WordPress\WordPressHooks;
use Merkushin\Wpal\Api\WordPress\WordPressOptions;
use Merkushin\Wpal\Api\WordPress\WordPressPosts;
use RuntimeException;

/**
 * Entry point to WPAL's Api layer (PHP 8.4+).
 *
 *     $wp = new Wpal();
 *     $wp->hooks()->onAction( 'init', fn () => ... );
 *
 * In tests, pass fakes for the services you use, e.g. `new Wpal( options: new FakeOptions() )`. Services you don't
 * pass are built on ServiceFactory, so `ServiceFactory::set_custom_*()` doubles apply to them too.
 *
 * This file stays PHP 7.4-compatible so older PHP gets a clear error instead of a parse error.
 */
final class Wpal {
	public const MINIMUM_PHP_VERSION_ID = 80400;

	/** @var Hooks|null */
	private $hooks;

	/** @var Options|null */
	private $options;

	/** @var Assets|null */
	private $assets;

	/** @var Posts|null */
	private $posts;

	public function __construct( ?Hooks $hooks = null, ?Options $options = null, ?Assets $assets = null, ?Posts $posts = null ) {
		if ( PHP_VERSION_ID < self::MINIMUM_PHP_VERSION_ID ) {
			throw new RuntimeException(
				'WPAL\'s Api layer needs PHP 8.4 or later; this is PHP ' . PHP_VERSION . '. On older PHP, use Merkushin\\Wpal\\ServiceFactory.'
			);
		}

		$this->hooks   = $hooks;
		$this->options = $options;
		$this->assets  = $assets;
		$this->posts   = $posts;
	}

	public function hooks(): Hooks {
		if ( $this->hooks === null ) {
			$this->hooks = new WordPressHooks( ServiceFactory::create_hooks() );
		}

		return $this->hooks;
	}

	public function options(): Options {
		if ( $this->options === null ) {
			$this->options = new WordPressOptions( ServiceFactory::create_options() );
		}

		return $this->options;
	}

	public function assets(): Assets {
		if ( $this->assets === null ) {
			$this->assets = new WordPressAssets( ServiceFactory::create_assets() );
		}

		return $this->assets;
	}

	public function posts(): Posts {
		if ( $this->posts === null ) {
			$this->posts = new WordPressPosts( ServiceFactory::create_posts() );
		}

		return $this->posts;
	}
}

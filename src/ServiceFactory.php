<?php declare( strict_types=1 );

namespace Merkushin\Wpal;

use Merkushin\Wpal\Service\Assets;
use Merkushin\Wpal\Service\Capabilities;
use Merkushin\Wpal\Service\Comments;
use Merkushin\Wpal\Service\Hooks;
use Merkushin\Wpal\Service\Localization;
use Merkushin\Wpal\Service\Plugins;
use Merkushin\Wpal\Service\PostAttachments;
use Merkushin\Wpal\Service\PostMeta;
use Merkushin\Wpal\Service\Posts;
use Merkushin\Wpal\Service\PostStatuses;
use Merkushin\Wpal\Service\PostTypes;
use Merkushin\Wpal\Service\Screen;
use Merkushin\Wpal\Service\Taxonomies;
use Merkushin\Wpal\Service\Transient;
use Merkushin\Wpal\Service\WpAssets;
use Merkushin\Wpal\Service\WpCapabilities;
use Merkushin\Wpal\Service\WpComments;
use Merkushin\Wpal\Service\WpHooks;
use Merkushin\Wpal\Service\WpLocalization;
use Merkushin\Wpal\Service\WpPlugins;
use Merkushin\Wpal\Service\WpPostAttachments;
use Merkushin\Wpal\Service\WpPostMeta;
use Merkushin\Wpal\Service\WpPosts;
use Merkushin\Wpal\Service\WpPostStatuses;
use Merkushin\Wpal\Service\WpPostTypes;
use Merkushin\Wpal\Service\WpScreen;
use Merkushin\Wpal\Service\WpTaxonomies;
use Merkushin\Wpal\Service\WpTransient;

class ServiceFactory {
	/**
	 * @var Hooks|null
	 */
	private static $custom_hooks;

	/**
	 * @var Localization|null
	 */
	private static $custom_localization;

	/**
	 * @var Plugins|null
	 */
	private static $custom_plugins;

	/**
	 * @var PostAttachments|null
	 */
	private static $custom_post_attachments;

	/**
	 * @var PostMeta|null
	 */
	private static $custom_post_meta;

	/**
	 * @var Posts|null
	 */
	private static $custom_posts;

	/**
	 * @var PostStatuses|null
	 */
	private static $custom_post_statuses;

	/**
	 * @var PostTypes|null
	 */
	private static $custom_post_types;

	/**
	 * @var Taxonomies|null
	 */
	private static $custom_taxonomies;

	/**
	 * @var Comments|null
	 */
	private static $custom_comments;

	/**
	 * @var Capabilities|null
	 */
	private static $custom_capabilities;

	/**
	 * @var Assets|null
	 */
	private static $custom_assets;

	/**
	 * @var Screen|null
	 */
	private static $custom_screen;

	/**
	 * @var Transient|null
	 */
	private static $custom_transient;

	public static function set_custom_hooks( ?Hooks $hooks ): void {
		self::$custom_hooks = $hooks;
	}

	public static function create_hooks(): Hooks {
		if ( self::$custom_hooks ) {
			return self::$custom_hooks;
		}

		return new WpHooks();
	}

	public static function set_custom_localization( ?Localization $custom_localization ): void {
		self::$custom_localization = $custom_localization;
	}

	public static function create_localization(): Localization {
		if ( self::$custom_localization ) {
			return self::$custom_localization;
		}

		return new WpLocalization();
	}

	public static function set_custom_plugins( ?Plugins $custom_plugins ): void {
		self::$custom_plugins = $custom_plugins;
	}

	public static function create_plugins(): Plugins {
		if ( self::$custom_plugins ) {
			return self::$custom_plugins;
		}

		return new WpPlugins();
	}

	public static function set_custom_post_attachments( ?PostAttachments $custom_post_attachments ): void {
		self::$custom_post_attachments = $custom_post_attachments;
	}

	public static function create_post_attachments(): PostAttachments {
		if ( self::$custom_post_attachments ) {
			return self::$custom_post_attachments;
		}

		return new WpPostAttachments();
	}

	public static function set_custom_post_meta( ?PostMeta $custom_post_meta ): void {
		self::$custom_post_meta = $custom_post_meta;
	}

	public static function create_post_meta(): PostMeta {
		if ( self::$custom_post_meta ) {
			return self::$custom_post_meta;
		}

		return new WpPostMeta();
	}

	public static function set_custom_posts( ?Posts $custom_posts ): void {
		self::$custom_posts = $custom_posts;
	}

	public static function create_posts(): Posts {
		if ( self::$custom_posts ) {
			return self::$custom_posts;
		}

		return new WpPosts();
	}

	public static function set_custom_post_statuses( ?PostStatuses $custom_post_statuses ): void {
		self::$custom_post_statuses = $custom_post_statuses;
	}

	public static function create_post_statuses(): PostStatuses {
		if ( self::$custom_post_statuses ) {
			return self::$custom_post_statuses;
		}

		return new WpPostStatuses();
	}

	public static function set_custom_post_types( ?PostTypes $custom_post_types ): void {
		self::$custom_post_types = $custom_post_types;
	}

	public static function create_post_types(): PostTypes {
		if ( self::$custom_post_types ) {
			return self::$custom_post_types;
		}

		return new WpPostTypes();
	}

	public static function set_custom_taxonomies( ?Taxonomies $custom_taxonomies ): void {
		self::$custom_taxonomies = $custom_taxonomies;
	}

	public static function create_taxonomies(): Taxonomies {
		if ( self::$custom_taxonomies ) {
			return self::$custom_taxonomies;
		}

		return new WpTaxonomies();
	}

	public static function set_custom_comments( ?Comments $custom_comments ): void {
		self::$custom_comments = $custom_comments;
	}

	public static function create_comments(): Comments {
		if ( self::$custom_comments ) {
			return self::$custom_comments;
		}

		return new WpComments();
	}

	public static function set_custom_capabilities( ?Capabilities $custom_capabilities ): void {
		self::$custom_capabilities = $custom_capabilities;
	}

	public static function create_capabilities(): Capabilities {
		if ( self::$custom_capabilities ) {
			return self::$custom_capabilities;
		}

		return new WpCapabilities();
	}

	public static function set_custom_assets( ?Assets $custom_assets ): void {
		self::$custom_assets = $custom_assets;
	}

	public static function create_assets(): Assets {
		if ( self::$custom_assets ) {
			return self::$custom_assets;
		}

		return new WpAssets();
	}

	public static function set_custom_screen( ?Screen $custom_screen ): void {
		self::$custom_screen = $custom_screen;
	}

	public static function create_screen(): Screen {
		if ( self::$custom_screen ) {
			return self::$custom_screen;
		}

		return new WpScreen();
	}

	public static function set_custom_transient( ?Transient $custom_transient ): void {
		self::$custom_transient = $custom_transient;
	}

	public static function create_transient(): Transient {
		if ( self::$custom_transient ) {
			return self::$custom_transient;
		}

		return new WpTransient();
	}
}

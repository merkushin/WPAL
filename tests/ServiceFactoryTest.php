<?php declare(strict_types=1);

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
use Merkushin\Wpal\ServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\ServiceFactory
 */
class ServiceFactoryTest extends TestCase
{
	public function testCreateHooks_WhenCalled_ReturnsWpHooks(): void
	{
		$actual = ServiceFactory::create_hooks();

		self::assertInstanceOf( WpHooks::class, $actual);
	}

	public function testCreateHooks_WhenCustomHooksSet_ReturnsCustomHooks(): void
	{
		$custom_hooks = $this->createMock( Hooks::class );
		ServiceFactory::set_custom_hooks( $custom_hooks );

		$actual = ServiceFactory::create_hooks();

		self::assertSame( $custom_hooks, $actual );
	}

	public function testCreateLocalization_WhenCalled_ReturnsWpLocalization(): void
	{
		$actual = ServiceFactory::create_localization();

		self::assertInstanceOf( WpLocalization::class, $actual);
	}

	public function testCreateLocalization_WhenCustomLocalizationSet_ReturnsCustomLocalization(): void
	{
		$custom_localization = $this->createMock( Localization::class );
		ServiceFactory::set_custom_localization( $custom_localization );

		$actual = ServiceFactory::create_localization();

		self::assertSame( $custom_localization, $actual );
	}

	public function testCreatePlugins_WhenCalled_ReturnsWpPlugins(): void
	{
		$actual = ServiceFactory::create_plugins();

		self::assertInstanceOf( WpPlugins::class, $actual);
	}

	public function testCreatePlugins_WhenCustomPluginsSet_ReturnsCustomPlugins(): void
	{
		$custom_plugins = $this->createMock( Plugins::class );
		ServiceFactory::set_custom_plugins( $custom_plugins );

		$actual = ServiceFactory::create_plugins();

		self::assertSame( $custom_plugins, $actual );
	}

	public function testCreatePostAttachments_WhenCalled_ReturnsWpPostAttachments(): void
	{
		$actual = ServiceFactory::create_post_attachments();

		self::assertInstanceOf( WpPostAttachments::class, $actual);
	}

	public function testCreatePostAttachments_WhenCustomPostAttachmentsSet_ReturnsCustomPostAttachments(): void
	{
		$custom_post_attachments = $this->createMock( PostAttachments::class );
		ServiceFactory::set_custom_post_attachments( $custom_post_attachments );

		$actual = ServiceFactory::create_post_attachments();

		self::assertSame( $custom_post_attachments, $actual );
	}

	public function testCreatePostMeta_WhenCalled_ReturnsWpPostMeta(): void
	{
		$actual = ServiceFactory::create_post_meta();

		self::assertInstanceOf( WpPostMeta::class, $actual);
	}

	public function testCreatePostMeta_WhenCustomPostMetaSet_ReturnsCustomPostMeta(): void
	{
		$custom_post_meta = $this->createMock( PostMeta::class );
		ServiceFactory::set_custom_post_meta( $custom_post_meta );

		$actual = ServiceFactory::create_post_meta();

		self::assertSame( $custom_post_meta, $actual );
	}

	public function testCreatePosts_WhenCalled_ReturnsWpPosts(): void
	{
		$actual = ServiceFactory::create_posts();

		self::assertInstanceOf( WpPosts::class, $actual);
	}

	public function testCreatePosts_WhenCustomPostsSet_ReturnsCustomPosts(): void
	{
		$custom_posts = $this->createMock( Posts::class );
		ServiceFactory::set_custom_posts( $custom_posts );

		$actual = ServiceFactory::create_posts();

		self::assertSame( $custom_posts, $actual );
	}

	public function testCreatePostStatuses_WhenCalled_ReturnsWpPostStatuses(): void
	{
		$actual = ServiceFactory::create_post_statuses();

		self::assertInstanceOf( WpPostStatuses::class, $actual);
	}

	public function testCreatePostStatuses_WhenCustomPostStatusesSet_ReturnsCustomPostStatuses(): void
	{
		$custom_post_statuses = $this->createMock( PostStatuses::class );
		ServiceFactory::set_custom_post_statuses( $custom_post_statuses );

		$actual = ServiceFactory::create_post_statuses();

		self::assertSame( $custom_post_statuses, $actual );
	}

	public function testCreatePostTypes_WhenCalled_ReturnsWpPostTypes(): void
	{
		$actual = ServiceFactory::create_post_types();

		self::assertInstanceOf( WpPostTypes::class, $actual);
	}

	public function testCreatePostTypes_WhenCustomPostTypesSet_ReturnsCustomPostTypes(): void
	{
		$custom_post_types = $this->createMock( PostTypes::class );
		ServiceFactory::set_custom_post_types( $custom_post_types );

		$actual = ServiceFactory::create_post_types();

		self::assertSame( $custom_post_types, $actual );
	}

	public function testCreateTaxonomies_WhenCalled_ReturnsWpTaxonomies(): void
	{
		$actual = ServiceFactory::create_taxonomies();

		self::assertInstanceOf( WpTaxonomies::class, $actual);
	}

	public function testCreateTaxonomies_WhenCustomTaxonomiesSet_ReturnsCustomTaxonomies(): void
	{
		$custom_taxonomies = $this->createMock( Taxonomies::class );
		ServiceFactory::set_custom_taxonomies( $custom_taxonomies );

		$actual = ServiceFactory::create_taxonomies();

		self::assertSame( $custom_taxonomies, $actual );
	}

	public function testCreateComments_WhenCalled_ReturnsWpComments(): void
	{
		$actual = ServiceFactory::create_comments();

		self::assertInstanceOf( WpComments::class, $actual);
	}

	public function testCreateComments_WhenCustomCommentsSet_ReturnsCustomComments(): void
	{
		$custom_comments = $this->createMock( Comments::class );
		ServiceFactory::set_custom_comments( $custom_comments );

		$actual = ServiceFactory::create_comments();

		self::assertSame( $custom_comments, $actual );
	}

	public function testCreateCapabilities_WhenCalled_ReturnsWpCapabilities(): void
	{
		$actual = ServiceFactory::create_capabilities();

		self::assertInstanceOf( WpCapabilities::class, $actual);
	}

	public function testCreateCapabilities_WhenCustomCapabilitiesSet_ReturnsCustomCapabilities(): void
	{
		$custom_capabilities = $this->createMock( Capabilities::class );
		ServiceFactory::set_custom_capabilities( $custom_capabilities );

		$actual = ServiceFactory::create_capabilities();

		self::assertSame( $custom_capabilities, $actual );
	}

	public function testCreateAssets_WhenCalled_ReturnsWpAssets(): void
	{
		$actual = ServiceFactory::create_assets();

		self::assertInstanceOf( WpAssets::class, $actual);
	}

	public function testCreateAssets_WhenCustomAssetsSet_ReturnsCustomAssets(): void
	{
		$custom_assets = $this->createMock( Assets::class );
		ServiceFactory::set_custom_assets( $custom_assets );

		$actual = ServiceFactory::create_assets();

		self::assertSame( $custom_assets, $actual );
	}

	public function testCreateScreen_WhenCalled_ReturnsWpScreen(): void
	{
		$actual = ServiceFactory::create_screen();

		self::assertInstanceOf( WpScreen::class, $actual);
	}

	public function testCreateScreen_WhenCustomScreenSet_ReturnsCustomScreen(): void
	{
		$custom_screen = $this->createMock( Screen::class );
		ServiceFactory::set_custom_screen( $custom_screen );

		$actual = ServiceFactory::create_screen();

		self::assertSame( $custom_screen, $actual );
	}

	public function testCreateTransient_WhenCalled_ReturnsWpTransient(): void
	{
		$actual = ServiceFactory::create_transient();

		self::assertInstanceOf( WpTransient::class, $actual);
	}

	public function testCreateTransient_WhenCustomTransientSet_ReturnsCustomTransient(): void
	{
		$custom_transient = $this->createMock( Transient::class );
		ServiceFactory::set_custom_transient( $custom_transient );

		$actual = ServiceFactory::create_transient();

		self::assertSame( $custom_transient, $actual );
	}
}

<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use DateTimeImmutable;
use Merkushin\Wpal\Api\Exception\PostNotFound;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Api\Posts\Order;
use Merkushin\Wpal\Api\Posts\Post;
use Merkushin\Wpal\Api\Posts\SortBy;
use Merkushin\Wpal\Api\Testing\FakePosts;
use Merkushin\Wpal\Api\WordPress\WordPressPosts;
use Merkushin\Wpal\Service\Posts as PostsService;
use Merkushin\Wpal\Tests\Api\Stubs\Stubs;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressPosts
 * @covers \Merkushin\Wpal\Api\Testing\FakePosts
 * @covers \Merkushin\Wpal\Api\Posts\Post
 * @covers \Merkushin\Wpal\Api\Posts\PostQuery
 * @covers \Merkushin\Wpal\Api\Exception\WordPressError
 * @covers \Merkushin\Wpal\Api\Exception\PostNotFound
 */
class PostsTest extends TestCase
{
	public static function setUpBeforeClass(): void
	{
		Stubs::load( 'WP_Error' );
	}

	public function testFind_WhenPostExists_MapsItToPost(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->method( 'get_post' )->with( 5 )->willReturn( self::wpPost( 5 ) );

		$post = ( new WordPressPosts( $service ) )->find( 5 );

		self::assertNotNull( $post );
		self::assertSame( [ 5, 'page', 'publish', 'About', 3, 2 ], [ $post->id, $post->type, $post->status, $post->title, $post->authorId, $post->parentId ] );
		self::assertEquals( new DateTimeImmutable( '2026-01-02 03:04:05', new \DateTimeZone( 'UTC' ) ), $post->date );
		self::assertTrue( $post->isPublished() );
	}

	public function testGet_WhenPostMissing_ThrowsPostNotFound(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->method( 'get_post' )->willReturn( null );

		$this->expectException( PostNotFound::class );

		( new WordPressPosts( $service ) )->get( 99 );
	}

	public function testQuery_WhenRun_TranslatesCriteriaToGetPostsArguments(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->expects( self::once() )->method( 'get_posts' )->with(
			[
				'post_type'      => [ 'page' ],
				'post_status'    => [ 'publish', 'draft' ],
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				'offset'         => 0,
				'post_parent'    => 0,
				's'              => 'hello',
			]
		)->willReturn( [ self::wpPost( 1 ) ] );

		$posts = ( new WordPressPosts( $service ) )->query()
			->type( 'page' )
			->status( 'publish', 'draft' )
			->parent( 0 )
			->search( 'hello' )
			->sortBy( SortBy::Title, Order::Asc )
			->limit( null )
			->get();

		self::assertSame( [ 1 ], array_map( static fn ( Post $p ): int => $p->id, $posts ) );
	}

	public function testCreate_WhenWordPressReturnsError_ThrowsWordPressError(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->method( 'wp_insert_post' )->willReturn( Stubs::error( 'invalid_post_type', 'Invalid post type.' ) );

		try {
			( new WordPressPosts( $service ) )->create( title: 'x', type: 'nope' );
			self::fail( 'Expected WordPressError.' );
		} catch ( WordPressError $e ) {
			self::assertSame( 'invalid_post_type', $e->errorCode );
			self::assertSame( 'Invalid post type.', $e->getMessage() );
		}
	}

	public function testCreate_WhenSaved_SendsOnlyGivenFieldsAndReturnsPost(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->expects( self::once() )->method( 'wp_insert_post' )->with(
			[
				'post_title'   => 'Hello',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_excerpt' => '',
				'menu_order'   => 0,
				'meta_input'   => [ 'color' => 'red' ],
				'post_type'    => 'page',
			],
			true
		)->willReturn( 5 );
		$service->method( 'get_post' )->willReturn( self::wpPost( 5 ) );

		$post = ( new WordPressPosts( $service ) )->create( title: 'Hello', type: 'page', status: 'publish', meta: [ 'color' => 'red' ] );

		self::assertSame( 5, $post->id );
	}

	public function testUpdate_WhenPostMissing_ThrowsBeforeCallingWordPress(): void
	{
		$service = $this->createMock( PostsService::class );
		$service->method( 'get_post' )->willReturn( null );
		$service->expects( self::never() )->method( 'wp_update_post' );

		$this->expectException( PostNotFound::class );

		( new WordPressPosts( $service ) )->update( 5, title: 'New' );
	}

	public function testFake_WhenQueried_FiltersSortsAndLimits(): void
	{
		$posts = new FakePosts();
		$posts->create( title: 'Banana', status: 'publish' );
		$posts->create( title: 'apple', status: 'publish' );
		$posts->create( title: 'Cherry', status: 'draft' );
		$posts->create( title: 'About', type: 'page', status: 'publish' );

		$titles = array_map(
			static fn ( Post $p ): string => $p->title,
			$posts->query()->status( 'publish', 'draft' )->sortBy( SortBy::Title, Order::Asc )->limit( 2 )->get()
		);

		self::assertSame( [ 'apple', 'Banana' ], $titles );
		self::assertSame( 'About', $posts->query()->type( 'page' )->first()?->title );
		self::assertCount( 2, $posts->query()->get() );
	}

	public function testFake_WhenUpdatedTrashedAndDeleted_ChangesStore(): void
	{
		$posts = new FakePosts();
		$post  = $posts->create( title: 'Draft', meta: [ 'a' => 1 ] );

		$updated = $posts->update( $post->id, title: 'Final', status: 'publish', meta: [ 'b' => 2 ] );
		$posts->trash( $post->id );

		self::assertSame( [ 'Final', 'publish' ], [ $updated->title, $updated->status ] );
		self::assertNotNull( $updated->date );
		self::assertSame( [ 'b' => 2, 'a' => 1 ], $posts->meta[ $post->id ] );
		self::assertSame( 'trash', $posts->get( $post->id )->status );

		$posts->delete( $post->id );

		self::assertNull( $posts->find( $post->id ) );
	}

	private static function wpPost( int $id ): stdClass
	{
		$post                    = new stdClass();
		$post->ID                = $id;
		$post->post_type         = 'page';
		$post->post_status       = 'publish';
		$post->post_title        = 'About';
		$post->post_content      = 'Text';
		$post->post_excerpt      = '';
		$post->post_name         = 'about';
		$post->post_author       = '3';
		$post->post_parent       = 2;
		$post->menu_order        = 0;
		$post->post_date_gmt     = '2026-01-02 03:04:05';
		$post->post_modified_gmt = '0000-00-00 00:00:00';
		$post->post_mime_type    = '';

		return $post;
	}
}

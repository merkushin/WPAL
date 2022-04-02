<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Posts {
	/**
	 * Retrieve all children of the post parent ID.
	 *
	 * Normally, without any enhancements, the children would apply to pages. In the
	 * context of the inner workings of WordPress, pages, posts, and attachments
	 * share the same table, so therefore the functionality could apply to any one
	 * of them. It is then noted that while this function does not work on posts, it
	 * does not mean that it won't work on posts. It is recommended that you know
	 * what context you wish to retrieve the children of.
	 *
	 * Attachments may also be made the child of a post, so if that is an accurate
	 * statement (which needs to be verified), it would then be possible to get
	 * all of the attachments for a post. Attachments have since changed since
	 * version 2.5, so this is most likely inaccurate, but serves generally as an
	 * example of what is possible.
	 *
	 * The arguments listed as defaults are for this function and also of the
	 * get_posts() function. The arguments are combined with the get_children defaults
	 * and are then passed to the get_posts() function, which accepts additional arguments.
	 * You can replace the defaults in this function, listed below and the additional
	 * arguments listed in the get_posts() function.
	 *
	 * The 'post_parent' is the most important argument and important attention
	 * needs to be paid to the $args parameter. If you pass either an object or an
	 * integer (number), then just the 'post_parent' is grabbed and everything else
	 * is lost. If you don't specify any arguments, then it is assumed that you are
	 * in The Loop and the post parent will be grabbed for from the current post.
	 *
	 * The 'post_parent' argument is the ID to get the children. The 'numberposts'
	 * is the amount of posts to retrieve that has a default of '-1', which is
	 * used to get all of the posts. Giving a number higher than 0 will only
	 * retrieve that amount of posts.
	 *
	 * The 'post_type' and 'post_status' arguments can be used to choose what
	 * criteria of posts to retrieve. The 'post_type' can be anything, but WordPress
	 * post types are 'post', 'pages', and 'attachments'. The 'post_status'
	 * argument will accept any post status within the write administration panels.
	 *
	 * @param mixed    $args   Optional. User defined arguments for replacing the defaults. Default empty.
	 * @param string   $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                         correspond to a WP_Post object, an associative array, or a numeric array,
	 *                         respectively. Default OBJECT.
	 *
	 * @return WP_Post[]|int[] Array of post objects or post IDs.
	 * @global WP_Post $post   Global post object.
	 *
	 * @since 2.0.0
	 *
	 * @see   get_posts()
	 * @todo  Check validity of description.
	 *
	 */
	function get_children( $args = '', string $output = 'OBJECT' );

	/**
	 * Get extended entry info (<!--more-->).
	 *
	 * There should not be any space after the second dash and before the word
	 * 'more'. There can be text or space(s) after the word 'more', but won't be
	 * referenced.
	 *
	 * The returned array has 'main', 'extended', and 'more_text' keys. Main has the text before
	 * the `<!--more-->`. The 'extended' key has the content after the
	 * `<!--more-->` comment. The 'more_text' key has the custom "Read More" text.
	 *
	 * @param string $post      Post content.
	 *
	 * @return string[] {
	 *     Extended entry info.
	 *
	 * @type string  $main      Content before the more tag.
	 * @type string  $extended  Content after the more tag.
	 * @type string  $more_text Custom read more text, or empty string.
	 * }
	 * @since 1.0.0
	 *
	 */
	function get_extended( $post );

	/**
	 * Retrieves post data given a post ID or post object.
	 *
	 * See sanitize_post() for optional $filter values. Also, the parameter
	 * `$post`, must be given as a variable, since it is passed by reference.
	 *
	 * @param int|WP_Post|null $post   Optional. Post ID or post object. `null`, `false`, `0` and other PHP falsey
	 *                                 values return the current global post inside the loop. A numerically valid post
	 *                                 ID that points to a non-existent post returns `null`. Defaults to global $post.
	 * @param string           $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                                 correspond to a WP_Post object, an associative array, or a numeric array,
	 *                                 respectively. Default OBJECT.
	 * @param string           $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db',
	 *                                 or 'display'. Default 'raw'.
	 *
	 * @return WP_Post|array|null Type corresponding to $output on success or null on failure.
	 *                            When $output is OBJECT, a `WP_Post` instance is returned.
	 * @since 1.5.1
	 *
	 * @global WP_Post         $post   Global post object.
	 *
	 */
	function get_post( $post = null, $output = OBJECT, $filter = 'raw' );

	/**
	 * Retrieves the IDs of the ancestors of a post.
	 *
	 * @param int|WP_Post $post Post ID or post object.
	 *
	 * @return int[] Array of ancestor IDs or empty array if there are none.
	 * @since 2.5.0
	 *
	 */
	function get_post_ancestors( $post );

	/**
	 * Retrieve data from a post field based on Post ID.
	 *
	 * Examples of the post field will be, 'post_type', 'post_status', 'post_content',
	 * etc and based off of the post object property or key names.
	 *
	 * The context values are based off of the taxonomy filter functions and
	 * supported values are found within those functions.
	 *
	 * @param string      $field   Post field name.
	 * @param int|WP_Post $post    Optional. Post ID or post object. Defaults to global $post.
	 * @param string      $context Optional. How to filter the field. Accepts 'raw', 'edit', 'db',
	 *                             or 'display'. Default 'display'.
	 *
	 * @return string The value of the post field on success, empty string on failure.
	 * @since 4.5.0 The `$post` parameter was made optional.
	 *
	 * @see   sanitize_post_field()
	 *
	 * @since 2.3.0
	 */
	function get_post_field( $field, $post = null, $context = 'display' );

	/**
	 * Retrieve the mime type of an attachment based on the ID.
	 *
	 * This function can be used with any post type, but it makes more sense with
	 * attachments.
	 *
	 * @param int|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
	 *
	 * @return string|false The mime type on success, false on failure.
	 * @since 2.0.0
	 *
	 */
	function get_post_mime_type( $post = null );

	/**
	 * Determine whether a post is publicly viewable.
	 *
	 * Posts are considered publicly viewable if both the post status and post type
	 * are viewable.
	 *
	 * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
	 *
	 * @return bool Whether the post is publicly viewable.
	 * @since 5.7.0
	 *
	 */
	function is_post_publicly_viewable( $post = null );

	/**
	 * Retrieves an array of the latest posts, or posts matching the given criteria.
	 *
	 * For more information on the accepted arguments, see the
	 * {@link https://developer.wordpress.org/reference/classes/wp_query/
	 * WP_Query} documentation in the Developer Handbook.
	 *
	 * The `$ignore_sticky_posts` and `$no_found_rows` arguments are ignored by
	 * this function and both are set to `true`.
	 *
	 * The defaults are as follows:
	 *
	 * @param array     $args             {
	 *                                    Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all
	 *                                    available arguments.
	 *
	 * @type int        $numberposts      Total number of posts to retrieve. Is an alias of `$posts_per_page`
	 *                                        in WP_Query. Accepts -1 for all. Default 5.
	 * @type int|string $category         Category ID or comma-separated list of IDs (this or any children).
	 *                                        Is an alias of `$cat` in WP_Query. Default 0.
	 * @type int[]      $include          An array of post IDs to retrieve, sticky posts will be included.
	 *                                        Is an alias of `$post__in` in WP_Query. Default empty array.
	 * @type int[]      $exclude          An array of post IDs not to retrieve. Default empty array.
	 * @type bool       $suppress_filters Whether to suppress filters. Default true.
	 * }
	 * @return WP_Post[]|int[] Array of post objects or post IDs.
	 * @see   WP_Query::parse_query()
	 *
	 * @since 1.2.0
	 *
	 * @see   WP_Query
	 */
	function get_posts( $args = null );

	/**
	 * Determines whether a post is sticky.
	 *
	 * Sticky posts should remain at the top of The Loop. If the post ID is not
	 * given, then The Loop ID for the current post will be used.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param int $post_id Optional. Post ID. Default is the ID of the global `$post`.
	 *
	 * @return bool Whether post is sticky.
	 * @since 2.7.0
	 *
	 */
	function is_sticky( $post_id = 0 );

	/**
	 * Sanitizes every post field.
	 *
	 * If the context is 'raw', then the post object or array will get minimal
	 * sanitization of the integer fields.
	 *
	 * @param object|WP_Post|array $post    The post object or array
	 * @param string               $context Optional. How to sanitize post fields.
	 *                                      Accepts 'raw', 'edit', 'db', 'display',
	 *                                      'attribute', or 'js'. Default 'display'.
	 *
	 * @return object|WP_Post|array The now sanitized post object or array (will be the
	 *                              same type as `$post`).
	 * @see   sanitize_post_field()
	 *
	 * @since 2.3.0
	 *
	 */
	function sanitize_post( $post, $context = 'display' );

	/**
	 * Sanitizes a post field based on context.
	 *
	 * Possible context values are:  'raw', 'edit', 'db', 'display', 'attribute' and
	 * 'js'. The 'display' context is used by default. 'attribute' and 'js' contexts
	 * are treated like 'display' when calling filters.
	 *
	 * @param string $field   The Post Object field name.
	 * @param mixed  $value   The Post Object value.
	 * @param int    $post_id Post ID.
	 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit',
	 *                        'db', 'display', 'attribute' and 'js'. Default 'display'.
	 *
	 * @return mixed Sanitized value.
	 * @since 4.4.0 Like `sanitize_post()`, `$context` defaults to 'display'.
	 *
	 * @since 2.3.0
	 */
	function sanitize_post_field( $field, $value, $post_id, $context = 'display' );

	/**
	 * Make a post sticky.
	 *
	 * Sticky posts should be displayed at the top of the front page.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 2.7.0
	 *
	 */
	function stick_post( $post_id );

	/**
	 * Un-stick a post.
	 *
	 * Sticky posts should be displayed at the top of the front page.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 2.7.0
	 *
	 */
	function unstick_post( $post_id );

	/**
	 * Count number of posts of a post type and if user has permissions to view.
	 *
	 * This function provides an efficient method of finding the amount of post's
	 * type a blog has. Another method is to count the amount of items in
	 * get_posts(), but that method has a lot of overhead with doing so. Therefore,
	 * when developing for 2.5+, use this function instead.
	 *
	 * The $perm parameter checks for 'readable' value and if the user can read
	 * private posts, it will display that for the user that is signed in.
	 *
	 * @param string $type Optional. Post type to retrieve count. Default 'post'.
	 * @param string $perm Optional. 'readable' or empty. Default empty.
	 *
	 * @return stdClass Number of posts for each status.
	 * @global wpdb  $wpdb WordPress database abstraction object.
	 *
	 * @since 2.5.0
	 *
	 */
	function wp_count_posts( $type = 'post', $perm = '' );

	/**
	 * Get default post mime types.
	 *
	 * @return array List of post mime types.
	 * @since 5.3.0 Added the 'Documents', 'Spreadsheets', and 'Archives' mime type groups.
	 *
	 * @since 2.9.0
	 */
	function get_post_mime_types();

	/**
	 * Check a MIME-Type against a list.
	 *
	 * If the wildcard_mime_types parameter is a string, it must be comma separated
	 * list. If the real_mime_types is a string, it is also comma separated to
	 * create the list.
	 *
	 * @param string|string[] $wildcard_mime_types Mime types, e.g. audio/mpeg or image (same as image/*)
	 *                                             or flash (same as *flash*).
	 * @param string|string[] $real_mime_types     Real post mime type values.
	 *
	 * @return array array(wildcard=>array(real types)).
	 * @since 2.5.0
	 *
	 */
	function wp_match_mime_types( $wildcard_mime_types, $real_mime_types );

	/**
	 * Convert MIME types into SQL.
	 *
	 * @param string|string[] $post_mime_types List of mime types or comma separated string
	 *                                         of mime types.
	 * @param string          $table_alias     Optional. Specify a table alias, if needed.
	 *                                         Default empty.
	 *
	 * @return string The SQL AND clause for mime searching.
	 * @since 2.5.0
	 *
	 */
	function wp_post_mime_type_where( $post_mime_types, $table_alias = '' );

	/**
	 * Trash or delete a post or page.
	 *
	 * When the post and page is permanently deleted, everything that is tied to
	 * it is deleted also. This includes comments, post meta fields, and terms
	 * associated with the post.
	 *
	 * The post or page is moved to Trash instead of permanently deleted unless
	 * Trash is disabled, item is already in the Trash, or $force_delete is true.
	 *
	 * @param int   $postid       Optional. Post ID. Default 0.
	 * @param bool  $force_delete Optional. Whether to bypass Trash and force deletion.
	 *                            Default false.
	 *
	 * @return WP_Post|false|null Post data on success, false or null on failure.
	 * @see   wp_trash_post()
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb         WordPress database abstraction object.
	 * @see   wp_delete_attachment()
	 */
	function wp_delete_post( $postid = 0, $force_delete = false );

	/**
	 * Move a post or page to the Trash
	 *
	 * If Trash is disabled, the post or page is permanently deleted.
	 *
	 * @param int $post_id Optional. Post ID. Default is the ID of the global `$post`
	 *                     if `EMPTY_TRASH_DAYS` equals true.
	 *
	 * @return WP_Post|false|null Post data on success, false or null on failure.
	 * @since 2.9.0
	 *
	 * @see   wp_delete_post()
	 *
	 */
	function wp_trash_post( $post_id = 0 );

	/**
	 * Restores a post from the Trash.
	 *
	 * @param int $post_id Optional. Post ID. Default is the ID of the global `$post`.
	 *
	 * @return WP_Post|false|null Post data on success, false or null on failure.
	 * @since 2.9.0
	 * @since 5.6.0 An untrashed post is now returned to 'draft' status by default, except for
	 *              attachments which are returned to their original 'inherit' status.
	 *
	 */
	function wp_untrash_post( $post_id = 0 );

	/**
	 * Moves comments for a post to the Trash.
	 *
	 * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
	 *
	 * @return mixed|void False on failure.
	 * @since 2.9.0
	 *
	 * @global wpdb            $wpdb WordPress database abstraction object.
	 *
	 */
	function wp_trash_post_comments( $post = null );

	/**
	 * Restore comments for a post from the Trash.
	 *
	 * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
	 *
	 * @return true|void
	 * @since 2.9.0
	 *
	 * @global wpdb            $wpdb WordPress database abstraction object.
	 *
	 */
	function wp_untrash_post_comments( $post = null );

	/**
	 * Retrieve the list of categories for a post.
	 *
	 * Compatibility layer for themes and plugins. Also an easy layer of abstraction
	 * away from the complexity of the taxonomy layer.
	 *
	 * @param int   $post_id Optional. The Post ID. Does not default to the ID of the
	 *                       global $post. Default 0.
	 * @param array $args    Optional. Category query parameters. Default empty array.
	 *                       See WP_Term_Query::__construct() for supported arguments.
	 *
	 * @return array|WP_Error List of categories. If the `$fields` argument passed via `$args` is 'all' or
	 *                        'all_with_object_id', an array of WP_Term objects will be returned. If `$fields`
	 *                        is 'ids', an array of category IDs. If `$fields` is 'names', an array of category names.
	 *                        WP_Error object if 'category' taxonomy doesn't exist.
	 * @see   wp_get_object_terms()
	 *
	 * @since 2.1.0
	 *
	 */
	function wp_get_post_categories( $post_id = 0, $args = [] );

	/**
	 * Retrieve the tags for a post.
	 *
	 * There is only one default for this function, called 'fields' and by default
	 * is set to 'all'. There are other defaults that can be overridden in
	 * wp_get_object_terms().
	 *
	 * @param int   $post_id Optional. The Post ID. Does not default to the ID of the
	 *                       global $post. Default 0.
	 * @param array $args    Optional. Tag query parameters. Default empty array.
	 *                       See WP_Term_Query::__construct() for supported arguments.
	 *
	 * @return array|WP_Error Array of WP_Term objects on success or empty array if no tags were found.
	 *                        WP_Error object if 'post_tag' taxonomy doesn't exist.
	 * @since 2.3.0
	 *
	 */
	function wp_get_post_tags( $post_id = 0, $args = [] );

	/**
	 * Retrieves the terms for a post.
	 *
	 * @param int             $post_id  Optional. The Post ID. Does not default to the ID of the
	 *                                  global $post. Default 0.
	 * @param string|string[] $taxonomy Optional. The taxonomy slug or array of slugs for which
	 *                                  to retrieve terms. Default 'post_tag'.
	 * @param array           $args     {
	 *                                  Optional. Term query parameters. See WP_Term_Query::__construct() for supported
	 *                                  arguments.
	 *
	 * @type string           $fields   Term fields to retrieve. Default 'all'.
	 * }
	 * @return array|WP_Error Array of WP_Term objects on success or empty array if no terms were found.
	 *                        WP_Error object if `$taxonomy` doesn't exist.
	 * @since 2.8.0
	 *
	 */
	function wp_get_post_terms( $post_id = 0, $taxonomy = 'post_tag', $args = [] );

	/**
	 * Retrieve a number of recent posts.
	 *
	 * @param array  $args   Optional. Arguments to retrieve posts. Default empty array.
	 * @param string $output Optional. The required return type. One of OBJECT or ARRAY_A, which
	 *                       correspond to a WP_Post object or an associative array, respectively.
	 *                       Default ARRAY_A.
	 *
	 * @return array|false Array of recent posts, where the type of each element is determined
	 *                     by the `$output` parameter. Empty array on failure.
	 * @see   get_posts()
	 *
	 * @since 1.0.0
	 *
	 */
	function wp_get_recent_posts( $args = [], $output = 'ARRAY_A' );

	/**
	 * Insert or update a post.
	 *
	 * If the $postarr parameter has 'ID' set to a value, then post will be updated.
	 *
	 * You can set the post date manually, by setting the values for 'post_date'
	 * and 'post_date_gmt' keys. You can close the comments or open the comments by
	 * setting the value for 'comment_status' key.
	 *
	 * @param array $postarr               {
	 *                                     An array of elements that make up a post to update or insert.
	 *
	 * @type int    $ID                    The post ID. If equal to something other than 0,
	 *                                         the post with that ID will be updated. Default 0.
	 * @type int    $post_author           The ID of the user who added the post. Default is
	 *                                         the current user ID.
	 * @type string $post_date             The date of the post. Default is the current time.
	 * @type string $post_date_gmt         The date of the post in the GMT timezone. Default is
	 *                                         the value of `$post_date`.
	 * @type string $post_content          The post content. Default empty.
	 * @type string $post_content_filtered The filtered post content. Default empty.
	 * @type string $post_title            The post title. Default empty.
	 * @type string $post_excerpt          The post excerpt. Default empty.
	 * @type string $post_status           The post status. Default 'draft'.
	 * @type string $post_type             The post type. Default 'post'.
	 * @type string $comment_status        Whether the post can accept comments. Accepts 'open' or 'closed'.
	 *                                         Default is the value of 'default_comment_status' option.
	 * @type string $ping_status           Whether the post can accept pings. Accepts 'open' or 'closed'.
	 *                                         Default is the value of 'default_ping_status' option.
	 * @type string $post_password         The password to access the post. Default empty.
	 * @type string $post_name             The post name. Default is the sanitized post title
	 *                                         when creating a new post.
	 * @type string $to_ping               Space or carriage return-separated list of URLs to ping.
	 *                                         Default empty.
	 * @type string $pinged                Space or carriage return-separated list of URLs that have
	 *                                         been pinged. Default empty.
	 * @type string $post_modified         The date when the post was last modified. Default is
	 *                                         the current time.
	 * @type string $post_modified_gmt     The date when the post was last modified in the GMT
	 *                                         timezone. Default is the current time.
	 * @type int    $post_parent           Set this for the post it belongs to, if any. Default 0.
	 * @type int    $menu_order            The order the post should be displayed in. Default 0.
	 * @type string $post_mime_type        The mime type of the post. Default empty.
	 * @type string $guid                  Global Unique ID for referencing the post. Default empty.
	 * @type int    $import_id             The post ID to be used when inserting a new post.
	 *                                         If specified, must not match any existing post ID. Default 0.
	 * @type int[]  $post_category         Array of category IDs.
	 *                                         Defaults to value of the 'default_category' option.
	 * @type array  $tags_input            Array of tag names, slugs, or IDs. Default empty.
	 * @type array  $tax_input             An array of taxonomy terms keyed by their taxonomy name.
	 *                                         If the taxonomy is hierarchical, the term list needs to be
	 *                                         either an array of term IDs or a comma-separated string of IDs.
	 *                                         If the taxonomy is non-hierarchical, the term list can be an array
	 *                                         that contains term names or slugs, or a comma-separated string
	 *                                         of names or slugs. This is because, in hierarchical taxonomy,
	 *                                         child terms can have the same names with different parent terms,
	 *                                         so the only way to connect them is using ID. Default empty.
	 * @type array  $meta_input            Array of post meta values keyed by their post meta key. Default empty.
	 * }
	 *
	 * @param bool  $wp_error              Optional. Whether to return a WP_Error on failure. Default false.
	 * @param bool  $fire_after_hooks      Optional. Whether to fire the after insert hooks. Default true.
	 *
	 * @return int|WP_Error The post ID on success. The value 0 or WP_Error on failure.
	 * @since 1.0.0
	 * @since 2.6.0 Added the `$wp_error` parameter to allow a WP_Error to be returned on failure.
	 * @since 4.2.0 Support was added for encoding emoji in the post title, content, and excerpt.
	 * @since 4.4.0 A 'meta_input' array can now be passed to `$postarr` to add post meta data.
	 * @since 5.6.0 Added the `$fire_after_hooks` parameter.
	 *
	 * @see   sanitize_post()
	 * @global wpdb $wpdb                  WordPress database abstraction object.
	 *
	 */
	function wp_insert_post( $postarr, $wp_error = false, $fire_after_hooks = true );

	/**
	 * Update a post with new post data.
	 *
	 * The date does not have to be set for drafts. You can set the date and it will
	 * not be overridden.
	 *
	 * @param array|object $postarr          Optional. Post data. Arrays are expected to be escaped,
	 *                                       objects are not. See wp_insert_post() for accepted arguments.
	 *                                       Default array.
	 * @param bool         $wp_error         Optional. Whether to return a WP_Error on failure. Default false.
	 * @param bool         $fire_after_hooks Optional. Whether to fire the after insert hooks. Default true.
	 *
	 * @return int|WP_Error The post ID on success. The value 0 or WP_Error on failure.
	 * @since 3.5.0 Added the `$wp_error` parameter to allow a WP_Error to be returned on failure.
	 * @since 5.6.0 Added the `$fire_after_hooks` parameter.
	 *
	 * @since 1.0.0
	 */
	function wp_update_post( $postarr = [], $wp_error = false, $fire_after_hooks = true );

	/**
	 * Publish a post by transitioning the post status.
	 *
	 * @param int|WP_Post $post Post ID or post object.
	 *
	 * @global wpdb       $wpdb WordPress database abstraction object.
	 *
	 * @since 2.1.0
	 *
	 */
	function wp_publish_post( $post );

	/**
	 * Publish future post and make sure post ID has future post status.
	 *
	 * Invoked by cron 'publish_future_post' event. This safeguard prevents cron
	 * from publishing drafts, etc.
	 *
	 * @param int|WP_Post $post_id Post ID or post object.
	 *
	 * @since 2.5.0
	 *
	 */
	function check_and_publish_future_post( $post_id );

	/**
	 * Uses wp_checkdate to return a valid Gregorian-calendar value for post_date.
	 * If post_date is not provided, this first checks post_date_gmt if provided,
	 * then falls back to use the current time.
	 *
	 * For back-compat purposes in wp_insert_post, an empty post_date and an invalid
	 * post_date_gmt will continue to return '1970-01-01 00:00:00' rather than false.
	 *
	 * @param string $post_date     The date in mysql format.
	 * @param string $post_date_gmt The GMT date in mysql format.
	 *
	 * @return string|false A valid Gregorian-calendar date string, or false on failure.
	 * @since 5.7.0
	 *
	 */
	function wp_resolve_post_date( $post_date = '', $post_date_gmt = '' );

	/**
	 * Computes a unique slug for the post, when given the desired slug and some post details.
	 *
	 * @param string      $slug        The desired slug (post_name).
	 * @param int         $post_ID     Post ID.
	 * @param string      $post_status No uniqueness checks are made if the post is still draft or pending.
	 * @param string      $post_type   Post type.
	 * @param int         $post_parent Post parent ID.
	 *
	 * @return string Unique slug for the post, based on $post_name (with a -1, -2, etc. suffix)
	 * @since 2.8.0
	 *
	 * @global wpdb       $wpdb        WordPress database abstraction object.
	 * @global WP_Rewrite $wp_rewrite  WordPress rewrite component.
	 *
	 */
	function wp_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent );

	/**
	 * Add tags to a post.
	 *
	 * @param int          $post_id Optional. The Post ID. Does not default to the ID of the global $post.
	 * @param string|array $tags    Optional. An array of tags to set for the post, or a string of tags
	 *                              separated by commas. Default empty.
	 *
	 * @return array|false|WP_Error Array of affected term IDs. WP_Error or false on failure.
	 * @since 2.3.0
	 *
	 * @see   wp_set_post_tags()
	 *
	 */
	function wp_add_post_tags( $post_id = 0, $tags = '' );

	/**
	 * Set the tags for a post.
	 *
	 * @param int          $post_id Optional. The Post ID. Does not default to the ID of the global $post.
	 * @param string|array $tags    Optional. An array of tags to set for the post, or a string of tags
	 *                              separated by commas. Default empty.
	 * @param bool         $append  Optional. If true, don't delete existing tags, just add on. If false,
	 *                              replace the tags with the new tags. Default false.
	 *
	 * @return array|false|WP_Error Array of term taxonomy IDs of affected terms. WP_Error or false on failure.
	 * @since 2.3.0
	 *
	 * @see   wp_set_object_terms()
	 *
	 */
	function wp_set_post_tags( $post_id = 0, $tags = '', $append = false );

	/**
	 * Set the terms for a post.
	 *
	 * @param int          $post_id  Optional. The Post ID. Does not default to the ID of the global $post.
	 * @param string|array $tags     Optional. An array of terms to set for the post, or a string of terms
	 *                               separated by commas. Hierarchical taxonomies must always pass IDs rather
	 *                               than names so that children with the same names but different parents
	 *                               aren't confused. Default empty.
	 * @param string       $taxonomy Optional. Taxonomy name. Default 'post_tag'.
	 * @param bool         $append   Optional. If true, don't delete existing terms, just add on. If false,
	 *                               replace the terms with the new terms. Default false.
	 *
	 * @return array|false|WP_Error Array of term taxonomy IDs of affected terms. WP_Error or false on failure.
	 * @see   wp_set_object_terms()
	 *
	 * @since 2.8.0
	 *
	 */
	function wp_set_post_terms( $post_id = 0, $tags = '', $taxonomy = 'post_tag', $append = false );

	/**
	 * Set categories for a post.
	 *
	 * If no categories are provided, the default category is used.
	 *
	 * @param int       $post_ID         Optional. The Post ID. Does not default to the ID
	 *                                   of the global $post. Default 0.
	 * @param int[]|int $post_categories Optional. List of category IDs, or the ID of a single category.
	 *                                   Default empty array.
	 * @param bool      $append          If true, don't delete existing categories, just add on.
	 *                                   If false, replace the categories with the new categories.
	 *
	 * @return array|false|WP_Error Array of term taxonomy IDs of affected categories. WP_Error or false on failure.
	 * @since 2.1.0
	 *
	 */
	function wp_set_post_categories( $post_ID = 0, $post_categories = [], $append = false );

	/**
	 * Fires actions related to the transitioning of a post's status.
	 *
	 * When a post is saved, the post status is "transitioned" from one status to another,
	 * though this does not always mean the status has actually changed before and after
	 * the save. This function fires a number of action hooks related to that transition:
	 * the generic {@see 'transition_post_status'} action, as well as the dynamic hooks
	 * {@see '$old_status_to_$new_status'} and {@see '$new_status_$post->post_type'}. Note
	 * that the function does not transition the post object in the database.
	 *
	 * For instance: When publishing a post for the first time, the post status may transition
	 * from 'draft' – or some other status – to 'publish'. However, if a post is already
	 * published and is simply being updated, the "old" and "new" statuses may both be 'publish'
	 * before and after the transition.
	 *
	 * @param string  $new_status Transition to this post status.
	 * @param string  $old_status Previous post status.
	 * @param WP_Post $post       Post data.
	 *
	 * @since 2.3.0
	 *
	 */
	function wp_transition_post_status( $new_status, $old_status, $post );

	/**
	 * Fires actions after a post, its terms and meta data has been saved.
	 *
	 * @param int|WP_Post  $post        The post ID or object that has been saved.
	 * @param bool         $update      Whether this is an existing post being updated.
	 * @param null|WP_Post $post_before Null for new posts, the WP_Post object prior
	 *                                  to the update for updated posts.
	 *
	 * @since 5.6.0
	 *
	 */
	function wp_after_insert_post( $post, $update, $post_before );

	/**
	 * Add a URL to those already pinged.
	 *
	 * @param int|WP_Post  $post_id Post object or ID.
	 * @param string|array $uri     Ping URI or array of URIs.
	 *
	 * @return int|false How many rows were updated.
	 * @global wpdb        $wpdb    WordPress database abstraction object.
	 *
	 * @since 1.5.0
	 * @since 4.7.0 `$post_id` can be a WP_Post object.
	 * @since 4.7.0 `$uri` can be an array of URIs.
	 *
	 */
	function add_ping( $post_id, $uri );

	/**
	 * Retrieve enclosures already enclosed for a post.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string[] Array of enclosures for the given post.
	 * @since 1.5.0
	 *
	 */
	function get_enclosed( $post_id );

	/**
	 * Retrieve URLs already pinged for a post.
	 *
	 * @param int|WP_Post $post_id Post ID or object.
	 *
	 * @return string[]|false Array of URLs already pinged for the given post, false if the post is not found.
	 * @since 1.5.0
	 *
	 * @since 4.7.0 `$post_id` can be a WP_Post object.
	 *
	 */
	function get_pung( $post_id );

	/**
	 * Retrieve URLs that need to be pinged.
	 *
	 * @param int|WP_Post $post_id Post Object or ID
	 *
	 * @return string[]|false List of URLs yet to ping.
	 * @since 1.5.0
	 * @since 4.7.0 `$post_id` can be a WP_Post object.
	 *
	 */
	function get_to_ping( $post_id );

	/**
	 * Do trackbacks for a list of URLs.
	 *
	 * @param string $tb_list Comma separated list of URLs.
	 * @param int    $post_id Post ID.
	 *
	 * @since 1.0.0
	 *
	 */
	function trackback_url_list( $tb_list, $post_id );

	/**
	 * Get a list of page IDs.
	 *
	 * @return string[] List of page IDs as strings.
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @since 2.0.0
	 *
	 */
	function get_all_page_ids();

	/**
	 * Retrieves page data given a page ID or page object.
	 *
	 * Use get_post() instead of get_page().
	 *
	 * @param int|WP_Post $page   Page object or page ID. Passed by reference.
	 * @param string      $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                            correspond to a WP_Post object, an associative array, or a numeric array,
	 *                            respectively. Default OBJECT.
	 * @param string      $filter Optional. How the return value should be filtered. Accepts 'raw',
	 *                            'edit', 'db', 'display'. Default 'raw'.
	 *
	 * @return WP_Post|array|null WP_Post or array on success, null on failure.
	 * @since      1.5.1
	 * @deprecated 3.5.0 Use get_post()
	 *
	 */
	function get_page( $page, $output = OBJECT, $filter = 'raw' );

	/**
	 * Retrieves a page given its path.
	 *
	 * @param string       $page_path Page path.
	 * @param string       $output    Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                                correspond to a WP_Post object, an associative array, or a numeric array,
	 *                                respectively. Default OBJECT.
	 * @param string|array $post_type Optional. Post type or array of post types. Default 'page'.
	 *
	 * @return WP_Post|array|null WP_Post (or array) on success, or null on failure.
	 * @since 2.1.0
	 *
	 * @global wpdb        $wpdb      WordPress database abstraction object.
	 *
	 */
	function get_page_by_path( $page_path, $output = OBJECT, $post_type = 'page' );

	/**
	 * Retrieve a page given its title.
	 *
	 * If more than one post uses the same title, the post with the smallest ID will be returned.
	 * Be careful: in case of more than one post having the same title, it will check the oldest
	 * publication date, not the smallest ID.
	 *
	 * Because this function uses the MySQL '=' comparison, $page_title will usually be matched
	 * as case-insensitive with default collation.
	 *
	 * @param string       $page_title Page title.
	 * @param string       $output     Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                                 correspond to a WP_Post object, an associative array, or a numeric array,
	 *                                 respectively. Default OBJECT.
	 * @param string|array $post_type  Optional. Post type or array of post types. Default 'page'.
	 *
	 * @return WP_Post|array|null WP_Post (or array) on success, or null on failure.
	 * @since 3.0.0 The `$post_type` parameter was added.
	 *
	 * @global wpdb        $wpdb       WordPress database abstraction object.
	 *
	 * @since 2.1.0
	 */
	function get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' );

	/**
	 * Identify descendants of a given page ID in a list of page objects.
	 *
	 * Descendants are identified from the `$pages` array passed to the function. No database queries are performed.
	 *
	 * @param int   $page_id Page ID.
	 * @param array $pages   List of page objects from which descendants should be identified.
	 *
	 * @return array List of page children.
	 * @since 1.5.1
	 *
	 */
	function get_page_children( $page_id, $pages );

	/**
	 * Order the pages with children under parents in a flat list.
	 *
	 * It uses auxiliary structure to hold parent-children relationships and
	 * runs in O(N) complexity
	 *
	 * @param WP_Post[] $pages   Posts array (passed by reference).
	 * @param int       $page_id Optional. Parent page ID. Default 0.
	 *
	 * @return string[] Array of post names keyed by ID and arranged by hierarchy. Children immediately follow their
	 *                  parents.
	 * @since 2.0.0
	 *
	 */
	function get_page_hierarchy( &$pages, $page_id = 0 );

	/**
	 * Build the URI path for a page.
	 *
	 * Sub pages will be in the "directory" under the parent page post name.
	 *
	 * @param WP_Post|object|int $page Optional. Page ID or WP_Post object. Default is global $post.
	 *
	 * @return string|false Page URI, false on error.
	 * @since 1.5.0
	 * @since 4.6.0 The `$page` parameter was made optional.
	 *
	 */
	function get_page_uri( $page = 0 );

	/**
	 * Retrieve an array of pages (or hierarchical post type items).
	 *
	 * @param array|string $args         {
	 *                                   Optional. Array or string of arguments to retrieve pages.
	 *
	 * @type int           $child_of     Page ID to return child and grandchild pages of. Note: The value
	 *                                      of `$hierarchical` has no bearing on whether `$child_of` returns
	 *                                      hierarchical results. Default 0, or no restriction.
	 * @type string        $sort_order   How to sort retrieved pages. Accepts 'ASC', 'DESC'. Default 'ASC'.
	 * @type string        $sort_column  What columns to sort pages by, comma-separated. Accepts 'post_author',
	 *                                      'post_date', 'post_title', 'post_name', 'post_modified', 'menu_order',
	 *                                      'post_modified_gmt', 'post_parent', 'ID', 'rand', 'comment_count'.
	 *                                      'post_' can be omitted for any values that start with it.
	 *                                      Default 'post_title'.
	 * @type bool          $hierarchical Whether to return pages hierarchically. If false in conjunction with
	 *                                      `$child_of` also being false, both arguments will be disregarded.
	 *                                      Default true.
	 * @type int[]         $exclude      Array of page IDs to exclude. Default empty array.
	 * @type int[]         $include      Array of page IDs to include. Cannot be used with `$child_of`,
	 *                                      `$parent`, `$exclude`, `$meta_key`, `$meta_value`, or `$hierarchical`.
	 *                                      Default empty array.
	 * @type string        $meta_key     Only include pages with this meta key. Default empty.
	 * @type string        $meta_value   Only include pages with this meta value. Requires `$meta_key`.
	 *                                      Default empty.
	 * @type string        $authors      A comma-separated list of author IDs. Default empty.
	 * @type int           $parent       Page ID to return direct children of. Default -1, or no restriction.
	 * @type string|int[]  $exclude_tree Comma-separated string or array of page IDs to exclude.
	 *                                      Default empty array.
	 * @type int           $number       The number of pages to return. Default 0, or all pages.
	 * @type int           $offset       The number of pages to skip before returning. Requires `$number`.
	 *                                      Default 0.
	 * @type string        $post_type    The post type to query. Default 'page'.
	 * @type string|array  $post_status  A comma-separated list or array of post statuses to include.
	 *                                      Default 'publish'.
	 * }
	 * @return WP_Post[]|int[]|false Array of pages (or hierarchical post type items). Boolean false if the
	 *                               specified post type is not hierarchical or the specified status is not
	 *                               supported by the post type.
	 * @global wpdb        $wpdb         WordPress database abstraction object.
	 *
	 * @since 1.5.0
	 *
	 */
	function get_pages( $args = [] );

	/**
	 * Retrieve the icon for a MIME type or attachment.
	 *
	 * @param string|int $mime MIME type or attachment ID.
	 *
	 * @return string|false Icon, false otherwise.
	 * @since 2.1.0
	 *
	 */
	function wp_mime_type_icon( $mime = 0 );

	/**
	 * Check for changed slugs for published post objects and save the old slug.
	 *
	 * The function is used when a post object of any type is updated,
	 * by comparing the current and previous post objects.
	 *
	 * If the slug was changed and not already part of the old slugs then it will be
	 * added to the post meta field ('_wp_old_slug') for storing old slugs for that
	 * post.
	 *
	 * The most logically usage of this function is redirecting changed post objects, so
	 * that those that linked to an changed post will be redirected to the new post.
	 *
	 * @param int     $post_id     Post ID.
	 * @param WP_Post $post        The Post Object
	 * @param WP_Post $post_before The Previous Post Object
	 *
	 * @since 2.1.0
	 *
	 */
	function wp_check_for_changed_slugs( $post_id, $post, $post_before );

	/**
	 * Check for changed dates for published post objects and save the old date.
	 *
	 * The function is used when a post object of any type is updated,
	 * by comparing the current and previous post objects.
	 *
	 * If the date was changed and not already part of the old dates then it will be
	 * added to the post meta field ('_wp_old_date') for storing old dates for that
	 * post.
	 *
	 * The most logically usage of this function is redirecting changed post objects, so
	 * that those that linked to an changed post will be redirected to the new post.
	 *
	 * @param int     $post_id     Post ID.
	 * @param WP_Post $post        The Post Object
	 * @param WP_Post $post_before The Previous Post Object
	 *
	 * @since 4.9.3
	 *
	 */
	function wp_check_for_changed_dates( $post_id, $post, $post_before );

	/**
	 * Retrieve the private post SQL based on capability.
	 *
	 * This function provides a standardized way to appropriately select on the
	 * post_status of a post type. The function will return a piece of SQL code
	 * that can be added to a WHERE clause; this SQL is constructed to allow all
	 * published posts, and all private posts to which the user has access.
	 *
	 * @param string|array $post_type Single post type or an array of post types. Currently only supports 'post' or
	 *                                'page'.
	 *
	 * @return string SQL code that can be added to a where clause.
	 * @since 2.2.0
	 * @since 4.3.0 Added the ability to pass an array to `$post_type`.
	 *
	 */
	function get_private_posts_cap_sql( $post_type );

	/**
	 * Retrieve the post SQL based on capability, author, and type.
	 *
	 * @param string|string[] $post_type   Single post type or an array of post types.
	 * @param bool            $full        Optional. Returns a full WHERE statement instead of just
	 *                                     an 'andalso' term. Default true.
	 * @param int             $post_author Optional. Query posts having a single author ID. Default null.
	 * @param bool            $public_only Optional. Only return public posts. Skips cap checks for
	 *                                     $current_user.  Default false.
	 *
	 * @return string SQL WHERE code that can be added to a query.
	 * @since 4.3.0 Introduced the ability to pass an array of post types to `$post_type`.
	 *
	 * @see   get_private_posts_cap_sql()
	 * @global wpdb           $wpdb        WordPress database abstraction object.
	 *
	 * @since 3.0.0
	 */
	function get_posts_by_author_sql( $post_type, $full = true, $post_author = null, $public_only = false );

	/**
	 * Retrieves the most recent time that a post on the site was published.
	 *
	 * The server timezone is the default and is the difference between GMT and
	 * server time. The 'blog' value is the date when the last post was posted.
	 * The 'gmt' is when the last post was posted in GMT formatted date.
	 *
	 * @param string $timezone  Optional. The timezone for the timestamp. Accepts 'server', 'blog', or 'gmt'.
	 *                          'server' uses the server's internal timezone.
	 *                          'blog' uses the `post_date` field, which proxies to the timezone set for the site.
	 *                          'gmt' uses the `post_date_gmt` field.
	 *                          Default 'server'.
	 * @param string $post_type Optional. The post type to check. Default 'any'.
	 *
	 * @return string The date of the last post, or false on failure.
	 * @since 4.4.0 The `$post_type` argument was added.
	 *
	 * @since 0.71
	 */
	function get_lastpostdate( $timezone = 'server', $post_type = 'any' );

	/**
	 * Get the most recent time that a post on the site was modified.
	 *
	 * The server timezone is the default and is the difference between GMT and
	 * server time. The 'blog' value is just when the last post was modified.
	 * The 'gmt' is when the last post was modified in GMT time.
	 *
	 * @param string $timezone  Optional. The timezone for the timestamp. See get_lastpostdate()
	 *                          for information on accepted values.
	 *                          Default 'server'.
	 * @param string $post_type Optional. The post type to check. Default 'any'.
	 *
	 * @return string The timestamp in 'Y-m-d H:i:s' format, or false on failure.
	 * @since 4.4.0 The `$post_type` argument was added.
	 *
	 * @since 1.2.0
	 */
	function get_lastpostmodified( $timezone = 'server', $post_type = 'any' );

	/**
	 * Updates posts in cache.
	 *
	 * @param WP_Post[] $posts Array of post objects (passed by reference).
	 *
	 * @since 1.5.1
	 *
	 */
	function update_post_cache( &$posts );

	/**
	 * Will clean the post in the cache.
	 *
	 * Cleaning means delete from the cache of the post. Will call to clean the term
	 * object cache associated with the post ID.
	 *
	 * This function not run if $_wp_suspend_cache_invalidation is not empty. See
	 * wp_suspend_cache_invalidation().
	 *
	 * @param int|WP_Post $post Post ID or post object to remove from the cache.
	 *
	 * @global bool       $_wp_suspend_cache_invalidation
	 *
	 * @since 2.0.0
	 *
	 */
	function clean_post_cache( $post );

	/**
	 * Call major cache updating functions for list of Post objects.
	 *
	 * @param WP_Post[] $posts             Array of Post objects
	 * @param string    $post_type         Optional. Post type. Default 'post'.
	 * @param bool      $update_term_cache Optional. Whether to update the term cache. Default true.
	 * @param bool      $update_meta_cache Optional. Whether to update the meta cache. Default true.
	 *
	 * @since 1.5.0
	 *
	 */
	function update_post_caches( &$posts, $post_type = 'post', $update_term_cache = true, $update_meta_cache = true );

	/**
	 * Returns the ID of the post's parent.
	 *
	 * @param int|WP_Post $post Post ID or post object.
	 *
	 * @return int|false Post parent ID (which can be 0 if there is no parent),
	 *                   or false if the post does not exist.
	 * @since 3.1.0
	 *
	 */
	function wp_get_post_parent_id( $post );

	/**
	 * Check the given subset of the post hierarchy for hierarchy loops.
	 *
	 * Prevents loops from forming and breaks those that it finds. Attached
	 * to the {@see 'wp_insert_post_parent'} filter.
	 *
	 * @param int $post_parent ID of the parent for the post we're checking.
	 * @param int $post_ID     ID of the post we're checking.
	 *
	 * @return int The new post_parent for the post, 0 otherwise.
	 * @see   wp_find_hierarchy_loop()
	 *
	 * @since 3.1.0
	 *
	 */
	function wp_check_post_hierarchy_for_loops( $post_parent, $post_ID );

	/**
	 * Sets the post thumbnail (featured image) for the given post.
	 *
	 * @param int|WP_Post $post         Post ID or post object where thumbnail should be attached.
	 * @param int         $thumbnail_id Thumbnail to attach.
	 *
	 * @return int|bool True on success, false on failure.
	 * @since 3.1.0
	 *
	 */
	function set_post_thumbnail( $post, $thumbnail_id );

	/**
	 * Removes the thumbnail (featured image) from the given post.
	 *
	 * @param int|WP_Post $post Post ID or post object from which the thumbnail should be removed.
	 *
	 * @return bool True on success, false on failure.
	 * @since 3.3.0
	 *
	 */
	function delete_post_thumbnail( $post );

	/**
	 * Delete auto-drafts for new posts that are > 7 days old.
	 *
	 * @since 3.4.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	function wp_delete_auto_drafts();

	/**
	 * Queues posts for lazy-loading of term meta.
	 *
	 * @param array $posts Array of WP_Post objects.
	 *
	 * @since 4.5.0
	 *
	 */
	function wp_queue_posts_for_term_meta_lazyload( $posts );

	/**
	 * Sets the last changed time for the 'posts' cache group.
	 *
	 * @since 5.0.0
	 */
	function wp_cache_set_posts_last_changed();

	/**
	 * Get all available post MIME types for a given post type.
	 *
	 * @param string $type
	 *
	 * @return mixed
	 * @since 2.5.0
	 *
	 * @global wpdb  $wpdb WordPress database abstraction object.
	 *
	 */
	function get_available_post_mime_types( $type = 'attachment' );

	/**
	 * Retrieves the path to an uploaded image file.
	 *
	 * Similar to `get_attached_file()` however some images may have been processed after uploading
	 * to make them suitable for web use. In this case the attached "full" size file is usually replaced
	 * with a scaled down version of the original image. This function always returns the path
	 * to the originally uploaded image file.
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @param bool $unfiltered    Optional. Passed through to `get_attached_file()`. Default false.
	 *
	 * @return string|false Path to the original image file or false if the attachment is not an image.
	 * @since 5.4.0 Added the `$unfiltered` parameter.
	 *
	 * @since 5.3.0
	 */
	function wp_get_original_image_path( $attachment_id, $unfiltered = false );

	/**
	 * Retrieve the URL to an original attachment image.
	 *
	 * Similar to `wp_get_attachment_url()` however some images may have been
	 * processed after uploading. In this case this function returns the URL
	 * to the originally uploaded image file.
	 *
	 * @param int $attachment_id Attachment post ID.
	 *
	 * @return string|false Attachment image URL, false on error or if the attachment is not an image.
	 * @since 5.3.0
	 *
	 */
	function wp_get_original_image_url( $attachment_id );

	/**
	 * Filter callback which sets the status of an untrashed post to its previous status.
	 *
	 * This can be used as a callback on the `wp_untrash_post_status` filter.
	 *
	 * @param string $new_status      The new status of the post being restored.
	 * @param int    $post_id         The ID of the post being restored.
	 * @param string $previous_status The status of the post at the point where it was trashed.
	 *
	 * @return string The new status of the post.
	 * @since 5.6.0
	 *
	 */
	function wp_untrash_post_set_previous_status( $new_status, $post_id, $previous_status );
}


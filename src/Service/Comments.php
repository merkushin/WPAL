<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Comments {
	/**
	 * Check whether a comment passes internal checks to be allowed to add.
	 *
	 * If manual comment moderation is set in the administration, then all checks,
	 * regardless of their type and substance, will fail and the function will
	 * return false.
	 *
	 * If the number of links exceeds the amount in the administration, then the
	 * check fails. If any of the parameter contents contain any disallowed words,
	 * then the check fails.
	 *
	 * If the comment author was approved before, then the comment is automatically
	 * approved.
	 *
	 * If all checks pass, the function will return true.
	 *
	 * @param string $author       Comment author name.
	 * @param string $email        Comment author email.
	 * @param string $url          Comment author URL.
	 * @param string $comment      Content of the comment.
	 * @param string $user_ip      Comment author IP address.
	 * @param string $user_agent   Comment author User-Agent.
	 * @param string $comment_type Comment type, either user-submitted comment,
	 *                             trackback, or pingback.
	 *
	 * @return bool If all checks pass, true, otherwise false.
	 * @since 1.2.0
	 *
	 * @global wpdb  $wpdb         WordPress database abstraction object.
	 *
	 */
	public function check_comment( $author, $email, $url, $comment, $user_ip, $user_agent, $comment_type );

	/**
	 * Retrieve the approved comments for post $post_id.
	 *
	 * @param int   $post_id The ID of the post.
	 * @param array $args    Optional. See WP_Comment_Query::__construct() for information on accepted arguments.
	 *
	 * @return int|array The approved comments, or number of comments if `$count`
	 *                   argument is true.
	 * @since 4.1.0 Refactored to leverage WP_Comment_Query over a direct query.
	 *
	 * @since 2.0.0
	 */
	public function get_approved_comments( $post_id, $args = [] );

	/**
	 * Retrieves comment data given a comment ID or comment object.
	 *
	 * If an object is passed then the comment data will be cached and then returned
	 * after being passed through a filter. If the comment is empty, then the global
	 * comment variable will be used, if it is set.
	 *
	 * @param WP_Comment|string|int $comment Comment to retrieve.
	 * @param string                $output  Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N,
	 *                                       which correspond to a WP_Comment object, an associative array, or a
	 *                                       numeric array, respectively. Default OBJECT.
	 *
	 * @return WP_Comment|array|null Depends on $output value.
	 * @global WP_Comment           $comment Global comment object.
	 *
	 * @since 2.0.0
	 *
	 */
	public function get_comment( $comment = null, $output = 'OBJECT' );

	/**
	 * Retrieve a list of comments.
	 *
	 * The comment list can be for the blog as a whole or for an individual post.
	 *
	 * @param string|array $args Optional. Array or string of arguments. See WP_Comment_Query::__construct()
	 *                           for information on accepted arguments. Default empty.
	 *
	 * @return int|array List of comments or number of found comments if `$count` argument is true.
	 * @since 2.7.0
	 *
	 */
	public function get_comments( $args = '' );

	/**
	 * Retrieve all of the WordPress supported comment statuses.
	 *
	 * Comments have a limited set of valid status values, this provides the comment
	 * status values and descriptions.
	 *
	 * @return string[] List of comment status labels keyed by status.
	 * @since 2.7.0
	 *
	 */
	public function get_comment_statuses();

	/**
	 * Gets the default comment status for a post type.
	 *
	 * @param string $post_type    Optional. Post type. Default 'post'.
	 * @param string $comment_type Optional. Comment type. Default 'comment'.
	 *
	 * @return string Expected return value is 'open' or 'closed'.
	 * @since 4.3.0
	 *
	 */
	public function get_default_comment_status( $post_type = 'post', $comment_type = 'comment' );

	/**
	 * The date the last comment was modified.
	 *
	 * @param string $timezone Which timezone to use in reference to 'gmt', 'blog', or 'server' locations.
	 *
	 * @return string|false Last comment modified date on success, false on failure.
	 * @global wpdb  $wpdb     WordPress database abstraction object.
	 *
	 * @since 1.5.0
	 * @since 4.7.0 Replaced caching the modified date in a local static variable
	 *              with the Object Cache API.
	 *
	 */
	public function get_lastcommentmodified( $timezone = 'server' );

	/**
	 * Retrieves the total comment counts for the whole site or a single post.
	 *
	 * Unlike wp_count_comments(), this function always returns the live comment counts without caching.
	 *
	 * @param int   $post_id             Optional. Restrict the comment counts to the given post. Default 0, which
	 *                                   indicates that comment counts for the whole site will be retrieved.
	 *
	 * @return array() {
	 *     The number of comments keyed by their status.
	 *
	 * @type int    $approved            The number of approved comments.
	 * @type int    $awaiting_moderation The number of comments awaiting moderation (a.k.a. pending).
	 * @type int    $spam                The number of spam comments.
	 * @type int    $trash               The number of trashed comments.
	 * @type int    $post-trashed        The number of comments for posts that are in the trash.
	 * @type int    $total_comments      The total number of non-trashed comments, including spam.
	 * @type int    $all                 The total number of pending or approved comments.
	 * }
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb                WordPress database abstraction object.
	 *
	 */
	public function get_comment_count( $post_id = 0 );

	/**
	 * Add meta data field to a comment.
	 *
	 * @param int    $comment_id Comment ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional. Whether the same key should not be added.
	 *                           Default false.
	 *
	 * @return int|false Meta ID on success, false on failure.
	 * @link  https://developer.wordpress.org/reference/functions/add_comment_meta/
	 *
	 * @since 2.9.0
	 *
	 */
	public function add_comment_meta( $comment_id, $meta_key, $meta_value, $unique = false );

	/**
	 * Remove metadata matching criteria from a comment.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @param int    $comment_id Comment ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. If provided,
	 *                           rows will only be removed that match the value.
	 *                           Must be serializable if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.9.0
	 *
	 * @link  https://developer.wordpress.org/reference/functions/delete_comment_meta/
	 *
	 */
	public function delete_comment_meta( $comment_id, $meta_key, $meta_value = '' );

	/**
	 * Retrieve comment meta field for a comment.
	 *
	 * @param int    $comment_id Comment ID.
	 * @param string $key        Optional. The meta key to retrieve. By default,
	 *                           returns data for all keys.
	 * @param bool   $single     Optional. Whether to return a single value.
	 *                           This parameter has no effect if `$key` is not specified.
	 *                           Default false.
	 *
	 * @return mixed An array of values if `$single` is false.
	 *               The value of meta data field if `$single` is true.
	 *               False for an invalid `$comment_id` (non-numeric, zero, or negative value).
	 *               An empty string if a valid but non-existing comment ID is passed.
	 * @since 2.9.0
	 *
	 * @link  https://developer.wordpress.org/reference/functions/get_comment_meta/
	 *
	 */
	public function get_comment_meta( $comment_id, $key = '', $single = false );

	/**
	 * Update comment meta field based on comment ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and comment ID.
	 *
	 * If the meta field for the comment does not exist, it will be added.
	 *
	 * @param int    $comment_id Comment ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before updating.
	 *                           If specified, only update existing metadata entries with
	 *                           this value. Otherwise, update all entries. Default empty.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 * @link  https://developer.wordpress.org/reference/functions/update_comment_meta/
	 *
	 * @since 2.9.0
	 *
	 */
	public function update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' );

	/**
	 * Queues comments for metadata lazy-loading.
	 *
	 * @param WP_Comment[] $comments Array of comment objects.
	 *
	 * @since 4.5.0
	 *
	 */
	public function wp_queue_comments_for_comment_meta_lazyload( $comments );

	/**
	 * Sets the cookies used to store an unauthenticated commentator's identity. Typically used
	 * to recall previous comments by this commentator that are still held in moderation.
	 *
	 * @param WP_Comment $comment         Comment object.
	 * @param WP_User    $user            Comment author's user object. The user may not exist.
	 * @param bool       $cookies_consent Optional. Comment author's consent to store cookies. Default true.
	 *
	 * @since 4.9.6 The `$cookies_consent` parameter was added.
	 *
	 * @since 3.4.0
	 */
	public function wp_set_comment_cookies( $comment, $user, $cookies_consent = true );

	/**
	 * Sanitizes the cookies sent to the user already.
	 *
	 * Will only do anything if the cookies have already been created for the user.
	 * Mostly used after cookies had been sent to use elsewhere.
	 *
	 * @since 2.0.4
	 */
	public function sanitize_comment_cookies();

	/**
	 * Validates whether this comment is allowed to be made.
	 *
	 * @param array $commentdata Contains information on the comment.
	 * @param bool  $wp_error    When true, a disallowed comment will result in the function
	 *                           returning a WP_Error object, rather than executing wp_die().
	 *                           Default false.
	 *
	 * @return int|string|WP_Error Allowed comments return the approval status (0|1|'spam'|'trash').
	 *                             If `$wp_error` is true, disallowed comments return a WP_Error.
	 * @global wpdb $wpdb        WordPress database abstraction object.
	 *
	 * @since 2.0.0
	 * @since 4.7.0 The `$avoid_die` parameter was added, allowing the function
	 *              to return a WP_Error object instead of dying.
	 * @since 5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
	 *
	 */
	public function wp_allow_comment( $commentdata, $wp_error = false );

	/**
	 * Hooks WP's native database-based comment-flood check.
	 *
	 * This wrapper maintains backward compatibility with plugins that expect to
	 * be able to unhook the legacy check_comment_flood_db() function from
	 * 'check_comment_flood' using remove_action().
	 *
	 * @since 2.3.0
	 * @since 4.7.0 Converted to be an add_filter() wrapper.
	 */
	public function check_comment_flood_db();

	/**
	 * Checks whether comment flooding is occurring.
	 *
	 * Won't run, if current user can manage options, so to not block
	 * administrators.
	 *
	 * @param bool   $is_flood  Is a comment flooding occurring?
	 * @param string $ip        Comment author's IP address.
	 * @param string $email     Comment author's email address.
	 * @param string $date      MySQL time string.
	 * @param bool   $avoid_die When true, a disallowed comment will result in the function
	 *                          returning without executing wp_die() or die(). Default false.
	 *
	 * @return bool Whether comment flooding is occurring.
	 * @since 4.7.0
	 *
	 * @global wpdb  $wpdb      WordPress database abstraction object.
	 *
	 */
	public function wp_check_comment_flood( $is_flood, $ip, $email, $date, $avoid_die = false );

	/**
	 * Separates an array of comments into an array keyed by comment_type.
	 *
	 * @param WP_Comment[] $comments Array of comments
	 *
	 * @return WP_Comment[] Array of comments keyed by comment_type.
	 * @since 2.7.0
	 *
	 */
	public function separate_comments( &$comments );

	/**
	 * Calculate the total number of comment pages.
	 *
	 * @param WP_Comment[] $comments Optional. Array of WP_Comment objects. Defaults to `$wp_query->comments`.
	 * @param int          $per_page Optional. Comments per page.
	 * @param bool         $threaded Optional. Control over flat or threaded comments.
	 *
	 * @return int Number of comment pages.
	 * @uses  Walker_Comment
	 *
	 * @global WP_Query    $wp_query WordPress Query object.
	 *
	 * @since 2.7.0
	 *
	 */
	public function get_comment_pages_count( $comments = null, $per_page = null, $threaded = null );

	/**
	 * Calculate what page number a comment will appear on for comment paging.
	 *
	 * @param int       $comment_ID Comment ID.
	 * @param array     $args       {
	 *                              Array of optional arguments.
	 *
	 * @type string     $type       Limit paginated comments to those matching a given type.
	 *                                 Accepts 'comment', 'trackback', 'pingback', 'pings'
	 *                                 (trackbacks and pingbacks), or 'all'. Default 'all'.
	 * @type int        $per_page   Per-page count to use when calculating pagination.
	 *                                 Defaults to the value of the 'comments_per_page' option.
	 * @type int|string $max_depth  If greater than 1, comment page will be determined
	 *                                 for the top-level parent `$comment_ID`.
	 *                                 Defaults to the value of the 'thread_comments_depth' option.
	 * } *
	 * @return int|null Comment page number or null on error.
	 * @global wpdb     $wpdb       WordPress database abstraction object.
	 *
	 * @since 2.7.0
	 *
	 */
	public function get_page_of_comment( $comment_ID, $args = [] );

	/**
	 * Retrieves the maximum character lengths for the comment form fields.
	 *
	 * @return int[] Array of maximum lengths keyed by field name.
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @since 4.5.0
	 *
	 */
	public function wp_get_comment_fields_max_lengths();

	/**
	 * Compares the lengths of comment data against the maximum character limits.
	 *
	 * @param array $comment_data Array of arguments for inserting a comment.
	 *
	 * @return WP_Error|true WP_Error when a comment field exceeds the limit,
	 *                       otherwise true.
	 * @since 4.7.0
	 *
	 */
	public function wp_check_comment_data_max_lengths( $comment_data );

	/**
	 * Checks if a comment contains disallowed characters or words.
	 *
	 * @param string $author     The author of the comment
	 * @param string $email      The email of the comment
	 * @param string $url        The url used in the comment
	 * @param string $comment    The comment content
	 * @param string $user_ip    The comment author's IP address
	 * @param string $user_agent The author's browser user agent
	 *
	 * @return bool True if comment contains disallowed content, false if comment does not
	 * @since 5.5.0
	 *
	 */
	public function wp_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent );

	/**
	 * Retrieves the total comment counts for the whole site or a single post.
	 *
	 * The comment stats are cached and then retrieved, if they already exist in the
	 * cache.
	 *
	 * @param int $post_id        Optional. Restrict the comment counts to the given post. Default 0, which indicates
	 *                            that comment counts for the whole site will be retrieved.
	 *
	 * @return stdClass {
	 *     The number of comments keyed by their status.
	 *
	 * @type int  $approved       The number of approved comments.
	 * @type int  $moderated      The number of comments awaiting moderation (a.k.a. pending).
	 * @type int  $spam           The number of spam comments.
	 * @type int  $trash          The number of trashed comments.
	 * @type int  $post-trashed   The number of comments for posts that are in the trash.
	 * @type int  $total_comments The total number of non-trashed comments, including spam.
	 * @type int  $all            The total number of pending or approved comments.
	 * }
	 * @see   get_comment_count() Which handles fetching the live comment counts.
	 *
	 * @since 2.5.0
	 *
	 */
	public function wp_count_comments( $post_id = 0 );

	/**
	 * Trashes or deletes a comment.
	 *
	 * The comment is moved to Trash instead of permanently deleted unless Trash is
	 * disabled, item is already in the Trash, or $force_delete is true.
	 *
	 * The post comment count will be updated if the comment was approved and has a
	 * post ID available.
	 *
	 * @param int|WP_Comment $comment_id   Comment ID or WP_Comment object.
	 * @param bool           $force_delete Whether to bypass Trash and force deletion. Default false.
	 *
	 * @return bool True on success, false on failure.
	 * @global wpdb          $wpdb         WordPress database abstraction object.
	 *
	 * @since 2.0.0
	 *
	 */
	public function wp_delete_comment( $comment_id, $force_delete = false );

	/**
	 * Moves a comment to the Trash
	 *
	 * If Trash is disabled, comment is permanently deleted.
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.9.0
	 *
	 */
	public function wp_trash_comment( $comment_id );

	/**
	 * Removes a comment from the Trash
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.9.0
	 *
	 */
	public function wp_untrash_comment( $comment_id );

	/**
	 * Marks a comment as Spam
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.9.0
	 *
	 */
	public function wp_spam_comment( $comment_id );

	/**
	 * Removes a comment from the Spam
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.9.0
	 *
	 */
	public function wp_unspam_comment( $comment_id );

	/**
	 * The status of a comment by ID.
	 *
	 * @param int|WP_Comment $comment_id Comment ID or WP_Comment object
	 *
	 * @return string|false Status might be 'trash', 'approved', 'unapproved', 'spam'. False on failure.
	 * @since 1.0.0
	 *
	 */
	public function wp_get_comment_status( $comment_id );

	/**
	 * Call hooks for when a comment status transition occurs.
	 *
	 * Calls hooks for comment status transitions. If the new comment status is not the same
	 * as the previous comment status, then two hooks will be ran, the first is
	 * {@see 'transition_comment_status'} with new status, old status, and comment data.
	 * The next action called is {@see 'comment_$old_status_to_$new_status'}. It has
	 * the comment data.
	 *
	 * The final action will run whether or not the comment statuses are the same.
	 * The action is named {@see 'comment_$new_status_$comment->comment_type'}.
	 *
	 * @param string     $new_status New comment status.
	 * @param string     $old_status Previous comment status.
	 * @param WP_Comment $comment    Comment object.
	 *
	 * @since 2.7.0
	 *
	 */
	public function wp_transition_comment_status( $new_status, $old_status, $comment );

	/**
	 * Get current commenter's name, email, and URL.
	 *
	 * Expects cookies content to already be sanitized. User of this function might
	 * wish to recheck the returned array for validity.
	 *
	 * @return array {
	 *     An array of current commenter variables.
	 *
	 * @type string $comment_author       The name of the current commenter, or an empty string.
	 * @type string $comment_author_email The email address of the current commenter, or an empty string.
	 * @type string $comment_author_url   The URL address of the current commenter, or an empty string.
	 * }
	 * @since 2.0.4
	 *
	 * @see   sanitize_comment_cookies() Use to sanitize cookies
	 *
	 */
	public function wp_get_current_commenter();

	/**
	 * Get unapproved comment author's email.
	 *
	 * Used to allow the commenter to see their pending comment.
	 *
	 * @return string The unapproved comment author's email (when supplied).
	 * @since       5.7.0 The window within which the author email for an unapproved comment
	 *              can be retrieved was extended to 10 minutes.
	 *
	 * @since       5.1.0
	 */
	public function wp_get_unapproved_comment_author_email();

	/**
	 * Inserts a comment into the database.
	 *
	 * @param array     $commentdata          {
	 *                                        Array of arguments for inserting a new comment.
	 *
	 * @type string     $comment_agent        The HTTP user agent of the `$comment_author` when
	 *                                            the comment was submitted. Default empty.
	 * @type int|string $comment_approved     Whether the comment has been approved. Default 1.
	 * @type string     $comment_author       The name of the author of the comment. Default empty.
	 * @type string     $comment_author_email The email address of the `$comment_author`. Default empty.
	 * @type string     $comment_author_IP    The IP address of the `$comment_author`. Default empty.
	 * @type string     $comment_author_url   The URL address of the `$comment_author`. Default empty.
	 * @type string     $comment_content      The content of the comment. Default empty.
	 * @type string     $comment_date         The date the comment was submitted. To set the date
	 *                                            manually, `$comment_date_gmt` must also be specified.
	 *                                            Default is the current time.
	 * @type string     $comment_date_gmt     The date the comment was submitted in the GMT timezone.
	 *                                            Default is `$comment_date` in the site's GMT timezone.
	 * @type int        $comment_karma        The karma of the comment. Default 0.
	 * @type int        $comment_parent       ID of this comment's parent, if any. Default 0.
	 * @type int        $comment_post_ID      ID of the post that relates to the comment, if any.
	 *                                            Default 0.
	 * @type string     $comment_type         Comment type. Default 'comment'.
	 * @type array      $comment_meta         Optional. Array of key/value pairs to be stored in commentmeta for the
	 *                                            new comment.
	 * @type int        $user_id              ID of the user who submitted the comment. Default 0.
	 * }
	 * @return int|false The new comment's ID on success, false on failure.
	 * @since 5.5.0 Default value for `$comment_type` argument changed to `comment`.
	 *
	 * @global wpdb     $wpdb                 WordPress database abstraction object.
	 *
	 * @since 2.0.0
	 * @since 4.4.0 Introduced the `$comment_meta` argument.
	 */
	public function wp_insert_comment( $commentdata );

	/**
	 * Filters and sanitizes comment data.
	 *
	 * Sets the comment data 'filtered' field to true when finished. This can be
	 * checked as to whether the comment should be filtered and to keep from
	 * filtering the same comment more than once.
	 *
	 * @param array $commentdata Contains information on the comment.
	 *
	 * @return array Parsed comment information.
	 * @since 2.0.0
	 *
	 */
	public function wp_filter_comment( $commentdata );

	/**
	 * Whether a comment should be blocked because of comment flood.
	 *
	 * @param bool $block            Whether plugin has already blocked comment.
	 * @param int  $time_lastcomment Timestamp for last comment.
	 * @param int  $time_newcomment  Timestamp for new comment.
	 *
	 * @return bool Whether comment should be blocked.
	 * @since 2.1.0
	 *
	 */
	public function wp_throttle_comment_flood( $block, $time_lastcomment, $time_newcomment );

	/**
	 * Adds a new comment to the database.
	 *
	 * Filters new comment to ensure that the fields are sanitized and valid before
	 * inserting comment into database. Calls {@see 'comment_post'} action with comment ID
	 * and whether comment is approved by WordPress. Also has {@see 'preprocess_comment'}
	 * filter for processing the comment data before the function handles it.
	 *
	 * We use `REMOTE_ADDR` here directly. If you are behind a proxy, you should ensure
	 * that it is properly set, such as in wp-config.php, for your environment.
	 *
	 * See {@link https://core.trac.wordpress.org/ticket/9235}
	 *
	 * @param array $commentdata          {
	 *                                    Comment data.
	 *
	 * @type string $comment_author       The name of the comment author.
	 * @type string $comment_author_email The comment author email address.
	 * @type string $comment_author_url   The comment author URL.
	 * @type string $comment_content      The content of the comment.
	 * @type string $comment_date         The date the comment was submitted. Default is the current time.
	 * @type string $comment_date_gmt     The date the comment was submitted in the GMT timezone.
	 *                                        Default is `$comment_date` in the GMT timezone.
	 * @type string $comment_type         Comment type. Default 'comment'.
	 * @type int    $comment_parent       The ID of this comment's parent, if any. Default 0.
	 * @type int    $comment_post_ID      The ID of the post that relates to the comment.
	 * @type int    $user_id              The ID of the user who submitted the comment. Default 0.
	 * @type int    $user_ID              Kept for backward-compatibility. Use `$user_id` instead.
	 * @type string $comment_agent        Comment author user agent. Default is the value of 'HTTP_USER_AGENT'
	 *                                        in the `$_SERVER` superglobal sent in the original request.
	 * @type string $comment_author_IP    Comment author IP address in IPv4 format. Default is the value of
	 *                                        'REMOTE_ADDR' in the `$_SERVER` superglobal sent in the original request.
	 * }
	 *
	 * @param bool  $wp_error             Should errors be returned as WP_Error objects instead of
	 *                                    executing wp_die()? Default false.
	 *
	 * @return int|false|WP_Error The ID of the comment on success, false or WP_Error on failure.
	 * @since       1.5.0
	 * @since       4.3.0 Introduced the `comment_agent` and `comment_author_IP` arguments.
	 * @since       4.7.0 The `$avoid_die` parameter was added, allowing the function
	 *              to return a WP_Error object instead of dying.
	 * @since       5.5.0 The `$avoid_die` parameter was renamed to `$wp_error`.
	 * @since       5.5.0 Introduced the `comment_type` argument.
	 *
	 * @see         wp_insert_comment()
	 * @global wpdb $wpdb                 WordPress database abstraction object.
	 *
	 */
	public function wp_new_comment( $commentdata, $wp_error = false );

	/**
	 * Send a comment moderation notification to the comment moderator.
	 *
	 * @param int $comment_ID ID of the comment.
	 *
	 * @return bool True on success, false on failure.
	 * @since 4.4.0
	 *
	 */
	public function wp_new_comment_notify_moderator( $comment_ID );

	/**
	 * Send a notification of a new comment to the post author.
	 *
	 * @param int $comment_ID Comment ID.
	 *
	 * @return bool True on success, false on failure.
	 * @since 4.4.0
	 *
	 * Uses the {@see 'notify_post_author'} filter to determine whether the post author
	 * should be notified when a new comment is added, overriding site setting.
	 *
	 */
	public function wp_new_comment_notify_postauthor( $comment_ID );

	/**
	 * Sets the status of a comment.
	 *
	 * The {@see 'wp_set_comment_status'} action is called after the comment is handled.
	 * If the comment status is not in the list, then false is returned.
	 *
	 * @param int|WP_Comment $comment_id     Comment ID or WP_Comment object.
	 * @param string         $comment_status New comment status, either 'hold', 'approve', 'spam', or 'trash'.
	 * @param bool           $wp_error       Whether to return a WP_Error object if there is a failure. Default false.
	 *
	 * @return bool|WP_Error True on success, false or WP_Error on failure.
	 * @since 1.0.0
	 *
	 * @global wpdb          $wpdb           WordPress database abstraction object.
	 *
	 */
	public function wp_set_comment_status( $comment_id, $comment_status, $wp_error = false );

	/**
	 * Updates an existing comment in the database.
	 *
	 * Filters the comment and makes sure certain fields are valid before updating.
	 *
	 * @param array $commentarr Contains information on the comment.
	 * @param bool  $wp_error   Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @return int|false|WP_Error The value 1 if the comment was updated, 0 if not updated.
	 *                            False or a WP_Error object on failure.
	 * @since       5.5.0 The return values for an invalid comment or post ID
	 *              were changed to false instead of 0.
	 *
	 * @global wpdb $wpdb       WordPress database abstraction object.
	 *
	 * @since       2.0.0
	 * @since       4.9.0 Add updating comment meta during comment update.
	 * @since       5.5.0 The `$wp_error` parameter was added.
	 */
	public function wp_update_comment( $commentarr, $wp_error = false );

	/**
	 * Whether to defer comment counting.
	 *
	 * When setting $defer to true, all post comment counts will not be updated
	 * until $defer is set to false. When $defer is set to false, then all
	 * previously deferred updated post comment counts will then be automatically
	 * updated without having to call wp_update_comment_count() after.
	 *
	 * @param bool $defer
	 *
	 * @return bool
	 * @since 2.5.0
	 *
	 */
	public function wp_defer_comment_counting( $defer = null );

	/**
	 * Updates the comment count for post(s).
	 *
	 * When $do_deferred is false (is by default) and the comments have been set to
	 * be deferred, the post_id will be added to a queue, which will be updated at a
	 * later date and only updated once per post ID.
	 *
	 * If the comments have not be set up to be deferred, then the post will be
	 * updated. When $do_deferred is set to true, then all previous deferred post
	 * IDs will be updated along with the current $post_id.
	 *
	 * @param int|null $post_id     Post ID.
	 * @param bool     $do_deferred Optional. Whether to process previously deferred
	 *                              post comment counts. Default false.
	 *
	 * @return bool|void True on success, false on failure or if post with ID does
	 *                   not exist.
	 * @see   wp_update_comment_count_now() For what could cause a false return value
	 *
	 * @since 2.1.0
	 *
	 */
	public function wp_update_comment_count( $post_id, $do_deferred = false );

	/**
	 * Updates the comment count for the post.
	 *
	 * @param int   $post_id Post ID
	 *
	 * @return bool True on success, false if the post does not exist.
	 * @since 2.5.0
	 *
	 * @global wpdb $wpdb    WordPress database abstraction object.
	 *
	 */
	public function wp_update_comment_count_now( $post_id );

	/**
	 * Finds a pingback server URI based on the given URL.
	 *
	 * Checks the HTML for the rel="pingback" link and x-pingback headers. It does
	 * a check for the x-pingback headers first and returns that, if available. The
	 * check for the rel="pingback" has more overhead than just the header.
	 *
	 * @param string $url        URL to ping.
	 * @param string $deprecated Not Used.
	 *
	 * @return string|false String containing URI on success, false on failure.
	 * @since 1.5.0
	 *
	 */
	public function discover_pingback_server_uri( $url, $deprecated = '' );

	/**
	 * Perform all pingbacks, enclosures, trackbacks, and send to pingback services.
	 *
	 * @since 2.1.0
	 * @since 5.6.0 Introduced `do_all_pings` action hook for individual services.
	 */
	public function do_all_pings();

	/**
	 * Perform all pingbacks.
	 *
	 * @since 5.6.0
	 */
	public function do_all_pingbacks();

	/**
	 * Perform all enclosures.
	 *
	 * @since 5.6.0
	 */
	public function do_all_enclosures();

	/**
	 * Perform all trackbacks.
	 *
	 * @since 5.6.0
	 */
	public function do_all_trackbacks();

	/**
	 * Perform trackbacks.
	 *
	 * @param int|WP_Post $post_id Post object or ID to do trackbacks on.
	 *
	 * @since 4.7.0 `$post_id` can be a WP_Post object.
	 *
	 * @global wpdb       $wpdb    WordPress database abstraction object.
	 *
	 * @since 1.5.0
	 */
	public function do_trackbacks( $post_id );

	/**
	 * Sends pings to all of the ping site services.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Same as Post ID from parameter
	 * @since 1.2.0
	 *
	 */
	public function generic_ping( $post_id = 0 );

	/**
	 * Pings back the links found in a post.
	 *
	 * @param string      $content Post content to check for links. If empty will retrieve from post.
	 * @param int|WP_Post $post_id Post Object or ID.
	 *
	 * @since 0.71
	 * @since 4.7.0 `$post_id` can be a WP_Post object.
	 *
	 */
	public function pingback( $content, $post_id );

	/**
	 * Check whether blog is public before returning sites.
	 *
	 * @param mixed $sites Will return if blog is public, will not return if not public.
	 *
	 * @return mixed Empty string if blog is not public, returns $sites, if site is public.
	 * @since 2.1.0
	 *
	 */
	public function privacy_ping_filter( $sites );

	/**
	 * Send a Trackback.
	 *
	 * Updates database when sending trackback to prevent duplicates.
	 *
	 * @param string $trackback_url URL to send trackbacks.
	 * @param string $title         Title of post.
	 * @param string $excerpt       Excerpt of post.
	 * @param int    $ID            Post ID.
	 *
	 * @return int|false|void Database query from update.
	 * @global wpdb  $wpdb          WordPress database abstraction object.
	 *
	 * @since 0.71
	 *
	 */
	public function trackback( $trackback_url, $title, $excerpt, $ID );

	/**
	 * Send a pingback.
	 *
	 * @param string $server Host of blog to connect to.
	 * @param string $path   Path to send the ping.
	 *
	 * @since 1.2.0
	 *
	 */
	public function weblog_ping( $server = '', $path = '' );

	/**
	 * Default filter attached to pingback_ping_source_uri to validate the pingback's Source URI
	 *
	 * @param string $source_uri
	 *
	 * @return string
	 * @since 3.5.1
	 *
	 * @see   wp_http_validate_url()
	 *
	 */
	public function pingback_ping_source_uri( $source_uri );

	/**
	 * Default filter attached to xmlrpc_pingback_error.
	 *
	 * Returns a generic pingback error code unless the error code is 48,
	 * which reports that the pingback is already registered.
	 *
	 * @param IXR_Error $ixr_error
	 *
	 * @return IXR_Error
	 * @since 3.5.1
	 *
	 * @link  https://www.hixie.ch/specs/pingback/pingback#TOC3
	 *
	 */
	public function xmlrpc_pingback_error( $ixr_error );

	/**
	 * Removes a comment from the object cache.
	 *
	 * @param int|array $ids Comment ID or an array of comment IDs to remove from cache.
	 *
	 * @since 2.3.0
	 *
	 */
	public function clean_comment_cache( $ids );

	/**
	 * Updates the comment cache of given comments.
	 *
	 * Will add the comments in $comments to the cache. If comment ID already exists
	 * in the comment cache then it will not be updated. The comment is added to the
	 * cache using the comment group with the key using the ID of the comments.
	 *
	 * @param WP_Comment[] $comments          Array of comment objects
	 * @param bool         $update_meta_cache Whether to update commentmeta cache. Default true.
	 *
	 * @since 2.3.0
	 * @since 4.4.0 Introduced the `$update_meta_cache` parameter.
	 *
	 */
	public function update_comment_cache( $comments, $update_meta_cache = true );

	/**
	 * Handles the submission of a comment, usually posted to wp-comments-post.php via a comment form.
	 *
	 * This function expects unslashed data, as opposed to functions such as `wp_new_comment()` which
	 * expect slashed data.
	 *
	 * @param array     $comment_data                {
	 *                                               Comment data.
	 *
	 * @type string|int $comment_post_ID             The ID of the post that relates to the comment.
	 * @type string     $author                      The name of the comment author.
	 * @type string     $email                       The comment author email address.
	 * @type string     $url                         The comment author URL.
	 * @type string     $comment                     The content of the comment.
	 * @type string|int $comment_parent              The ID of this comment's parent, if any. Default 0.
	 * @type string     $_wp_unfiltered_html_comment The nonce value for allowing unfiltered HTML.
	 * }
	 * @return WP_Comment|WP_Error A WP_Comment object on success, a WP_Error object on failure.
	 * @since 4.4.0
	 *
	 */
	public function wp_handle_comment_submission( $comment_data );

	/**
	 * Registers the personal data exporter for comments.
	 *
	 * @param array $exporters An array of personal data exporters.
	 *
	 * @return array An array of personal data exporters.
	 * @since 4.9.6
	 *
	 */
	public function wp_register_comment_personal_data_exporter( $exporters );

	/**
	 * Finds and exports personal data associated with an email address from the comments table.
	 *
	 * @param string $email_address The comment author email address.
	 * @param int    $page          Comment page.
	 *
	 * @return array An array of personal data.
	 * @since 4.9.6
	 *
	 */
	public function wp_comments_personal_data_exporter( $email_address, $page = 1 );

	/**
	 * Registers the personal data eraser for comments.
	 *
	 * @param array $erasers An array of personal data erasers.
	 *
	 * @return array An array of personal data erasers.
	 * @since 4.9.6
	 *
	 */
	public function wp_register_comment_personal_data_eraser( $erasers );

	/**
	 * Erases personal data associated with an email address from the comments table.
	 *
	 * @param string $email_address The comment author email address.
	 * @param int    $page          Comment page.
	 *
	 * @return array
	 * @since 4.9.6
	 *
	 */
	public function wp_comments_personal_data_eraser( $email_address, $page = 1 );

	/**
	 * Sets the last changed time for the 'comment' cache group.
	 *
	 * @since 5.0.0
	 */
	public function wp_cache_set_comments_last_changed();

	/**
	 * Updates the comment type for a batch of comments.
	 *
	 * @since 5.5.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	public function _wp_batch_update_comment_type();

	/**
	 * In order to avoid the _wp_batch_update_comment_type() job being accidentally removed,
	 * check that it's still scheduled while we haven't finished updating comment types.
	 *
	 * @ignore
	 * @since 5.5.0
	 */
	public function _wp_check_for_scheduled_update_comment_type();
}

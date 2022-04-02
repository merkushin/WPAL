<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface PostAttachments {
	/**
	 * Retrieve attached file path based on attachment ID.
	 *
	 * By default the path will go through the 'get_attached_file' filter, but
	 * passing a true to the $unfiltered argument of get_attached_file() will
	 * return the file path unfiltered.
	 *
	 * The function works by getting the single post meta name, named
	 * '_wp_attached_file' and returning it. This is a convenience function to
	 * prevent looking up the meta name and provide a mechanism for sending the
	 * attached filename through a filter.
	 *
	 * @param int  $attachment_id Attachment ID.
	 * @param bool $unfiltered    Optional. Whether to apply filters. Default false.
	 *
	 * @return string|false The file path to where the attached file should be, false otherwise.
	 * @since 2.0.0
	 *
	 */
	function get_attached_file( int $attachment_id, $unfiltered = false );

	/**
	 * Update attachment file path based on attachment ID.
	 *
	 * Used to update the file path of the attachment, which uses post meta name
	 * '_wp_attached_file' to store the path of the attachment.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $file          File path for the attachment.
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.1.0
	 *
	 */
	function update_attached_file( int $attachment_id, string $file ): bool;

	/**
	 * Count number of attachments for the mime type(s).
	 *
	 * If you set the optional mime_type parameter, then an array will still be
	 * returned, but will only have the item you are looking for. It does not give
	 * you the number of attachments that are children of a post. You can get that
	 * by counting the number of children that post has.
	 *
	 * @param string|string[] $mime_type Optional. Array or comma-separated list of
	 *                                   MIME patterns. Default empty.
	 *
	 * @return stdClass An object containing the attachment counts by mime type.
	 * @since 2.5.0
	 *
	 * @global wpdb           $wpdb      WordPress database abstraction object.
	 *
	 */
	function wp_count_attachments( $mime_type = '' );

	/**
	 * Determines whether an attachment URI is local and really an attachment.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $url URL to check
	 *
	 * @return bool True on success, false on failure.
	 * @since 2.0.0
	 *
	 */
	function is_local_attachment( $url );

	/**
	 * Insert an attachment.
	 *
	 * If you set the 'ID' in the $args parameter, it will mean that you are
	 * updating and attempt to update the attachment. You can also set the
	 * attachment name or title by setting the key 'post_name' or 'post_title'.
	 *
	 * You can set the dates for the attachment manually by setting the 'post_date'
	 * and 'post_date_gmt' keys' values.
	 *
	 * By default, the comments will use the default settings for whether the
	 * comments are allowed. You can close them manually or keep them open by
	 * setting the value for the 'comment_status' key.
	 *
	 * @param string|array $args             Arguments for inserting an attachment.
	 * @param string|false $file             Optional. Filename.
	 * @param int          $parent           Optional. Parent post ID.
	 * @param bool         $wp_error         Optional. Whether to return a WP_Error on failure. Default false.
	 * @param bool         $fire_after_hooks Optional. Whether to fire the after insert hooks. Default true.
	 *
	 * @return int|WP_Error The attachment ID on success. The value 0 or WP_Error on failure.
	 * @since 2.0.0
	 * @since 4.7.0 Added the `$wp_error` parameter to allow a WP_Error to be returned on failure.
	 * @since 5.6.0 Added the `$fire_after_hooks` parameter.
	 *
	 * @see   wp_insert_post()
	 *
	 */
	function wp_insert_attachment( $args, $file = false, $parent = 0, $wp_error = false, $fire_after_hooks = true );

	/**
	 * Trash or delete an attachment.
	 *
	 * When an attachment is permanently deleted, the file will also be removed.
	 * Deletion removes all post meta fields, taxonomy, comments, etc. associated
	 * with the attachment (except the main post).
	 *
	 * The attachment is moved to the Trash instead of permanently deleted unless Trash
	 * for media is disabled, item is already in the Trash, or $force_delete is true.
	 *
	 * @param int   $post_id      Attachment ID.
	 * @param bool  $force_delete Optional. Whether to bypass Trash and force deletion.
	 *                            Default false.
	 *
	 * @return WP_Post|false|null Post data on success, false or null on failure.
	 * @global wpdb $wpdb         WordPress database abstraction object.
	 *
	 * @since 2.0.0
	 *
	 */
	function wp_delete_attachment( $post_id, $force_delete = false );

	/**
	 * Deletes all files that belong to the given attachment.
	 *
	 * @param int    $post_id      Attachment ID.
	 * @param array  $meta         The attachment's meta data.
	 * @param array  $backup_sizes The meta data for the attachment's backup images.
	 * @param string $file         Absolute path to the attachment's file.
	 *
	 * @return bool True on success, false on failure.
	 * @global wpdb  $wpdb         WordPress database abstraction object.
	 *
	 * @since 4.9.7
	 *
	 */
	function wp_delete_attachment_files( $post_id, $meta, $backup_sizes, $file );

	/**
	 * Retrieves attachment metadata for attachment ID.
	 *
	 * @param int   $attachment_id Attachment post ID. Defaults to global $post.
	 * @param bool  $unfiltered    Optional. If true, filters are not run. Default false.
	 *
	 * @return array|false {
	 *     Attachment metadata. False on failure.
	 *
	 * @type int    $width         The width of the attachment.
	 * @type int    $height        The height of the attachment.
	 * @type string $file          The file path relative to `wp-content/uploads`.
	 * @type array  $sizes         Keys are size slugs, each value is an array containing
	 *                              'file', 'width', 'height', and 'mime-type'.
	 * @type array  $image_meta    Image metadata.
	 * }
	 * @since 2.1.0
	 *
	 */
	function wp_get_attachment_metadata( $attachment_id = 0, $unfiltered = false );

	/**
	 * Updates metadata for an attachment.
	 *
	 * @param int   $attachment_id Attachment post ID.
	 * @param array $data          Attachment meta data.
	 *
	 * @return int|false False if $post is invalid.
	 * @since 2.1.0
	 *
	 */
	function wp_update_attachment_metadata( $attachment_id, $data );

	/**
	 * Retrieve the URL for an attachment.
	 *
	 * @param int     $attachment_id Optional. Attachment post ID. Defaults to global $post.
	 *
	 * @return string|false Attachment URL, otherwise false.
	 * @since 2.1.0
	 *
	 * @global string $pagenow
	 *
	 */
	function wp_get_attachment_url( $attachment_id = 0 );

	/**
	 * Retrieves the caption for an attachment.
	 *
	 * @param int $post_id Optional. Attachment ID. Default is the ID of the global `$post`.
	 *
	 * @return string|false Attachment caption on success, false on failure.
	 * @since 4.6.0
	 *
	 */
	function wp_get_attachment_caption( $post_id = 0 );

	/**
	 * Retrieve thumbnail for an attachment.
	 *
	 * @param int $post_id Optional. Attachment ID. Default is the ID of the global `$post`.
	 *
	 * @return string|false Thumbnail file path on success, false on failure.
	 * @since 2.1.0
	 *
	 */
	function wp_get_attachment_thumb_file( $post_id = 0 );

	/**
	 * Retrieve URL for an attachment thumbnail.
	 *
	 * @param int $post_id Optional. Attachment ID. Default is the ID of the global `$post`.
	 *
	 * @return string|false Thumbnail URL on success, false on failure.
	 * @since 2.1.0
	 *
	 */
	function wp_get_attachment_thumb_url( $post_id = 0 );

	/**
	 * Verifies an attachment is of a given type.
	 *
	 * @param string      $type Attachment type. Accepts 'image', 'audio', or 'video'.
	 * @param int|WP_Post $post Optional. Attachment ID or object. Default is global $post.
	 *
	 * @return bool True if one of the accepted types, false otherwise.
	 * @since 4.2.0
	 *
	 */
	function wp_attachment_is( $type, $post = null );

	/**
	 * Determines whether an attachment is an image.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param int|WP_Post $post Optional. Attachment ID or object. Default is global $post.
	 *
	 * @return bool Whether the attachment is an image.
	 * @since 2.1.0
	 * @since 4.2.0 Modified into wrapper for wp_attachment_is() and
	 *              allowed WP_Post object to be passed.
	 *
	 */
	function wp_attachment_is_image( $post = null );

	/**
	 * Will clean the attachment in the cache.
	 *
	 * Cleaning means delete from the cache. Optionally will clean the term
	 * object cache associated with the attachment ID.
	 *
	 * This function will not run if $_wp_suspend_cache_invalidation is not empty.
	 *
	 * @param int   $id          The attachment ID in the cache to clean.
	 * @param bool  $clean_terms Optional. Whether to clean terms cache. Default false.
	 *
	 * @since 3.0.0
	 *
	 * @global bool $_wp_suspend_cache_invalidation
	 *
	 */
	function clean_attachment_cache( $id, $clean_terms = false );
}

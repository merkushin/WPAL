<?php declare(strict_types=1);

namespace Merkushin\Wpal;

interface PostMeta {
	/**
	 * Adds a meta field to the given post.
	 *
	 * Post meta data is called "Custom Fields" on the Administration Screen.
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional. Whether the same key should not be added.
	 *                           Default false.
	 *
	 * @return int|false Meta ID on success, false on failure.
	 * @since 1.5.0
	 *
	 */
	function add_post_meta( $post_id, $meta_key, $meta_value, $unique = false );

	/**
	 * Deletes a post meta field for the given post ID.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching the key, if needed.
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. If provided,
	 *                           rows will only be removed that match the value.
	 *                           Must be serializable if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 * @since 1.5.0
	 *
	 */
	function delete_post_meta( $post_id, $meta_key, $meta_value = '' );

	/**
	 * Retrieves a post meta field for the given post ID.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Optional. The meta key to retrieve. By default,
	 *                        returns data for all keys. Default empty.
	 * @param bool   $single  Optional. Whether to return a single value.
	 *                        This parameter has no effect if `$key` is not specified.
	 *                        Default false.
	 *
	 * @return mixed An array of values if `$single` is false.
	 *               The value of the meta field if `$single` is true.
	 *               False for an invalid `$post_id` (non-numeric, zero, or negative value).
	 *               An empty string if a valid but non-existing post ID is passed.
	 * @since 1.5.0
	 *
	 */
	function get_post_meta( $post_id, $key = '', $single = false );

	/**
	 * Updates a post meta field based on the given post ID.
	 *
	 * Use the `$prev_value` parameter to differentiate between meta fields with the
	 * same key and post ID.
	 *
	 * If the meta field for the post does not exist, it will be added and its ID returned.
	 *
	 * Can be used in place of add_post_meta().
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before updating.
	 *                           If specified, only update existing metadata entries with
	 *                           this value. Otherwise, update all entries. Default empty.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure or if the value passed to the function
	 *                  is the same as the one that is already in the database.
	 * @since 1.5.0
	 *
	 */
	function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' );

	/**
	 * Deletes everything from post meta matching the given meta key.
	 *
	 * @param string $post_meta_key Key to search for when deleting.
	 *
	 * @return bool Whether the post meta key was deleted from the database.
	 * @since 2.3.0
	 *
	 */
	function delete_post_meta_by_key( $post_meta_key );

	/**
	 * Registers a meta key for posts.
	 *
	 * @param string $post_type Post type to register a meta key for. Pass an empty string
	 *                          to register the meta key across all existing post types.
	 * @param string $meta_key  The meta key to register.
	 * @param array  $args      Data used to describe the meta key when registered. See
	 *                          {@see register_meta()} for a list of supported arguments.
	 *
	 * @return bool True if the meta key was successfully registered, false if not.
	 * @since 4.9.8
	 *
	 */
	function register_post_meta( $post_type, $meta_key, array $args );

	/**
	 * Unregisters a meta key for posts.
	 *
	 * @param string $post_type Post type the meta key is currently registered for. Pass
	 *                          an empty string if the meta key is registered across all
	 *                          existing post types.
	 * @param string $meta_key  The meta key to unregister.
	 *
	 * @return bool True on success, false if the meta key was not previously registered.
	 * @since 4.9.8
	 *
	 */
	function unregister_post_meta( $post_type, $meta_key );

	/**
	 * Retrieve post meta fields, based on post ID.
	 *
	 * The post meta fields are retrieved from the cache where possible,
	 * so the function is optimized to be called more than once.
	 *
	 * @param int $post_id Optional. Post ID. Default is the ID of the global `$post`.
	 *
	 * @return array Post meta for the given post.
	 * @since 1.2.0
	 *
	 */
	function get_post_custom( $post_id = 0 );

	/**
	 * Retrieve meta field names for a post.
	 *
	 * If there are no meta fields, then nothing (null) will be returned.
	 *
	 * @param int $post_id Optional. Post ID. Default is the ID of the global `$post`.
	 *
	 * @return array|void Array of the keys, if retrieved.
	 * @since 1.2.0
	 *
	 */
	function get_post_custom_keys( $post_id = 0 );

	/**
	 * Retrieve values for a custom post field.
	 *
	 * The parameters must not be considered optional. All of the post meta fields
	 * will be retrieved and only the meta field key values returned.
	 *
	 * @param string $key     Optional. Meta field key. Default empty.
	 * @param int    $post_id Optional. Post ID. Default is the ID of the global `$post`.
	 *
	 * @return array|null Meta field values.
	 * @since 1.2.0
	 *
	 */
	function get_post_custom_values( $key = '', $post_id = 0 );

	/**
	 * Updates metadata cache for list of post IDs.
	 *
	 * Performs SQL query to retrieve the metadata for the post IDs and updates the
	 * metadata cache for the posts. Therefore, the functions, which call this
	 * function, do not need to perform SQL queries on their own.
	 *
	 * @param int[] $post_ids Array of post IDs.
	 *
	 * @return array|false An array of metadata on success, false if there is nothing to update.
	 * @since 2.1.0
	 *
	 */
	function update_postmeta_cache( $post_ids );
}

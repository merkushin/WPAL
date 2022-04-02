<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface PostStatuses {
	/**
	 * Retrieve the post status based on the post ID.
	 *
	 * If the post ID is of an attachment, then the parent post status will be given
	 * instead.
	 *
	 * @param int|WP_Post $post Optional. Post ID or post object. Defaults to global $post.
	 *
	 * @return string|false Post status on success, false on failure.
	 * @since 2.0.0
	 *
	 */
	function get_post_status( $post = null );

	/**
	 * Retrieve all of the WordPress supported post statuses.
	 *
	 * Posts have a limited set of valid status values, this provides the
	 * post_status values and descriptions.
	 *
	 * @return string[] Array of post status labels keyed by their status.
	 * @since 2.5.0
	 *
	 */
	function get_post_statuses();

	/**
	 * Retrieve all of the WordPress support page statuses.
	 *
	 * Pages have a limited set of valid status values, this provides the
	 * post_status values and descriptions.
	 *
	 * @return string[] Array of page status labels keyed by their status.
	 * @since 2.5.0
	 *
	 */
	function get_page_statuses();

	/**
	 * Register a post status. Do not use before init.
	 *
	 * A simple function for creating or modifying a post status based on the
	 * parameters given. The function will accept an array (second optional
	 * parameter), along with a string for the post status name.
	 *
	 * Arguments prefixed with an _underscore shouldn't be used by plugins and themes.
	 *
	 * @param string       $post_status               Name of the post status.
	 * @param array|string $args                      {
	 *                                                Optional. Array or string of post status arguments.
	 *
	 * @type bool|string   $label                     A descriptive name for the post status marked
	 *                                                  for translation. Defaults to value of $post_status.
	 * @type bool|array    $label_count               Descriptive text to use for nooped plurals.
	 *                                                  Default array of $label, twice.
	 * @type bool          $exclude_from_search       Whether to exclude posts with this post status
	 *                                                  from search results. Default is value of $internal.
	 * @type bool          $_builtin                  Whether the status is built-in. Core-use only.
	 *                                                  Default false.
	 * @type bool          $public                    Whether posts of this status should be shown
	 *                                                  in the front end of the site. Default false.
	 * @type bool          $internal                  Whether the status is for internal use only.
	 *                                                  Default false.
	 * @type bool          $protected                 Whether posts with this status should be protected.
	 *                                                  Default false.
	 * @type bool          $private                   Whether posts with this status should be private.
	 *                                                  Default false.
	 * @type bool          $publicly_queryable        Whether posts with this status should be publicly-
	 *                                                  queryable. Default is value of $public.
	 * @type bool          $show_in_admin_all_list    Whether to include posts in the edit listing for
	 *                                                  their post type. Default is the opposite value
	 *                                                  of $internal.
	 * @type bool          $show_in_admin_status_list Show in the list of statuses with post counts at
	 *                                                  the top of the edit listings,
	 *                                                  e.g. All (12) | Published (9) | My Custom Status (2)
	 *                                                  Default is the opposite value of $internal.
	 * @type bool          $date_floating             Whether the post has a floating creation date.
	 *                                                  Default to false.
	 * }
	 * @return object
	 * @global stdClass[]  $wp_post_statuses          Inserts new post status object into the list
	 *
	 * @since 3.0.0
	 *
	 */
	function register_post_status( $post_status, $args = [] );

	/**
	 * Retrieve a post status object by name.
	 *
	 * @param string      $post_status      The name of a registered post status.
	 *
	 * @return stdClass|null A post status object.
	 * @see   register_post_status()
	 *
	 * @since 3.0.0
	 *
	 * @global stdClass[] $wp_post_statuses List of post statuses.
	 *
	 */
	function get_post_status_object( $post_status );

	/**
	 * Get a list of post statuses.
	 *
	 * @param array|string $args             Optional. Array or string of post status arguments to compare against
	 *                                       properties of the global `$wp_post_statuses objects`. Default empty array.
	 * @param string       $output           Optional. The type of output to return, either 'names' or 'objects'.
	 *                                       Default 'names'.
	 * @param string       $operator         Optional. The logical operation to perform. 'or' means only one element
	 *                                       from the array needs to match; 'and' means all elements must match.
	 *                                       Default 'and'.
	 *
	 * @return string[]|stdClass[] A list of post status names or objects.
	 * @global stdClass[]  $wp_post_statuses List of post statuses.
	 *
	 * @see   register_post_status()
	 *
	 * @since 3.0.0
	 *
	 */
	function get_post_stati( $args = [], $output = 'names', $operator = 'and' );

	/**
	 * Determine whether a post status is considered "viewable".
	 *
	 * For built-in post statuses such as publish and private, the 'public' value will be evaluted.
	 * For all others, the 'publicly_queryable' value will be used.
	 *
	 * @param string|stdClass $post_status Post status name or object.
	 *
	 * @return bool Whether the post status should be considered viewable.
	 * @since 5.7.0
	 *
	 */
	function is_post_status_viewable( $post_status );
}

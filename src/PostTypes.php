<?php declare(strict_types=1);

namespace Merkushin\Wpal;

interface PostTypes {
	/**
	 * Whether the post type is hierarchical.
	 *
	 * A false return value might also mean that the post type does not exist.
	 *
	 * @param string $post_type Post type name
	 *
	 * @return bool Whether post type is hierarchical.
	 * @since 3.0.0
	 *
	 * @see   get_post_type_object()
	 *
	 */
	function is_post_type_hierarchical( $post_type );

	/**
	 * Determines whether a post type is registered.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return bool Whether post type is registered.
	 * @since 3.0.0
	 *
	 * @see   get_post_type_object()
	 *
	 */
	function post_type_exists( $post_type );

	/**
	 * Retrieves the post type of the current post or of a given post.
	 *
	 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
	 *
	 * @return string|false          Post type on success, false on failure.
	 * @since 2.1.0
	 *
	 */
	function get_post_type( $post = null );

	/**
	 * Retrieves a post type object by name.
	 *
	 * @param string $post_type     The name of a registered post type.
	 *
	 * @return WP_Post_Type|null WP_Post_Type object if it exists, null otherwise.
	 * @global array $wp_post_types List of post types.
	 *
	 * @see   register_post_type()
	 *
	 * @since 3.0.0
	 * @since 4.6.0 Object returned is now an instance of `WP_Post_Type`.
	 *
	 */
	function get_post_type_object( $post_type );

	/**
	 * Get a list of all registered post type objects.
	 *
	 * @param array|string $args          Optional. An array of key => value arguments to match against
	 *                                    the post type objects. Default empty array.
	 * @param string       $output        Optional. The type of output to return. Accepts post type 'names'
	 *                                    or 'objects'. Default 'names'.
	 * @param string       $operator      Optional. The logical operation to perform. 'or' means only one
	 *                                    element from the array needs to match; 'and' means all elements
	 *                                    must match; 'not' means no elements may match. Default 'and'.
	 *
	 * @return string[]|WP_Post_Type[] An array of post type names or objects.
	 * @global array       $wp_post_types List of post types.
	 *
	 * @see   register_post_type() for accepted arguments.
	 *
	 * @since 2.9.0
	 *
	 */
	function get_post_types( $args = [], $output = 'names', $operator = 'and' );

	/**
	 * Registers a post type.
	 *
	 * Note: Post type registrations should not be hooked before the
	 * {@see 'init'} action. Also, any taxonomy connections should be
	 * registered via the `$taxonomies` argument to ensure consistency
	 * when hooks such as {@see 'parse_query'} or {@see 'pre_get_posts'}
	 * are used.
	 *
	 * Post types can support any number of built-in core features such
	 * as meta boxes, custom fields, post thumbnails, post statuses,
	 * comments, and more. See the `$supports` argument for a complete
	 * list of supported features.
	 *
	 * @param string       $post_type             Post type key. Must not exceed 20 characters and may
	 *                                            only contain lowercase alphanumeric characters, dashes,
	 *                                            and underscores. See sanitize_key().
	 * @param array|string $args                  {
	 *                                            Array or string of arguments for registering a post type.
	 *
	 * @type string        $label                 Name of the post type shown in the menu. Usually plural.
	 *                                               Default is value of $labels['name'].
	 * @type string[]      $labels                An array of labels for this post type. If not set, post
	 *                                               labels are inherited for non-hierarchical types and page
	 *                                               labels for hierarchical ones. See get_post_type_labels() for a
	 *                                               full
	 *                                               list of supported labels.
	 * @type string        $description           A short descriptive summary of what the post type is.
	 *                                               Default empty.
	 * @type bool          $public                Whether a post type is intended for use publicly either via
	 *                                               the admin interface or by front-end users. While the default
	 *                                               settings of $exclude_from_search, $publicly_queryable, $show_ui,
	 *                                               and $show_in_nav_menus are inherited from $public, each does not
	 *                                               rely on this relationship and controls a very specific intention.
	 *                                               Default false.
	 * @type bool          $hierarchical          Whether the post type is hierarchical (e.g. page). Default false.
	 * @type bool          $exclude_from_search   Whether to exclude posts with this post type from front end search
	 *                                               results. Default is the opposite value of $public.
	 * @type bool          $publicly_queryable    Whether queries can be performed on the front end for the post type
	 *                                               as part of parse_request(). Endpoints would include:
	 *                                               * ?post_type={post_type_key}
	 *                                               * ?{post_type_key}={single_post_slug}
	 *                                               * ?{post_type_query_var}={single_post_slug}
	 *                                               If not set, the default is inherited from $public.
	 * @type bool          $show_ui               Whether to generate and allow a UI for managing this post type in the
	 *                                               admin. Default is value of $public.
	 * @type bool|string   $show_in_menu          Where to show the post type in the admin menu. To work, $show_ui
	 *                                               must be true. If true, the post type is shown in its own top level
	 *                                               menu. If false, no menu is shown. If a string of an existing top
	 *                                               level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the
	 *                                               post
	 *                                               type will be placed as a sub-menu of that.
	 *                                               Default is value of $show_ui.
	 * @type bool          $show_in_nav_menus     Makes this post type available for selection in navigation menus.
	 *                                               Default is value of $public.
	 * @type bool          $show_in_admin_bar     Makes this post type available via the admin bar. Default is value
	 *                                               of $show_in_menu.
	 * @type bool          $show_in_rest          Whether to include the post type in the REST API. Set this to true
	 *                                               for the post type to be available in the block editor.
	 * @type string        $rest_base             To change the base URL of REST API route. Default is $post_type.
	 * @type string        $rest_namespace        To change the namespace URL of REST API route. Default is wp/v2.
	 * @type string        $rest_controller_class REST API controller class name. Default is
	 *       'WP_REST_Posts_Controller'.
	 * @type int           $menu_position         The position in the menu order the post type should appear. To work,
	 *                                               $show_in_menu must be true. Default null (at the bottom).
	 * @type string        $menu_icon             The URL to the icon to be used for this menu. Pass a base64-encoded
	 *                                               SVG using a data URI, which will be colored to match the color
	 *                                               scheme
	 *                                               -- this should begin with 'data:image/svg+xml;base64,'. Pass the
	 *                                               name of a Dashicons helper class to use a font icon, e.g.
	 *                                               'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image
	 *                                               empty
	 *                                               so an icon can be added via CSS. Defaults to use the posts icon.
	 * @type string|array  $capability_type       The string to use to build the read, edit, and delete capabilities.
	 *                                               May be passed as an array to allow for alternative plurals when
	 *                                               using this argument as a base to construct the capabilities, e.g.
	 *                                               array('story', 'stories'). Default 'post'.
	 * @type string[]      $capabilities          Array of capabilities for this post type. $capability_type is used
	 *                                               as a base to construct capabilities by default.
	 *                                               See get_post_type_capabilities().
	 * @type bool          $map_meta_cap          Whether to use the internal default meta capability handling.
	 *                                               Default false.
	 * @type array         $supports              Core feature(s) the post type supports. Serves as an alias for
	 *       calling
	 *                                               add_post_type_support() directly. Core features include 'title',
	 *                                               'editor', 'comments', 'revisions', 'trackbacks', 'author',
	 *                                               'excerpt',
	 *                                               'page-attributes', 'thumbnail', 'custom-fields', and
	 *                                               'post-formats'. Additionally, the 'revisions' feature dictates
	 *                                               whether the post type will store revisions, and the 'comments'
	 *                                               feature dictates whether the comments count will show on the edit
	 *                                               screen. A feature can also be specified as an array of arguments
	 *                                               to provide additional information about supporting that feature.
	 *                                               Example: `array( 'my_feature', array( 'field' => 'value' ) )`.
	 *                                               Default is an array containing 'title' and 'editor'.
	 * @type callable      $register_meta_box_cb  Provide a callback function that sets up the meta boxes for the
	 *                                               edit form. Do remove_meta_box() and add_meta_box() calls in the
	 *                                               callback. Default null.
	 * @type string[]      $taxonomies            An array of taxonomy identifiers that will be registered for the
	 *                                               post type. Taxonomies can be registered later with
	 *                                               register_taxonomy() or register_taxonomy_for_object_type().
	 *                                               Default empty array.
	 * @type bool|string   $has_archive           Whether there should be post type archives, or if a string, the
	 *                                               archive slug to use. Will generate the proper rewrite rules if
	 *                                               $rewrite is enabled. Default false.
	 * @type bool|array    $rewrite               {
	 *         Triggers the handling of rewrites for this post type. To prevent rewrite, set to false.
	 *         Defaults to true, using $post_type as slug. To specify rewrite rules, an array can be
	 *         passed with any of these keys:
	 *
	 * @type string        $slug                  Customize the permastruct slug. Defaults to $post_type key.
	 * @type bool          $with_front            Whether the permastruct should be prepended with WP_Rewrite::$front.
	 *                                  Default true.
	 * @type bool          $feeds                 Whether the feed permastruct should be built for this post type.
	 *                                  Default is value of $has_archive.
	 * @type bool          $pages                 Whether the permastruct should provide for pagination. Default true.
	 * @type int           $ep_mask               Endpoint mask to assign. If not specified and permalink_epmask is
	 *       set,
	 *                                  inherits from $permalink_epmask. If not specified and permalink_epmask
	 *                                  is not set, defaults to EP_PERMALINK.
	 *     }
	 * @type string|bool   $query_var             Sets the query_var key for this post type. Defaults to $post_type
	 *                                               key. If false, a post type cannot be loaded at
	 *                                               ?{query_var}={post_slug}. If specified as a string, the query
	 *                                               ?{query_var_string}={post_slug} will be valid.
	 * @type bool          $can_export            Whether to allow this post type to be exported. Default true.
	 * @type bool          $delete_with_user      Whether to delete posts of this type when deleting a user.
	 *                                               * If true, posts of this type belonging to the user will be moved
	 *                                                 to Trash when the user is deleted.
	 *                                               * If false, posts of this type belonging to the user will *not*
	 *                                                 be trashed or deleted.
	 *                                               * If not set (the default), posts are trashed if post type
	 *                                               supports
	 *                                                 the 'author' feature. Otherwise posts are not trashed or
	 *                                                 deleted.
	 *                                               Default null.
	 * @type array         $template              Array of blocks to use as the default initial state for an editor
	 *                                               session. Each item should be an array containing block name and
	 *                                               optional attributes. Default empty array.
	 * @type string|false  $template_lock         Whether the block template should be locked if $template is set.
	 *                                               * If set to 'all', the user is unable to insert new blocks,
	 *                                                 move existing blocks and delete blocks.
	 *                                               * If set to 'insert', the user is able to move existing blocks
	 *                                                 but is unable to insert new blocks and delete blocks.
	 *                                               Default false.
	 * @type bool          $_builtin              FOR INTERNAL USE ONLY! True if this post type is a native or
	 *                                               "built-in" post_type. Default false.
	 * @type string        $_edit_link            FOR INTERNAL USE ONLY! URL segment to use for edit link of
	 *                                               this post type. Default 'post.php?post=%d'.
	 * }
	 * @return WP_Post_Type|WP_Error The registered post type object on success,
	 *                               WP_Error object on failure.
	 * @since 2.9.0
	 * @since 3.0.0 The `show_ui` argument is now enforced on the new post screen.
	 * @since 4.4.0 The `show_ui` argument is now enforced on the post type listing
	 *              screen and post editing screen.
	 * @since 4.6.0 Post type object returned is now an instance of `WP_Post_Type`.
	 * @since 4.7.0 Introduced `show_in_rest`, `rest_base` and `rest_controller_class`
	 *              arguments to register the post type in REST API.
	 * @since 5.0.0 The `template` and `template_lock` arguments were added.
	 * @since 5.3.0 The `supports` argument will now accept an array of arguments for a feature.
	 * @since 5.9.0 The `rest_namespace` argument was added.
	 *
	 * @global array       $wp_post_types         List of post types.
	 *
	 */
	function register_post_type( $post_type, $args = [] );

	/**
	 * Unregisters a post type.
	 *
	 * Can not be used to unregister built-in post types.
	 *
	 * @param string $post_type     Post type to unregister.
	 *
	 * @return true|WP_Error True on success, WP_Error on failure or if the post type doesn't exist.
	 * @since 4.5.0
	 *
	 * @global array $wp_post_types List of post types.
	 *
	 */
	function unregister_post_type( $post_type );

	/**
	 * Build an object with all post type capabilities out of a post type object
	 *
	 * Post type capabilities use the 'capability_type' argument as a base, if the
	 * capability is not set in the 'capabilities' argument array or if the
	 * 'capabilities' argument is not supplied.
	 *
	 * The capability_type argument can optionally be registered as an array, with
	 * the first value being singular and the second plural, e.g. array('story, 'stories')
	 * Otherwise, an 's' will be added to the value for the plural form. After
	 * registration, capability_type will always be a string of the singular value.
	 *
	 * By default, eight keys are accepted as part of the capabilities array:
	 *
	 * - edit_post, read_post, and delete_post are meta capabilities, which are then
	 *   generally mapped to corresponding primitive capabilities depending on the
	 *   context, which would be the post being edited/read/deleted and the user or
	 *   role being checked. Thus these capabilities would generally not be granted
	 *   directly to users or roles.
	 *
	 * - edit_posts - Controls whether objects of this post type can be edited.
	 * - edit_others_posts - Controls whether objects of this type owned by other users
	 *   can be edited. If the post type does not support an author, then this will
	 *   behave like edit_posts.
	 * - delete_posts - Controls whether objects of this post type can be deleted.
	 * - publish_posts - Controls publishing objects of this post type.
	 * - read_private_posts - Controls whether private objects can be read.
	 *
	 * These five primitive capabilities are checked in core in various locations.
	 * There are also six other primitive capabilities which are not referenced
	 * directly in core, except in map_meta_cap(), which takes the three aforementioned
	 * meta capabilities and translates them into one or more primitive capabilities
	 * that must then be checked against the user or role, depending on the context.
	 *
	 * - read - Controls whether objects of this post type can be read.
	 * - delete_private_posts - Controls whether private objects can be deleted.
	 * - delete_published_posts - Controls whether published objects can be deleted.
	 * - delete_others_posts - Controls whether objects owned by other users can be
	 *   can be deleted. If the post type does not support an author, then this will
	 *   behave like delete_posts.
	 * - edit_private_posts - Controls whether private objects can be edited.
	 * - edit_published_posts - Controls whether published objects can be edited.
	 *
	 * These additional capabilities are only used in map_meta_cap(). Thus, they are
	 * only assigned by default if the post type is registered with the 'map_meta_cap'
	 * argument set to true (default is false).
	 *
	 * @param object $args Post type registration arguments.
	 *
	 * @return object Object with all the capabilities as member variables.
	 * @see   register_post_type()
	 * @see   map_meta_cap()
	 *
	 * @since 3.0.0
	 * @since 5.4.0 'delete_posts' is included in default capabilities.
	 *
	 */
	function get_post_type_capabilities( $args );

	/**
	 * Registers support of certain features for a post type.
	 *
	 * All core features are directly associated with a functional area of the edit
	 * screen, such as the editor or a meta box. Features include: 'title', 'editor',
	 * 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes',
	 * 'thumbnail', 'custom-fields', and 'post-formats'.
	 *
	 * Additionally, the 'revisions' feature dictates whether the post type will
	 * store revisions, and the 'comments' feature dictates whether the comments
	 * count will show on the edit screen.
	 *
	 * A third, optional parameter can also be passed along with a feature to provide
	 * additional information about supporting that feature.
	 *
	 * Example usage:
	 *
	 *     add_post_type_support( 'my_post_type', 'comments' );
	 *     add_post_type_support( 'my_post_type', array(
	 *         'author', 'excerpt',
	 *     ) );
	 *     add_post_type_support( 'my_post_type', 'my_feature', array(
	 *         'field' => 'value',
	 *     ) );
	 *
	 * @param string       $post_type The post type for which to add the feature.
	 * @param string|array $feature   The feature being added, accepts an array of
	 *                                feature strings or a single string.
	 * @param mixed        ...$args   Optional extra arguments to pass along with certain features.
	 *
	 * @since 3.0.0
	 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 *
	 * @global array       $_wp_post_type_features
	 *
	 */
	function add_post_type_support( $post_type, $feature, ...$args );

	/**
	 * Remove support for a feature from a post type.
	 *
	 * @param string $post_type The post type for which to remove the feature.
	 * @param string $feature   The feature being removed.
	 *
	 * @since 3.0.0
	 *
	 * @global array $_wp_post_type_features
	 *
	 */
	function remove_post_type_support( $post_type, $feature );

	/**
	 * Get all the post type features
	 *
	 * @param string $post_type The post type.
	 *
	 * @return array Post type supports list.
	 * @since 3.4.0
	 *
	 * @global array $_wp_post_type_features
	 *
	 */
	function get_all_post_type_supports( $post_type );

	/**
	 * Check a post type's support for a given feature.
	 *
	 * @param string $post_type The post type being checked.
	 * @param string $feature   The feature being checked.
	 *
	 * @return bool Whether the post type supports the given feature.
	 * @global array $_wp_post_type_features
	 *
	 * @since 3.0.0
	 *
	 */
	function post_type_supports( $post_type, $feature );

	/**
	 * Retrieves a list of post type names that support a specific feature.
	 *
	 * @param array|string $feature                Single feature or an array of features the post types should support.
	 * @param string       $operator               Optional. The logical operation to perform. 'or' means
	 *                                             only one element from the array needs to match; 'and'
	 *                                             means all elements must match; 'not' means no elements may
	 *                                             match. Default 'and'.
	 *
	 * @return string[] A list of post type names.
	 * @global array       $_wp_post_type_features Post type features
	 *
	 * @since 4.5.0
	 *
	 */
	function get_post_types_by_support( $feature, $operator = 'and' );

	/**
	 * Update the post type for the post ID.
	 *
	 * The page or post cache will be cleaned for the post ID.
	 *
	 * @param int    $post_id   Optional. Post ID to change post type. Default 0.
	 * @param string $post_type Optional. Post type. Accepts 'post' or 'page' to
	 *                          name a few. Default 'post'.
	 *
	 * @return int|false Amount of rows changed. Should be 1 for success and 0 for failure.
	 * @global wpdb  $wpdb      WordPress database abstraction object.
	 *
	 * @since 2.5.0
	 *
	 */
	function set_post_type( $post_id = 0, $post_type = 'post' );

	/**
	 * Determines whether a post type is considered "viewable".
	 *
	 * For built-in post types such as posts and pages, the 'public' value will be evaluated.
	 * For all others, the 'publicly_queryable' value will be used.
	 *
	 * @param string|WP_Post_Type $post_type Post type name or object.
	 *
	 * @return bool Whether the post type should be considered viewable.
	 * @since 4.6.0 Converted the `$post_type` parameter to accept a `WP_Post_Type` object.
	 * @since 5.9.0 Added `is_post_type_viewable` hook to filter the result.
	 *
	 * @since 4.4.0
	 * @since 4.5.0 Added the ability to pass a post type name in addition to object.
	 */
	function is_post_type_viewable( $post_type );
}

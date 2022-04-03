<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Capabilities {
	/**
	 * Maps a capability to the primitive capabilities required of the given user to
	 * satisfy the capability being checked.
	 *
	 * This function also accepts an ID of an object to map against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by this function to map to primitive
	 * capabilities that a user or role requires, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     map_meta_cap( 'edit_posts', $user->ID );
	 *     map_meta_cap( 'edit_post', $user->ID, $post->ID );
	 *     map_meta_cap( 'edit_post_meta', $user->ID, $post->ID, $meta_key );
	 *
	 * This function does not check whether the user has the required capabilities,
	 * it just returns what the required capabilities are.
	 *
	 * @param string $cap                 Capability being checked.
	 * @param int    $user_id             User ID.
	 * @param mixed  ...$args             Optional further parameters, typically starting with an object ID.
	 *
	 * @return string[] Primitive capabilities required of the user.
	 * @since       2.0.0
	 * @since       4.9.6 Added the `export_others_personal_data`, `erase_others_personal_data`,
	 *              and `manage_privacy_options` capabilities.
	 * @since       5.1.0 Added the `update_php` capability.
	 * @since       5.2.0 Added the `resume_plugin` and `resume_theme` capabilities.
	 * @since       5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 * @since       5.7.0 Added the `create_app_password`, `list_app_passwords`, `read_app_password`,
	 *              `edit_app_password`, `delete_app_passwords`, `delete_app_password`,
	 *              and `update_https` capabilities.
	 *
	 * @global array $post_type_meta_caps Used to get post type meta capabilities.
	 *
	 */
	public function map_meta_cap( $cap, $user_id, ...$args );

	/**
	 * Returns whether the current user has the specified capability.
	 *
	 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
	 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     current_user_can( 'edit_posts' );
	 *     current_user_can( 'edit_post', $post->ID );
	 *     current_user_can( 'edit_post_meta', $post->ID, $meta_key );
	 *
	 * While checking against particular roles in place of a capability is supported
	 * in part, this practice is discouraged as it may produce unreliable results.
	 *
	 * Note: Will always return true if the current user is a super admin, unless specifically denied.
	 *
	 * @param string $capability Capability name.
	 * @param mixed  ...$args    Optional further parameters, typically starting with an object ID.
	 *
	 * @return bool Whether the current user has the given capability. If `$capability` is a meta cap and `$object_id`
	 *              is passed, whether the current user has the given meta capability for the given object.
	 * @see   WP_User::has_cap()
	 * @see   map_meta_cap()
	 *
	 * @since 2.0.0
	 * @since 5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 * @since 5.8.0 Converted to wrapper for the user_can() function.
	 *
	 */
	public function current_user_can( $capability, ...$args );

	/**
	 * Returns whether the current user has the specified capability for a given site.
	 *
	 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
	 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     current_user_can_for_blog( $blog_id, 'edit_posts' );
	 *     current_user_can_for_blog( $blog_id, 'edit_post', $post->ID );
	 *     current_user_can_for_blog( $blog_id, 'edit_post_meta', $post->ID, $meta_key );
	 *
	 * @param int    $blog_id    Site ID.
	 * @param string $capability Capability name.
	 * @param mixed  ...$args    Optional further parameters, typically starting with an object ID.
	 *
	 * @return bool Whether the user has the given capability.
	 * @since       5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 * @since       5.8.0 Wraps current_user_can() after switching to blog.
	 *
	 * @since       3.0.0
	 */
	public function current_user_can_for_blog( $blog_id, $capability, ...$args );

	/**
	 * Returns whether the author of the supplied post has the specified capability.
	 *
	 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
	 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     author_can( $post, 'edit_posts' );
	 *     author_can( $post, 'edit_post', $post->ID );
	 *     author_can( $post, 'edit_post_meta', $post->ID, $meta_key );
	 *
	 * @param int|WP_Post $post       Post ID or post object.
	 * @param string      $capability Capability name.
	 * @param mixed       ...$args    Optional further parameters, typically starting with an object ID.
	 *
	 * @return bool Whether the post author has the given capability.
	 * @since       2.9.0
	 * @since       5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 *
	 */
	public function author_can( $post, $capability, ...$args );

	/**
	 * Returns whether a particular user has the specified capability.
	 *
	 * This function also accepts an ID of an object to check against if the capability is a meta capability. Meta
	 * capabilities such as `edit_post` and `edit_user` are capabilities used by the `map_meta_cap()` function to
	 * map to primitive capabilities that a user or role has, such as `edit_posts` and `edit_others_posts`.
	 *
	 * Example usage:
	 *
	 *     user_can( $user->ID, 'edit_posts' );
	 *     user_can( $user->ID, 'edit_post', $post->ID );
	 *     user_can( $user->ID, 'edit_post_meta', $post->ID, $meta_key );
	 *
	 * @param int|WP_User $user       User ID or object.
	 * @param string      $capability Capability name.
	 * @param mixed       ...$args    Optional further parameters, typically starting with an object ID.
	 *
	 * @return bool Whether the user has the given capability.
	 * @since       3.1.0
	 * @since       5.3.0 Formalized the existing and already documented `...$args` parameter
	 *              by adding it to the function signature.
	 *
	 */
	public function user_can( $user, $capability, ...$args );

	/**
	 * Retrieves the global WP_Roles instance and instantiates it if necessary.
	 *
	 * @return WP_Roles WP_Roles global instance if not already instantiated.
	 * @global WP_Roles $wp_roles WordPress role management object.
	 *
	 * @since 4.3.0
	 *
	 */
	public function wp_roles();

	/**
	 * Retrieve role object.
	 *
	 * @param string $role Role name.
	 *
	 * @return WP_Role|null WP_Role object if found, null if the role does not exist.
	 * @since 2.0.0
	 *
	 */
	public function get_role( $role );

	/**
	 * Add role, if it does not exist.
	 *
	 * @param string $role         Role name.
	 * @param string $display_name Display name for role.
	 * @param bool[] $capabilities List of capabilities keyed by the capability name,
	 *                             e.g. array( 'edit_posts' => true, 'delete_posts' => false ).
	 *
	 * @return WP_Role|null WP_Role object if role is added, null if already exists.
	 * @since 2.0.0
	 *
	 */
	public function add_role( $role, $display_name, $capabilities = [] );

	/**
	 * Remove role, if it exists.
	 *
	 * @param string $role Role name.
	 *
	 * @since 2.0.0
	 *
	 */
	public function remove_role( $role );

	/**
	 * Retrieve a list of super admins.
	 *
	 * @return string[] List of super admin logins.
	 * @global array $super_admins
	 *
	 * @since 3.0.0
	 *
	 */
	public function get_super_admins();

	/**
	 * Determine if user is a site admin.
	 *
	 * @param int|false $user_id Optional. The ID of a user. Defaults to false, to check the current user.
	 *
	 * @return bool Whether the user is a site admin.
	 * @since 3.0.0
	 *
	 */
	public function is_super_admin( $user_id = false );

	/**
	 * Grants Super Admin privileges.
	 *
	 * @param int    $user_id ID of the user to be granted Super Admin privileges.
	 *
	 * @return bool True on success, false on failure. This can fail when the user is
	 *              already a super admin or when the `$super_admins` global is defined.
	 * @since 3.0.0
	 *
	 * @global array $super_admins
	 *
	 */
	public function grant_super_admin( $user_id );

	/**
	 * Revokes Super Admin privileges.
	 *
	 * @param int    $user_id ID of the user Super Admin privileges to be revoked from.
	 *
	 * @return bool True on success, false on failure. This can fail when the user's email
	 *              is the network admin email or when the `$super_admins` global is defined.
	 * @since 3.0.0
	 *
	 * @global array $super_admins
	 *
	 */
	public function revoke_super_admin( $user_id );

	/**
	 * Filters the user capabilities to grant the 'install_languages' capability as necessary.
	 *
	 * A user must have at least one out of the 'update_core', 'install_plugins', and
	 * 'install_themes' capabilities to qualify for 'install_languages'.
	 *
	 * @param bool[] $allcaps An array of all the user's capabilities.
	 *
	 * @return bool[] Filtered array of the user's capabilities.
	 * @since 4.9.0
	 *
	 */
	public function wp_maybe_grant_install_languages_cap( $allcaps );

	/**
	 * Filters the user capabilities to grant the 'resume_plugins' and 'resume_themes' capabilities as necessary.
	 *
	 * @param bool[] $allcaps An array of all the user's capabilities.
	 *
	 * @return bool[] Filtered array of the user's capabilities.
	 * @since 5.2.0
	 *
	 */
	public function wp_maybe_grant_resume_extensions_caps( $allcaps );

	/**
	 * Filters the user capabilities to grant the 'view_site_health_checks' capabilities as necessary.
	 *
	 * @param bool[]   $allcaps An array of all the user's capabilities.
	 * @param string[] $caps    Required primitive capabilities for the requested capability.
	 * @param array    $args    {
	 *                          Arguments that accompany the requested capability check.
	 *
	 * @type string    $0 Requested capability.
	 * @type int       $1 Concerned user ID.
	 * @type mixed  ...$2 Optional second and further parameters, typically object ID.
	 * }
	 *
	 * @param WP_User  $user    The user object.
	 *
	 * @return bool[] Filtered array of the user's capabilities.
	 * @since 5.2.2
	 *
	 */
	public function wp_maybe_grant_site_health_caps( $allcaps, $caps, $args, $user );
}

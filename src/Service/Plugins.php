<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Plugins {
	/**
	 * Gets the basename of a plugin.
	 *
	 * This method extracts the name of a plugin from its filename.
	 *
	 * @since 1.5.0
	 *
	 * @global array $wp_plugin_paths
	 *
	 * @param string $file The filename of plugin.
	 * @return string The name of a plugin.
	 */
	public function plugin_basename( string $file ): string;

	/**
	 * Register a plugin's real path.
	 *
	 * This is used in plugin_basename() to resolve symlinked paths.
	 *
	 * @since 3.9.0
	 *
	 * @see wp_normalize_path()
	 *
	 * @global array $wp_plugin_paths
	 *
	 * @param string $file Known path to the file.
	 * @return bool Whether the path was able to be registered.
	 */
	public function wp_register_plugin_realpath( string $file ): bool;

	/**
	 * Get the filesystem directory path (with trailing slash) for the plugin __FILE__ passed in.
	 *
	 * @since 2.8.0
	 *
	 * @param string $file The filename of the plugin (__FILE__).
	 * @return string the filesystem path of the directory that contains the plugin.
	 */
	public function plugin_dir_path( string $file ): string;

	/**
	 * Get the URL directory path (with trailing slash) for the plugin __FILE__ passed in.
	 *
	 * @since 2.8.0
	 *
	 * @param string $file The filename of the plugin (__FILE__).
	 * @return string the URL path of the directory that contains the plugin.
	 */
	public function plugin_dir_url( string $file ): string;

	/**
	 * Set the activation hook for a plugin.
	 *
	 * When a plugin is activated, the action 'activate_PLUGINNAME' hook is
	 * called. In the name of this hook, PLUGINNAME is replaced with the name
	 * of the plugin, including the optional subdirectory. For example, when the
	 * plugin is located in wp-content/plugins/sampleplugin/sample.php, then
	 * the name of this hook will become 'activate_sampleplugin/sample.php'.
	 *
	 * When the plugin consists of only one file and is (as by default) located at
	 * wp-content/plugins/sample.php the name of this hook will be
	 * 'activate_sample.php'.
	 *
	 * @since 2.0.0
	 *
	 * @param string   $file     The filename of the plugin including the path.
	 * @param callable $callback The function hooked to the 'activate_PLUGIN' action.
	 */
	public function register_activation_hook( string $file, callable $callback );

	/**
	 * Sets the deactivation hook for a plugin.
	 *
	 * When a plugin is deactivated, the action 'deactivate_PLUGINNAME' hook is
	 * called. In the name of this hook, PLUGINNAME is replaced with the name
	 * of the plugin, including the optional subdirectory. For example, when the
	 * plugin is located in wp-content/plugins/sampleplugin/sample.php, then
	 * the name of this hook will become 'deactivate_sampleplugin/sample.php'.
	 *
	 * When the plugin consists of only one file and is (as by default) located at
	 * wp-content/plugins/sample.php the name of this hook will be
	 * 'deactivate_sample.php'.
	 *
	 * @since 2.0.0
	 *
	 * @param string   $file     The filename of the plugin including the path.
	 * @param callable $callback The function hooked to the 'deactivate_PLUGIN' action.
	 */
	public function register_deactivation_hook( string $file, callable $callback );

	/**
	 * Sets the uninstallation hook for a plugin.
	 *
	 * Registers the uninstall hook that will be called when the user clicks on the
	 * uninstall link that calls for the plugin to uninstall itself. The link won't
	 * be active unless the plugin hooks into the action.
	 *
	 * The plugin should not run arbitrary code outside of functions, when
	 * registering the uninstall hook. In order to run using the hook, the plugin
	 * will have to be included, which means that any code laying outside of a
	 * function will be run during the uninstallation process. The plugin should not
	 * hinder the uninstallation process.
	 *
	 * If the plugin can not be written without running code within the plugin, then
	 * the plugin should create a file named 'uninstall.php' in the base plugin
	 * folder. This file will be called, if it exists, during the uninstallation process
	 * bypassing the uninstall hook. The plugin, when using the 'uninstall.php'
	 * should always check for the 'WP_UNINSTALL_PLUGIN' constant, before
	 * executing.
	 *
	 * @since 2.7.0
	 *
	 * @param string   $file     Plugin file.
	 * @param callable $callback The callback to run when the hook is called. Must be
	 *                           a static method or function.
	 */
	public function register_uninstall_hook( string $file, callable $callback );

	/**
	 * Parses the plugin contents to retrieve plugin's metadata.
	 *
	 * All plugin headers must be on their own line. Plugin description must not have
	 * any newlines, otherwise only parts of the description will be displayed.
	 * The below is formatted for printing.
	 *
	 *     /*
	 *     Plugin Name: Name of the plugin.
	 *     Plugin URI: The home page of the plugin.
	 *     Description: Plugin description.
	 *     Author: Plugin author's name.
	 *     Author URI: Link to the author's website.
	 *     Version: Plugin version.
	 *     Text Domain: Optional. Unique identifier, should be same as the one used in
	 *          load_plugin_textdomain().
	 *     Domain Path: Optional. Only useful if the translations are located in a
	 *          folder above the plugin's base path. For example, if .mo files are
	 *          located in the locale folder then Domain Path will be "/locale/" and
	 *          must have the first slash. Defaults to the base folder the plugin is
	 *          located in.
	 *     Network: Optional. Specify "Network: true" to require that a plugin is activated
	 *          across all sites in an installation. This will prevent a plugin from being
	 *          activated on a single site when Multisite is enabled.
	 *     Requires at least: Optional. Specify the minimum required WordPress version.
	 *     Requires PHP: Optional. Specify the minimum required PHP version.
	 *     * / # Remove the space to close comment.
	 *
	 * The first 8 KB of the file will be pulled in and if the plugin data is not
	 * within that first 8 KB, then the plugin author should correct their plugin
	 * and move the plugin data headers to the top.
	 *
	 * The plugin file is assumed to have permissions to allow for scripts to read
	 * the file. This is not checked however and the file is only opened for
	 * reading.
	 *
	 * @param string $plugin_file Absolute path to the main plugin file.
	 * @param bool   $markup      Optional. If the returned data should have HTML markup applied.
	 *                            Default true.
	 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
	 *
	 * @return array {
	 *     Plugin data. Values will be empty if not supplied by the plugin.
	 *
	 * @type string  $Name        Name of the plugin. Should be unique.
	 * @type string  $Title       Title of the plugin and link to the plugin's site (if set).
	 * @type string  $Description Plugin description.
	 * @type string  $Author      Author's name.
	 * @type string  $AuthorURI   Author's website address (if set).
	 * @type string  $Version     Plugin version.
	 * @type string  $TextDomain  Plugin textdomain.
	 * @type string  $DomainPath  Plugins relative directory path to .mo files.
	 * @type bool    $Network     Whether the plugin can only be activated network-wide.
	 * @type string  $RequiresWP  Minimum required version of WordPress.
	 * @type string  $RequiresPHP Minimum required version of PHP.
	 * @type string  $UpdateURI   ID of the plugin for update purposes, should be a URI.
	 * }
	 * @since 5.3.0 Added support for `Requires at least` and `Requires PHP` headers.
	 * @since 5.8.0 Added support for `Update URI` header.
	 *
	 * @since 1.5.0
	 */
	public function get_plugin_data( $plugin_file, $markup = true, $translate = true );

	/**
	 * Sanitizes plugin data, optionally adds markup, optionally translates.
	 *
	 * @param string $plugin_file Path to the main plugin file.
	 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`.
	 * @param bool   $markup      Optional. If the returned data should have HTML markup applied.
	 *                            Default true.
	 * @param bool   $translate   Optional. If the returned data should be translated. Default true.
	 *
	 * @return array {
	 *     Plugin data. Values will be empty if not supplied by the plugin.
	 *
	 * @type string  $Name        Name of the plugin. Should be unique.
	 * @type string  $Title       Title of the plugin and link to the plugin's site (if set).
	 * @type string  $Description Plugin description.
	 * @type string  $Author      Author's name.
	 * @type string  $AuthorURI   Author's website address (if set).
	 * @type string  $Version     Plugin version.
	 * @type string  $TextDomain  Plugin textdomain.
	 * @type string  $DomainPath  Plugins relative directory path to .mo files.
	 * @type bool    $Network     Whether the plugin can only be activated network-wide.
	 * }
	 * @see    get_plugin_data()
	 *
	 * @access private
	 *
	 * @since  2.7.0
	 *
	 */
	public function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true );

	/**
	 * Get a list of a plugin's files.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return string[] Array of file names relative to the plugin root.
	 * @since 2.8.0
	 *
	 */
	public function get_plugin_files( $plugin );

	/**
	 * Check the plugins directory and retrieve all plugin files with plugin data.
	 *
	 * WordPress only supports plugin files in the base plugins directory
	 * (wp-content/plugins) and in one directory above the plugins directory
	 * (wp-content/plugins/my-plugin). The file it looks for has the plugin data
	 * and must be found in those two locations. It is recommended to keep your
	 * plugin files in their own directories.
	 *
	 * The file with the plugin data is the file that will be included and therefore
	 * needs to have the main execution for the plugin. This does not mean
	 * everything must be contained in the file and it is recommended that the file
	 * be split for maintainability. Keep everything in one file for extreme
	 * optimization purposes.
	 *
	 * @param string $plugin_folder Optional. Relative path to single plugin folder.
	 *
	 * @return array[] Array of arrays of plugin data, keyed by plugin file name. See `get_plugin_data()`.
	 * @since 1.5.0
	 *
	 */
	public function get_plugins( $plugin_folder = '' );

	/**
	 * Check the mu-plugins directory and retrieve all mu-plugin files with any plugin data.
	 *
	 * WordPress only includes mu-plugin files in the base mu-plugins directory (wp-content/mu-plugins).
	 *
	 * @return array[] Array of arrays of mu-plugin data, keyed by plugin file name. See `get_plugin_data()`.
	 * @since 3.0.0
	 */
	public function get_mu_plugins();

	/**
	 * Callback to sort array by a 'Name' key.
	 *
	 * @param array $a array with 'Name' key.
	 * @param array $b array with 'Name' key.
	 *
	 * @return int Return 0 or 1 based on two string comparison.
	 * @since  3.1.0
	 *
	 * @access private
	 *
	 */
	public function _sort_uname_callback( $a, $b );

	/**
	 * Check the wp-content directory and retrieve all drop-ins with any plugin data.
	 *
	 * @return array[] Array of arrays of dropin plugin data, keyed by plugin file name. See `get_plugin_data()`.
	 * @since 3.0.0
	 */
	public function get_dropins();

	/**
	 * Returns drop-ins that WordPress uses.
	 *
	 * Includes Multisite drop-ins only when is_multisite()
	 *
	 * @return array[] Key is file name. The value is an array, with the first value the
	 *  purpose of the drop-in and the second value the name of the constant that must be
	 *  true for the drop-in to be used, or true if no constant is required.
	 * @since 3.0.0
	 */
	public function _get_dropins();

	/**
	 * Determines whether a plugin is active.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool True, if in the active plugins list. False, not in the list.
	 * @since 2.5.0
	 *
	 */
	public function is_plugin_active( $plugin );

	/**
	 * Determines whether the plugin is inactive.
	 *
	 * Reverse of is_plugin_active(). Used as a callback.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool True if inactive. False if active.
	 * @since 3.1.0
	 *
	 * @see   is_plugin_active()
	 *
	 */
	public function is_plugin_inactive( $plugin );

	/**
	 * Determines whether the plugin is active for the entire network.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool True if active for the network, otherwise false.
	 * @since 3.0.0
	 *
	 */
	public function is_plugin_active_for_network( $plugin );

	/**
	 * Checks for "Network: true" in the plugin header to see if this should
	 * be activated only as a network wide plugin. The plugin would also work
	 * when Multisite is not enabled.
	 *
	 * Checks for "Site Wide Only: true" for backward compatibility.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool True if plugin is network only, false otherwise.
	 * @since 3.0.0
	 *
	 */
	public function is_network_only_plugin( $plugin );

	/**
	 * Attempts activation of plugin in a "sandbox" and redirects on success.
	 *
	 * A plugin that is already activated will not attempt to be activated again.
	 *
	 * The way it works is by setting the redirection to the error before trying to
	 * include the plugin file. If the plugin fails, then the redirection will not
	 * be overwritten with the success message. Also, the options will not be
	 * updated and the activation hook will not be called on plugin error.
	 *
	 * It should be noted that in no way the below code will actually prevent errors
	 * within the file. The code should not be used elsewhere to replicate the
	 * "sandbox", which uses redirection to work.
	 * {@source 13 1}
	 *
	 * If any errors are found or text is outputted, then it will be captured to
	 * ensure that the success redirection will update the error redirection.
	 *
	 * @param string $plugin       Path to the plugin file relative to the plugins directory.
	 * @param string $redirect     Optional. URL to redirect to.
	 * @param bool   $network_wide Optional. Whether to enable the plugin for all sites in the network
	 *                             or just the current site. Multisite only. Default false.
	 * @param bool   $silent       Optional. Whether to prevent calling activation hooks. Default false.
	 *
	 * @return null|WP_Error Null on success, WP_Error on invalid file.
	 * @since 5.2.0 Test for WordPress version and PHP version compatibility.
	 *
	 * @since 2.5.0
	 */
	public function activate_plugin( $plugin, $redirect = '', $network_wide = false, $silent = false );

	/**
	 * Deactivate a single plugin or multiple plugins.
	 *
	 * The deactivation hook is disabled by the plugin upgrader by using the $silent
	 * parameter.
	 *
	 * @param string|string[] $plugins      Single plugin or list of plugins to deactivate.
	 * @param bool            $silent       Prevent calling deactivation hooks. Default false.
	 * @param bool|null       $network_wide Whether to deactivate the plugin for all sites in the network.
	 *                                      A value of null will deactivate plugins for both the network
	 *                                      and the current site. Multisite only. Default null.
	 *
	 * @since 2.5.0
	 *
	 */
	public function deactivate_plugins( $plugins, $silent = false, $network_wide = null );

	/**
	 * Activate multiple plugins.
	 *
	 * When WP_Error is returned, it does not mean that one of the plugins had
	 * errors. It means that one or more of the plugin file paths were invalid.
	 *
	 * The execution will be halted as soon as one of the plugins has an error.
	 *
	 * @param string|string[] $plugins      Single plugin or list of plugins to activate.
	 * @param string          $redirect     Redirect to page after successful activation.
	 * @param bool            $network_wide Whether to enable the plugin for all sites in the network.
	 *                                      Default false.
	 * @param bool            $silent       Prevent calling activation hooks. Default false.
	 *
	 * @return bool|WP_Error True when finished or WP_Error if there were errors during a plugin activation.
	 * @since 2.6.0
	 *
	 */
	public function activate_plugins( $plugins, $redirect = '', $network_wide = false, $silent = false );

	/**
	 * Remove directory and files of a plugin for a list of plugins.
	 *
	 * @param string[]            $plugins       List of plugin paths to delete, relative to the plugins directory.
	 * @param string              $deprecated    Not used.
	 *
	 * @return bool|null|WP_Error True on success, false if `$plugins` is empty, `WP_Error` on failure.
	 *                            `null` if filesystem credentials are required to proceed.
	 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
	 *
	 * @since 2.6.0
	 *
	 */
	public function delete_plugins( $plugins, $deprecated = '' );

	/**
	 * Validate active plugins
	 *
	 * Validate all active plugins, deactivates invalid and
	 * returns an array of deactivated ones.
	 *
	 * @return WP_Error[] Array of plugin errors keyed by plugin file name.
	 * @since 2.5.0
	 */
	public function validate_active_plugins();

	/**
	 * Validate the plugin path.
	 *
	 * Checks that the main plugin file exists and is a valid plugin. See validate_file().
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return int|WP_Error 0 on success, WP_Error on failure.
	 * @since 2.5.0
	 *
	 */
	public function validate_plugin( $plugin );

	/**
	 * Validates the plugin requirements for WordPress version and PHP version.
	 *
	 * Uses the information from `Requires at least` and `Requires PHP` headers
	 * defined in the plugin's main PHP file.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return true|WP_Error True if requirements are met, WP_Error on failure.
	 * @since 5.8.0 Removed support for using `readme.txt` as a fallback.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added support for reading the headers from the plugin's
	 *              main PHP file, with `readme.txt` as a fallback.
	 */
	public function validate_plugin_requirements( $plugin );

	/**
	 * Whether the plugin can be uninstalled.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool Whether plugin can be uninstalled.
	 * @since 2.7.0
	 *
	 */
	public function is_uninstallable_plugin( $plugin );

	/**
	 * Uninstall a single plugin.
	 *
	 * Calls the uninstall hook, if it is available.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return true|void True if a plugin's uninstall.php file has been found and included.
	 *                   Void otherwise.
	 * @since 2.7.0
	 *
	 */
	public function uninstall_plugin( $plugin );

	/**
	 * Adds a top-level menu page.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title   The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title   The text to be used for the menu.
	 * @param string   $capability   The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug    The slug name to refer to this menu by. Should be unique for this menu page and
	 *                               only include lowercase alphanumeric, dashes, and underscores characters to be
	 *                               compatible with sanitize_key().
	 * @param callable $function     Optional. The function to be called to output the content for this page.
	 * @param string   $icon_url     Optional. The URL to the icon to be used for this menu.
	 *                               * Pass a base64-encoded SVG using a data URI, which will be colored to match
	 *                               the color scheme. This should begin with 'data:image/svg+xml;base64,'.
	 *                               * Pass the name of a Dashicons helper class to use a font icon,
	 *                               e.g. 'dashicons-chart-pie'.
	 *                               * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 * @param int      $position     Optional. The position in the menu order this item should appear.
	 *
	 * @return string The resulting page's hook_suffix.
	 * @since 1.5.0
	 *
	 * @global array   $menu
	 * @global array   $admin_page_hooks
	 * @global array   $_registered_pages
	 * @global array   $_parent_pages
	 *
	 */
	public function add_menu_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$icon_url = '',
		$position = null
	);

	/**
	 * Adds a submenu page.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $parent_slug The slug name for the parent menu (or the file name of a standard
	 *                              WordPress admin page).
	 * @param string   $page_title  The text to be displayed in the title tags of the page when the menu
	 *                              is selected.
	 * @param string   $menu_title  The text to be used for the menu.
	 * @param string   $capability  The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug   The slug name to refer to this menu by. Should be unique for this menu
	 *                              and only include lowercase alphanumeric, dashes, and underscores characters
	 *                              to be compatible with sanitize_key().
	 * @param callable $function    Optional. The function to be called to output the content for this page.
	 * @param int      $position    Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 1.5.0
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @global array   $submenu
	 * @global array   $menu
	 * @global array   $_wp_real_parent_file
	 * @global bool    $_wp_submenu_nopriv
	 * @global array   $_registered_pages
	 * @global array   $_parent_pages
	 *
	 */
	public function add_submenu_page(
		$parent_slug,
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	);

	/**
	 * Adds a submenu page to the Tools main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 1.5.0
	 */
	public function add_management_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Settings main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 1.5.0
	 */
	public function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Appearance main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.0.0
	 */
	public function add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Plugins main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 3.0.0
	 */
	public function add_plugins_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Users/Profile main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.1.3
	 */
	public function add_users_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Dashboard main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_dashboard_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Posts main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_posts_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Media main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_media_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Links main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_links_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Pages main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_pages_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Adds a submenu page to the Comments main menu.
	 *
	 * This function takes a capability which will be used to determine whether
	 * or not a page is included in the menu.
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string   $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string   $menu_title The text to be used for the menu.
	 * @param string   $capability The capability required for this menu to be displayed to the user.
	 * @param string   $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
	 * @param callable $function   Optional. The function to be called to output the content for this page.
	 * @param int      $position   Optional. The position in the menu order this item should appear.
	 *
	 * @return string|false The resulting page's hook_suffix, or false if the user does not have the capability
	 *                      required.
	 * @since 5.3.0 Added the `$position` parameter.
	 *
	 * @since 2.7.0
	 */
	public function add_comments_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null );

	/**
	 * Removes a top-level admin menu.
	 *
	 * Example usage:
	 *
	 *  - `remove_menu_page( 'tools.php' )`
	 *  - `remove_menu_page( 'plugin_menu_slug' )`
	 *
	 * @param string $menu_slug The slug of the menu.
	 *
	 * @return array|false The removed menu on success, false if not found.
	 * @since 3.1.0
	 *
	 * @global array $menu
	 *
	 */
	public function remove_menu_page( $menu_slug );

	/**
	 * Removes an admin submenu.
	 *
	 * Example usage:
	 *
	 *  - `remove_submenu_page( 'themes.php', 'nav-menus.php' )`
	 *  - `remove_submenu_page( 'tools.php', 'plugin_submenu_slug' )`
	 *  - `remove_submenu_page( 'plugin_menu_slug', 'plugin_submenu_slug' )`
	 *
	 * @param string $menu_slug    The slug for the parent menu.
	 * @param string $submenu_slug The slug of the submenu.
	 *
	 * @return array|false The removed submenu on success, false if not found.
	 * @global array $submenu
	 *
	 * @since 3.1.0
	 *
	 */
	public function remove_submenu_page( $menu_slug, $submenu_slug );

	/**
	 * Gets the URL to access a particular menu page based on the slug it was registered with.
	 *
	 * If the slug hasn't been registered properly, no URL will be returned.
	 *
	 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu).
	 * @param bool   $echo      Whether or not to echo the URL. Default true.
	 *
	 * @return string The menu page URL.
	 * @global array $_parent_pages
	 *
	 * @since 3.0.0
	 *
	 */
	public function menu_page_url( $menu_slug, $echo = true );

	/**
	 * Gets the parent file of the current admin page.
	 *
	 * @param string  $parent The slug name for the parent menu (or the file name of a standard
	 *                        WordPress admin page). Default empty string.
	 *
	 * @return string The parent file of the current admin page.
	 * @since 1.5.0
	 *
	 * @global string $parent_file
	 * @global array  $menu
	 * @global array  $submenu
	 * @global string $pagenow
	 * @global string $typenow
	 * @global string $plugin_page
	 * @global array  $_wp_real_parent_file
	 * @global array  $_wp_menu_nopriv
	 * @global array  $_wp_submenu_nopriv
	 *
	 */
	public function get_admin_page_parent( $parent = '' );

	/**
	 * Gets the title of the current admin page.
	 *
	 * @return string The title of the current admin page.
	 * @global string $title
	 * @global array  $menu
	 * @global array  $submenu
	 * @global string $pagenow
	 * @global string $plugin_page
	 * @global string $typenow
	 *
	 * @since 1.5.0
	 *
	 */
	public function get_admin_page_title();

	/**
	 * Gets the hook attached to the administrative page of a plugin.
	 *
	 * @param string $plugin_page The slug name of the plugin page.
	 * @param string $parent_page The slug name for the parent menu (or the file name of a standard
	 *                            WordPress admin page).
	 *
	 * @return string|null Hook attached to the plugin page, null otherwise.
	 * @since 1.5.0
	 *
	 */
	public function get_plugin_page_hook( $plugin_page, $parent_page );

	/**
	 * Gets the hook name for the administrative page of a plugin.
	 *
	 * @param string $plugin_page The slug name of the plugin page.
	 * @param string $parent_page The slug name for the parent menu (or the file name of a standard
	 *                            WordPress admin page).
	 *
	 * @return string Hook name for the plugin page.
	 * @global array $admin_page_hooks
	 *
	 * @since 1.5.0
	 *
	 */
	public function get_plugin_page_hookname( $plugin_page, $parent_page );

	/**
	 * Determines whether the current user can access the current admin page.
	 *
	 * @return bool True if the current user can access the admin page, false otherwise.
	 * @global string $pagenow
	 * @global array  $menu
	 * @global array  $submenu
	 * @global array  $_wp_menu_nopriv
	 * @global array  $_wp_submenu_nopriv
	 * @global string $plugin_page
	 * @global array  $_registered_pages
	 *
	 * @since 1.5.0
	 *
	 */
	public function user_can_access_admin_page();

	/**
	 * Refreshes the value of the allowed options list available via the 'allowed_options' hook.
	 *
	 * See the {@see 'allowed_options'} filter.
	 *
	 * @param array  $options
	 *
	 * @return array
	 * @global array $new_allowed_options
	 *
	 * @since 2.7.0
	 * @since 5.5.0 `$new_whitelist_options` was renamed to `$new_allowed_options`.
	 *              Please consider writing more inclusive code.
	 *
	 */
	public function option_update_filter( $options );

	/**
	 * Adds an array of options to the list of allowed options.
	 *
	 * @param array        $new_options
	 * @param string|array $options
	 *
	 * @return array
	 * @global array       $allowed_options
	 *
	 * @since 5.5.0
	 *
	 */
	public function add_allowed_options( $new_options, $options = '' );

	/**
	 * Removes a list of options from the allowed options list.
	 *
	 * @param array        $del_options
	 * @param string|array $options
	 *
	 * @return array
	 * @global array       $allowed_options
	 *
	 * @since 5.5.0
	 *
	 */
	public function remove_allowed_options( $del_options, $options = '' );

	/**
	 * Output nonce, action, and option_page fields for a settings page.
	 *
	 * @param string $option_group A settings group name. This should match the group name
	 *                             used in register_setting().
	 *
	 * @since 2.7.0
	 *
	 */
	public function settings_fields( $option_group );

	/**
	 * Clears the plugins cache used by get_plugins() and by default, the plugin updates cache.
	 *
	 * @param bool $clear_update_cache Whether to clear the plugin updates cache. Default true.
	 *
	 * @since 3.7.0
	 *
	 */
	public function wp_clean_plugins_cache( $clear_update_cache = true );

	/**
	 * Load a given plugin attempt to generate errors.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @since 4.4.0 Function was moved into the `wp-admin/includes/plugin.php` file.
	 *
	 * @since 3.0.0
	 */
	public function plugin_sandbox_scrape( $plugin );

	/**
	 * Helper function for adding content to the Privacy Policy Guide.
	 *
	 * Plugins and themes should suggest text for inclusion in the site's privacy policy.
	 * The suggested text should contain information about any functionality that affects user privacy,
	 * and will be shown on the Privacy Policy Guide screen.
	 *
	 * A plugin or theme can use this function multiple times as long as it will help to better present
	 * the suggested policy content. For example modular plugins such as WooCommerse or Jetpack
	 * can add or remove suggested content depending on the modules/extensions that are enabled.
	 * For more information see the Plugin Handbook:
	 * https://developer.wordpress.org/plugins/privacy/suggesting-text-for-the-site-privacy-policy/.
	 *
	 * The HTML contents of the `$policy_text` supports use of a specialized `.privacy-policy-tutorial`
	 * CSS class which can be used to provide supplemental information. Any content contained within
	 * HTML elements that have the `.privacy-policy-tutorial` CSS class applied will be omitted
	 * from the clipboard when the section content is copied.
	 *
	 * Intended for use with the `'admin_init'` action.
	 *
	 * @param string $plugin_name The name of the plugin or theme that is suggesting content
	 *                            for the site's privacy policy.
	 * @param string $policy_text The suggested content for inclusion in the policy.
	 *
	 * @since 4.9.6
	 *
	 */
	public function wp_add_privacy_policy_content( $plugin_name, $policy_text );

	/**
	 * Determines whether a plugin is technically active but was paused while
	 * loading.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return bool True, if in the list of paused plugins. False, if not in the list.
	 * @since 5.2.0
	 *
	 */
	public function is_plugin_paused( $plugin );

	/**
	 * Gets the error that was recorded for a paused plugin.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins directory.
	 *
	 * @return array|false Array of error information as returned by `error_get_last()`,
	 *                     or false if none was recorded.
	 * @since 5.2.0
	 *
	 */
	public function wp_get_plugin_error( $plugin );

	/**
	 * Tries to resume a single plugin.
	 *
	 * If a redirect was provided, we first ensure the plugin does not throw fatal
	 * errors anymore.
	 *
	 * The way it works is by setting the redirection to the error before trying to
	 * include the plugin file. If the plugin fails, then the redirection will not
	 * be overwritten with the success message and the plugin will not be resumed.
	 *
	 * @param string $plugin   Single plugin to resume.
	 * @param string $redirect Optional. URL to redirect to. Default empty string.
	 *
	 * @return bool|WP_Error True on success, false if `$plugin` was not paused,
	 *                       `WP_Error` on failure.
	 * @since 5.2.0
	 *
	 */
	public function resume_plugin( $plugin, $redirect = '' );

	/**
	 * Renders an admin notice in case some plugins have been paused due to errors.
	 *
	 * @since 5.2.0
	 *
	 * @global string $pagenow
	 */
	public function paused_plugins_notice();

	/**
	 * Renders an admin notice when a plugin was deactivated during an update.
	 *
	 * Displays an admin notice in case a plugin has been deactivated during an
	 * upgrade due to incompatibility with the current version of WordPress.
	 *
	 * @since  5.8.0
	 * @access private
	 *
	 * @global string $pagenow
	 * @global string $wp_version
	 */
	public function deactivated_plugins_notice();
}


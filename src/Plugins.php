<?php declare(strict_types=1);

namespace Merkushin\Wpal;

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
}


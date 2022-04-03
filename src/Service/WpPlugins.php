<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Service;

final class WpPlugins implements Plugins {
	public function plugin_basename( string $file ): string {
		return plugin_basename( $file );
	}

	public function wp_register_plugin_realpath( string $file ): bool {
		return wp_register_plugin_realpath( $file );
	}

	public function plugin_dir_path( string $file ): string {
		return plugin_dir_path( $file );
	}

	public function plugin_dir_url( string $file ): string {
		return plugin_dir_url( $file );
	}

	public function register_activation_hook( string $file, callable $callback ) {
		register_activation_hook( $file, $callback );
	}

	public function register_deactivation_hook( string $file, callable $callback ) {
		register_deactivation_hook( $file, $callback );
	}

	public function register_uninstall_hook( string $file, callable $callback ) {
		register_uninstall_hook( $file, $callback );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
		return get_plugin_data( $plugin_file, $markup, $translate );
	}

	/**
	 * @inheritDoc
	 */
	public function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true ) {
		return _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_files( $plugin ) {
		return get_plugin_files( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugins( $plugin_folder = '' ) {
		return get_plugins( $plugin_folder );
	}

	/**
	 * @inheritDoc
	 */
	public function get_mu_plugins() {
		return get_mu_plugins();
	}

	/**
	 * @inheritDoc
	 */
	public function _sort_uname_callback( $a, $b ) {
		return _sort_uname_callback( $a, $b );
	}

	/**
	 * @inheritDoc
	 */
	public function get_dropins() {
		return get_dropins();
	}

	/**
	 * @inheritDoc
	 */
	public function _get_dropins() {
		return _get_dropins();
	}

	/**
	 * @inheritDoc
	 */
	public function is_plugin_active( $plugin ) {
		return is_plugin_active( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function is_plugin_inactive( $plugin ) {
		return is_plugin_inactive( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function is_plugin_active_for_network( $plugin ) {
		return is_plugin_active_for_network( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function is_network_only_plugin( $plugin ) {
		return is_network_only_plugin( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function activate_plugin( $plugin, $redirect = '', $network_wide = false, $silent = false ) {
		return activate_plugin( $plugin, $redirect, $network_wide, $silent );
	}

	/**
	 * @inheritDoc
	 */
	public function deactivate_plugins( $plugins, $silent = false, $network_wide = null ) {
		deactivate_plugins( $plugins, $silent, $network_wide );
	}

	/**
	 * @inheritDoc
	 */
	public function activate_plugins( $plugins, $redirect = '', $network_wide = false, $silent = false ) {
		return activate_plugins( $plugins, $redirect, $network_wide, $silent );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_plugins( $plugins, $deprecated = '' ) {
		return delete_plugins( $plugins, $deprecated );
	}

	/**
	 * @inheritDoc
	 */
	public function validate_active_plugins() {
		return validate_active_plugins();
	}

	/**
	 * @inheritDoc
	 */
	public function validate_plugin( $plugin ) {
		return validate_plugin( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function validate_plugin_requirements( $plugin ) {
		return validate_plugin_requirements( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function is_uninstallable_plugin( $plugin ) {
		return is_uninstallable_plugin( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function uninstall_plugin( $plugin ) {
		return uninstall_plugin( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function add_menu_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$icon_url = '',
		$position = null
	) {
		return add_menu_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$icon_url,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_submenu_page(
		$parent_slug,
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_submenu_page(
			$parent_slug,
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_management_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_management_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_options_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_options_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_theme_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_theme_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_plugins_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_plugins_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_users_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_users_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_dashboard_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_dashboard_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_posts_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_posts_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_media_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_media_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_links_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_links_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_pages_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_pages_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function add_comments_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function = '',
		$position = null
	) {
		return add_comments_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function,
			$position
		);
	}

	/**
	 * @inheritDoc
	 */
	public function remove_menu_page( $menu_slug ) {
		return remove_menu_page( $menu_slug );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_submenu_page( $menu_slug, $submenu_slug ) {
		return remove_submenu_page( $menu_slug, $submenu_slug );
	}

	/**
	 * @inheritDoc
	 */
	public function menu_page_url( $menu_slug, $echo = true ) {
		return menu_page_url( $menu_slug, $echo );
	}

	/**
	 * @inheritDoc
	 */
	public function get_admin_page_parent( $parent = '' ) {
		return get_admin_page_parent( $parent );
	}

	/**
	 * @inheritDoc
	 */
	public function get_admin_page_title() {
		return get_admin_page_title();
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_page_hook( $plugin_page, $parent_page ) {
		return get_plugin_page_hook( $plugin_page, $parent_page );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_page_hookname( $plugin_page, $parent_page ) {
		return get_plugin_page_hookname( $plugin_page, $parent_page );
	}

	/**
	 * @inheritDoc
	 */
	public function user_can_access_admin_page() {
		return user_can_access_admin_page();
	}

	/**
	 * @inheritDoc
	 */
	public function option_update_filter( $options ) {
		return option_update_filter( $options );
	}

	/**
	 * @inheritDoc
	 */
	public function add_allowed_options( $new_options, $options = '' ) {
		return add_allowed_options( $new_options, $options = '' );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_allowed_options( $del_options, $options = '' ) {
		return remove_allowed_options( $del_options, $options = '' );
	}

	/**
	 * @inheritDoc
	 */
	public function settings_fields( $option_group ) {
		settings_fields( $option_group );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_clean_plugins_cache( $clear_update_cache = true ) {
		wp_clean_plugins_cache( $clear_update_cache );
	}

	/**
	 * @inheritDoc
	 */
	public function plugin_sandbox_scrape( $plugin ) {
		plugin_sandbox_scrape( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_add_privacy_policy_content( $plugin_name, $policy_text ) {
		wp_add_privacy_policy_content( $plugin_name, $policy_text );
	}

	/**
	 * @inheritDoc
	 */
	public function is_plugin_paused( $plugin ) {
		return is_plugin_paused( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_plugin_error( $plugin ) {
		return wp_get_plugin_error( $plugin );
	}

	/**
	 * @inheritDoc
	 */
	public function resume_plugin( $plugin, $redirect = '' ) {
		return resume_plugin( $plugin, $redirect );
	}

	/**
	 * @inheritDoc
	 */
	public function paused_plugins_notice() {
		paused_plugins_notice();
	}

	/**
	 * @inheritDoc
	 */
	public function deactivated_plugins_notice() {
		deactivated_plugins_notice();
	}
}


<?php

namespace Merkushin\Wpal;

class WpLocalization implements Localization {

	/**
	 * @inheritDoc
	 */
	public function get_locale() {
		return get_locale();
	}

	/**
	 * @inheritDoc
	 */
	public function get_user_locale( $user_id = 0 ) {
		return get_user_locale( $user_id );
	}

	/**
	 * @inheritDoc
	 */
	public function determine_locale() {
		return determine_locale();
	}

	/**
	 * @inheritDoc
	 */
	public function translate( $text, $domain = 'default' ) {
		return translate( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function before_last_bar( $string ) {
		return before_last_bar( $string );
	}

	/**
	 * @inheritDoc
	 */
	public function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
		return translate_with_gettext_context( $text, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_attr__( $text, $domain = 'default' ) {
		return esc_attr__( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_html__( $text, $domain = 'default' ) {
		return esc_html__( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _e( $text, $domain = 'default' ) {
		_e( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_attr_e( $text, $domain = 'default' ) {
		esc_attr_e( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_html_e( $text, $domain = 'default' ) {
		esc_html_e( $text, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _x( $text, $context, $domain = 'default' ) {
		return _x( $text, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _ex( $text, $context, $domain = 'default' ) {
		_ex( $text, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_attr_x( $text, $context, $domain = 'default' ) {
		return esc_attr_x( $text, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function esc_html_x( $text, $context, $domain = 'default' ) {
		return esc_html_x( $text, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _n( $single, $plural, $number, $domain = 'default' ) {
		return _n( $single, $plural, $number, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _nx( $single, $plural, $number, $context, $domain = 'default' ) {
		return _nx( $single, $plural, $number, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _n_noop( $singular, $plural, $domain = null ) {
		return _n_noop( $singular, $plural, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _nx_noop( $singular, $plural, $context, $domain = null ) {
		return _nx_noop( $singular, $plural, $context, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function translate_nooped_plural( $nooped_plural, $count, $domain = 'default' ) {
		return translate_nooped_plural( $nooped_plural, $count, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function load_textdomain( $domain, $mofile ) {
		return load_textdomain( $domain, $mofile );
	}

	/**
	 * @inheritDoc
	 */
	public function unload_textdomain( $domain ) {
		return unload_textdomain( $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function load_default_textdomain( $locale = null ) {
		return load_default_textdomain( $locale );
	}

	/**
	 * @inheritDoc
	 */
	public function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
		return load_plugin_textdomain( $domain, $deprecated, $plugin_rel_path );
	}

	/**
	 * @inheritDoc
	 */
	public function load_muplugin_textdomain( $domain, $mu_plugin_rel_path = '' ) {
		return load_muplugin_textdomain( $domain, $mu_plugin_rel_path );
	}

	/**
	 * @inheritDoc
	 */
	public function load_theme_textdomain( $domain, $path = false ) {
		return load_theme_textdomain( $domain, $path );
	}

	/**
	 * @inheritDoc
	 */
	public function load_child_theme_textdomain( $domain, $path = false ) {
		return load_child_theme_textdomain( $domain, $path );
	}

	/**
	 * @inheritDoc
	 */
	public function load_script_textdomain( $handle, $domain = 'default', $path = null ) {
		return load_script_textdomain( $handle, $domain, $path );
	}

	/**
	 * @inheritDoc
	 */
	public function load_script_translations( $file, $handle, $domain ) {
		return load_script_translations( $file, $handle, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _load_textdomain_just_in_time( $domain ) {
		return _load_textdomain_just_in_time( $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function _get_path_to_translation( $domain, $reset = false ) {
		return _get_path_to_translation( $domain, $reset );
	}

	/**
	 * @inheritDoc
	 */
	public function _get_path_to_translation_from_lang_dir( $domain ) {
		return _get_path_to_translation_from_lang_dir( $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function get_translations_for_domain( $domain ) {
		return get_translations_for_domain( $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function is_textdomain_loaded( $domain ) {
		return is_textdomain_loaded( $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function translate_user_role( $name, $domain = 'default' ) {
		return translate_user_role( $name, $domain );
	}

	/**
	 * @inheritDoc
	 */
	public function get_available_languages( $dir = null ) {
		return get_available_languages( $dir );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_installed_translations( $type ) {
		return wp_get_installed_translations( $type );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_pomo_file_data( $po_file ) {
		return wp_get_pomo_file_data( $po_file );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_dropdown_languages( $args = [] ) {
		return wp_dropdown_languages( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function is_rtl() {
		return is_rtl();
	}

	/**
	 * @inheritDoc
	 */
	public function switch_to_locale( $locale ) {
		return switch_to_locale( $locale );
	}

	/**
	 * @inheritDoc
	 */
	public function restore_previous_locale() {
		return restore_previous_locale();
	}

	/**
	 * @inheritDoc
	 */
	public function restore_current_locale() {
		return restore_current_locale();
	}

	/**
	 * @inheritDoc
	 */
	public function is_locale_switched() {
		return is_locale_switched();
	}

	/**
	 * @inheritDoc
	 */
	public function translate_settings_using_i18n_schema( $i18n_schema, $settings, $textdomain ) {
		return translate_settings_using_i18n_schema( $i18n_schema, $settings, $textdomain );
	}
}

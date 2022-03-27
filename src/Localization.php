<?php

namespace Merkushin\Wpal;

/**
 * Core Translation API
 *
 * @package    WordPress
 * @subpackage i18n
 * @since      1.2.0
 */
interface Localization {
	/**
	 * Retrieves the current locale.
	 *
	 * If the locale is set, then it will filter the locale in the {@see 'locale'}
	 * filter hook and return the value.
	 *
	 * If the locale is not set already, then the WPLANG constant is used if it is
	 * defined. Then it is filtered through the {@see 'locale'} filter hook and
	 * the value for the locale global set and the locale is returned.
	 *
	 * The process to get the locale should only be done once, but the locale will
	 * always be filtered using the {@see 'locale'} hook.
	 *
	 * @return string The locale of the blog or from the {@see 'locale'} hook.
	 * @global string $locale           The current locale.
	 * @global string $wp_local_package Locale code of the package.
	 *
	 * @since 1.5.0
	 *
	 */
	public function get_locale();

	/**
	 * Retrieves the locale of a user.
	 *
	 * If the user has a locale set to a non-empty string then it will be
	 * returned. Otherwise it returns the locale of get_locale().
	 *
	 * @param int|WP_User $user_id User's ID or a WP_User object. Defaults to current user.
	 *
	 * @return string The locale of the user.
	 * @since 4.7.0
	 *
	 */
	public function get_user_locale( $user_id = 0 );

	/**
	 * Determine the current locale desired for the request.
	 *
	 * @return string The determined locale.
	 * @global string $pagenow
	 *
	 * @since 5.0.0
	 *
	 */
	public function determine_locale();

	/**
	 * Retrieve the translation of $text.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 *
	 * *Note:* Don't use translate() directly, use __() or related public functions.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string Translated text.
	 * @since 5.5.0 Introduced gettext-{$domain} filter.
	 *
	 * @since 2.2.0
	 */
	public function translate( $text, $domain = 'default' );

	/**
	 * Remove last item on a pipe-delimited string.
	 *
	 * Meant for removing the last item in a string, such as 'Role name|User role'. The original
	 * string will be returned if no pipe '|' characters are found in the string.
	 *
	 * @param string $string A pipe-delimited string.
	 *
	 * @return string Either $string or everything before the last pipe.
	 * @since 2.8.0
	 *
	 */
	public function before_last_bar( $string );

	/**
	 * Retrieve the translation of $text in the context defined in $context.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 *
	 * *Note:* Don't use translate_with_gettext_context() directly, use _x() or related public functions.
	 *
	 * @param string $text    Text to translate.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @return string Translated text on success, original text on failure.
	 * @since 2.8.0
	 * @since 5.5.0 Introduced gettext_with_context-{$domain} filter.
	 *
	 */
	public function translate_with_gettext_context( $text, $context, $domain = 'default' );

	/**
	 * Retrieve the translation of $text.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string Translated text.
	 * @since 2.1.0
	 *
	 */
	public function __( $text, $domain = 'default' );

	/**
	 * Retrieve the translation of $text and escapes it for safe use in an attribute.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string Translated text on success, original text on failure.
	 * @since 2.8.0
	 *
	 */
	public function esc_attr__( $text, $domain = 'default' );

	/**
	 * Retrieve the translation of $text and escapes it for safe use in HTML output.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and returned.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string Translated text.
	 * @since 2.8.0
	 *
	 */
	public function esc_html__( $text, $domain = 'default' );

	/**
	 * Display translated text.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @since 1.2.0
	 *
	 */
	public function _e( $text, $domain = 'default' );

	/**
	 * Display translated text that has been escaped for safe use in an attribute.
	 *
	 * Encodes `< > & " '` (less than, greater than, ampersand, double quote, single quote).
	 * Will never double encode entities.
	 *
	 * If you need the value for use in PHP, use esc_attr__().
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @since 2.8.0
	 *
	 */
	public function esc_attr_e( $text, $domain = 'default' );

	/**
	 * Display translated text that has been escaped for safe use in HTML output.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and displayed.
	 *
	 * If you need the value for use in PHP, use esc_html__().
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @since 2.8.0
	 *
	 */
	public function esc_html_e( $text, $domain = 'default' );

	/**
	 * Retrieve translated string with gettext context.
	 *
	 * Quite a few times, there will be collisions with similar translatable text
	 * found in more than two places, but with different translated context.
	 *
	 * By including the context in the pot file, translators can translate the two
	 * strings differently.
	 *
	 * @param string $text    Text to translate.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @return string Translated context string without pipe.
	 * @since 2.8.0
	 *
	 */
	public function _x( $text, $context, $domain = 'default' );

	/**
	 * Display translated string with gettext context.
	 *
	 * @param string $text    Text to translate.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @since 3.0.0
	 *
	 */
	public function _ex( $text, $context, $domain = 'default' );

	/**
	 * Translate string with gettext context, and escapes it for safe use in an attribute.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and returned.
	 *
	 * @param string $text    Text to translate.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @return string Translated text.
	 * @since 2.8.0
	 *
	 */
	public function esc_attr_x( $text, $context, $domain = 'default' );

	/**
	 * Translate string with gettext context, and escapes it for safe use in HTML output.
	 *
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and returned.
	 *
	 * @param string $text    Text to translate.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @return string Translated text.
	 * @since 2.9.0
	 *
	 */
	public function esc_html_x( $text, $context, $domain = 'default' );

	/**
	 * Translates and retrieves the singular or plural form based on the supplied number.
	 *
	 * Used when you want to use the appropriate form of a string based on whether a
	 * number is singular or plural.
	 *
	 * Example:
	 *
	 *     printf( _n( '%s person', '%s people', $count, 'text-domain' ), number_format_i18n( $count ) );
	 *
	 * @param string $single The text to be used if the number is singular.
	 * @param string $plural The text to be used if the number is plural.
	 * @param int    $number The number to compare against to use either the singular or plural form.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string The translated singular or plural form.
	 * @since 5.5.0 Introduced ngettext-{$domain} filter.
	 *
	 * @since 2.8.0
	 */
	public function _n( $single, $plural, $number, $domain = 'default' );

	/**
	 * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
	 *
	 * This is a hybrid of _n() and _x(). It supports context and plurals.
	 *
	 * Used when you want to use the appropriate form of a string with context based on whether a
	 * number is singular or plural.
	 *
	 * Example of a generic phrase which is disambiguated via the context parameter:
	 *
	 *     printf( _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n(
	 *     $people ) ); printf( _nx( '%s group', '%s groups', $animals, 'group of animals', 'text-domain' ),
	 *     number_format_i18n( $animals ) );
	 *
	 * @param string $single  The text to be used if the number is singular.
	 * @param string $plural  The text to be used if the number is plural.
	 * @param int    $number  The number to compare against to use either the singular or plural form.
	 * @param string $context Context information for the translators.
	 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                        Default 'default'.
	 *
	 * @return string The translated singular or plural form.
	 * @since 2.8.0
	 * @since 5.5.0 Introduced ngettext_with_context-{$domain} filter.
	 *
	 */
	public function _nx( $single, $plural, $number, $context, $domain = 'default' );

	/**
	 * Registers plural strings in POT file, but does not translate them.
	 *
	 * Used when you want to keep structures with translatable plural
	 * strings and use them later when the number is known.
	 *
	 * Example:
	 *
	 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
	 *     ...
	 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
	 *
	 * @param string $singular Singular form to be localized.
	 * @param string $plural   Plural form to be localized.
	 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                         Default null.
	 *
	 * @return array {
	 *     Array of translation information for the strings.
	 *
	 * @type string $0        Singular form to be localized. No longer used.
	 * @type string $1        Plural form to be localized. No longer used.
	 * @type string  $singular Singular form to be localized.
	 * @type string  $plural   Plural form to be localized.
	 * @type null    $context  Context information for the translators.
	 * @type string  $domain   Text domain.
	 * }
	 * @since 2.5.0
	 *
	 */
	public function _n_noop( $singular, $plural, $domain = null );

	/**
	 * Registers plural strings with gettext context in POT file, but does not translate them.
	 *
	 * Used when you want to keep structures with translatable plural
	 * strings and use them later when the number is known.
	 *
	 * Example of a generic phrase which is disambiguated via the context parameter:
	 *
	 *     $messages = array(
	 *          'people'  => _nx_noop( '%s group', '%s groups', 'people', 'text-domain' ),
	 *          'animals' => _nx_noop( '%s group', '%s groups', 'animals', 'text-domain' ),
	 *     );
	 *     ...
	 *     $message = $messages[ $type ];
	 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
	 *
	 * @param string $singular Singular form to be localized.
	 * @param string $plural   Plural form to be localized.
	 * @param string $context  Context information for the translators.
	 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                         Default null.
	 *
	 * @return array {
	 *     Array of translation information for the strings.
	 *
	 * @type string $0        Singular form to be localized. No longer used.
	 * @type string $1        Plural form to be localized. No longer used.
	 * @type string $2        Context information for the translators. No longer used.
	 * @type string  $singular Singular form to be localized.
	 * @type string  $plural   Plural form to be localized.
	 * @type string  $context  Context information for the translators.
	 * @type string  $domain   Text domain.
	 * }
	 * @since 2.8.0
	 *
	 */
	public function _nx_noop( $singular, $plural, $context, $domain = null );

	/**
	 * Translates and retrieves the singular or plural form of a string that's been registered
	 * with _n_noop() or _nx_noop().
	 *
	 * Used when you want to use a translatable plural string once the number is known.
	 *
	 * Example:
	 *
	 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
	 *     ...
	 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
	 *
	 * @param array  $nooped_plural Array with singular, plural, and context keys, usually the result of _n_noop() or
	 *                              _nx_noop().
	 * @param int    $count         Number of objects.
	 * @param string $domain        Optional. Text domain. Unique identifier for retrieving translated strings. If
	 *                              $nooped_plural contains a text domain passed to _n_noop() or _nx_noop(), it will
	 *                              override this value. Default 'default'.
	 *
	 * @return string Either $single or $plural translated text.
	 * @since 3.1.0
	 *
	 */
	public function translate_nooped_plural( $nooped_plural, $count, $domain = 'default' );

	/**
	 * Load a .mo file into the text domain $domain.
	 *
	 * If the text domain already exists, the translations will be merged. If both
	 * sets have the same string, the translation from the original value will be taken.
	 *
	 * On success, the .mo file will be placed in the $l10n global by $domain
	 * and will be a MO object.
	 *
	 * @param string $domain        Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile        Path to the .mo file.
	 *
	 * @return bool True on success, false on failure.
	 * @since 1.5.0
	 *
	 * @global MO[]  $l10n          An array of all currently loaded text domains.
	 * @global MO[]  $l10n_unloaded An array of all text domains that have been unloaded again.
	 *
	 */
	public function load_textdomain( $domain, $mofile );

	/**
	 * Unload translations for a text domain.
	 *
	 * @param string $domain        Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return bool Whether textdomain was unloaded.
	 * @global MO[]  $l10n_unloaded An array of all text domains that have been unloaded again.
	 *
	 * @since 3.0.0
	 *
	 * @global MO[]  $l10n          An array of all currently loaded text domains.
	 */
	public function unload_textdomain( $domain );

	/**
	 * Load default translated strings based on locale.
	 *
	 * Loads the .mo file in WP_LANG_DIR constant path from WordPress root.
	 * The translated (.mo) file is named based on the locale.
	 *
	 * @param string $locale Optional. Locale to load. Default is the value of get_locale().
	 *
	 * @return bool Whether the textdomain was loaded.
	 * @see   load_textdomain()
	 *
	 * @since 1.5.0
	 *
	 */
	public function load_default_textdomain( $locale = null );

	/**
	 * Loads a plugin's translated strings.
	 *
	 * If the path is not given then it will be the root of the plugin directory.
	 *
	 * The .mo file should be named based on the text domain with a dash, and then the locale exactly.
	 *
	 * @param string       $domain          Unique identifier for retrieving translated strings
	 * @param string|false $deprecated      Optional. Deprecated. Use the $plugin_rel_path parameter instead.
	 *                                      Default false.
	 * @param string|false $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR where the .mo file resides.
	 *                                      Default false.
	 *
	 * @return bool True when textdomain is successfully loaded, false otherwise.
	 * @since 1.5.0
	 * @since 4.6.0 The public function now tries to load the .mo file from the languages directory first.
	 *
	 */
	public function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false );

	/**
	 * Load the translated strings for a plugin residing in the mu-plugins directory.
	 *
	 * @param string $domain             Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mu_plugin_rel_path Optional. Relative to `WPMU_PLUGIN_DIR` directory in which the .mo
	 *                                   file resides. Default empty string.
	 *
	 * @return bool True when textdomain is successfully loaded, false otherwise.
	 * @since 4.6.0 The public function now tries to load the .mo file from the languages directory first.
	 *
	 * @since 3.0.0
	 */
	public function load_muplugin_textdomain( $domain, $mu_plugin_rel_path = '' );

	/**
	 * Load the theme's translated strings.
	 *
	 * If the current locale exists as a .mo file in the theme's root directory, it
	 * will be included in the translated strings by the $domain.
	 *
	 * The .mo files must be named based on the locale exactly.
	 *
	 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param string|false $path   Optional. Path to the directory containing the .mo file.
	 *                             Default false.
	 *
	 * @return bool True when textdomain is successfully loaded, false otherwise.
	 * @since 4.6.0 The public function now tries to load the .mo file from the languages directory first.
	 *
	 * @since 1.5.0
	 */
	public function load_theme_textdomain( $domain, $path = false );

	/**
	 * Load the child themes translated strings.
	 *
	 * If the current locale exists as a .mo file in the child themes
	 * root directory, it will be included in the translated strings by the $domain.
	 *
	 * The .mo files must be named based on the locale exactly.
	 *
	 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param string|false $path   Optional. Path to the directory containing the .mo file.
	 *                             Default false.
	 *
	 * @return bool True when the theme textdomain is successfully loaded, false otherwise.
	 * @since 2.9.0
	 *
	 */
	public function load_child_theme_textdomain( $domain, $path = false );

	/**
	 * Loads the script translated strings.
	 *
	 * @param string $handle Name of the script to register a translation domain to.
	 * @param string $domain Optional. Text domain. Default 'default'.
	 * @param string $path   Optional. The full file path to the directory containing translation files.
	 *
	 * @return string|false The translated strings in JSON encoding on success,
	 *                      false if the script textdomain could not be loaded.
	 * @since 5.0.0
	 * @since 5.0.2 Uses load_script_translations() to load translation data.
	 * @since 5.1.0 The `$domain` parameter was made optional.
	 *
	 * @see   WP_Scripts::set_translations()
	 *
	 */
	public function load_script_textdomain( $handle, $domain = 'default', $path = null );

	/**
	 * Loads the translation data for the given script handle and text domain.
	 *
	 * @param string|false $file   Path to the translation file to load. False if there isn't one.
	 * @param string       $handle Name of the script to register a translation domain to.
	 * @param string       $domain The text domain.
	 *
	 * @return string|false The JSON-encoded translated strings for the given script handle and text domain.
	 *                      False if there are none.
	 * @since 5.0.2
	 *
	 */
	public function load_script_translations( $file, $handle, $domain );

	/**
	 * Loads plugin and theme textdomains just-in-time.
	 *
	 * When a textdomain is encountered for the first time, we try to load
	 * the translation file from `wp-content/languages`, removing the need
	 * to call load_plugin_texdomain() or load_theme_texdomain().
	 *
	 * @param string $domain        Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return bool True when the textdomain is successfully loaded, false otherwise.
	 * @global MO[]  $l10n_unloaded An array of all text domains that have been unloaded again.
	 *
	 * @since  4.6.0
	 * @access private
	 *
	 * @see    get_translations_for_domain()
	 */
	public function _load_textdomain_just_in_time( $domain );

	/**
	 * Gets the path to a translation file for loading a textdomain just in time.
	 *
	 * Caches the retrieved results internally.
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param bool   $reset  Whether to reset the internal cache. Used by the switch to locale public functionality.
	 *
	 * @return string|false The path to the translation file or false if no translation file was found.
	 * @see    _load_textdomain_just_in_time()
	 *
	 * @since  4.7.0
	 * @access private
	 *
	 */
	public function _get_path_to_translation( $domain, $reset = false );

	/**
	 * Gets the path to a translation file in the languages directory for the current locale.
	 *
	 * Holds a cached list of available .mo files to improve performance.
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return string|false The path to the translation file or false if no translation file was found.
	 * @since  4.7.0
	 * @access private
	 *
	 * @see    _get_path_to_translation()
	 *
	 */
	public function _get_path_to_translation_from_lang_dir( $domain );

	/**
	 * Return the Translations instance for a text domain.
	 *
	 * If there isn't one, returns empty Translations instance.
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return Translations|NOOP_Translations A Translations instance.
	 * @since 2.8.0
	 *
	 * @global MO[]  $l10n
	 *
	 */
	public function get_translations_for_domain( $domain );

	/**
	 * Whether there are translations for the text domain.
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return bool Whether there are translations.
	 * @since 3.0.0
	 *
	 * @global MO[]  $l10n
	 *
	 */
	public function is_textdomain_loaded( $domain );

	/**
	 * Translates role name.
	 *
	 * Since the role names are in the database and not in the source there
	 * are dummy gettext calls to get them into the POT file and this public function
	 * properly translates them back.
	 *
	 * The before_last_bar() call is needed, because older installations keep the roles
	 * using the old context format: 'Role name|User role' and just skipping the
	 * content after the last bar is easier than fixing them in the DB. New installations
	 * won't suffer from that problem.
	 *
	 * @param string $name   The role name.
	 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
	 *                       Default 'default'.
	 *
	 * @return string Translated role name on success, original name on failure.
	 * @since 5.2.0 Added the `$domain` parameter.
	 *
	 * @since 2.8.0
	 */
	public function translate_user_role( $name, $domain = 'default' );

	/**
	 * Get all available languages based on the presence of *.mo files in a given directory.
	 *
	 * The default directory is WP_LANG_DIR.
	 *
	 * @param string $dir A directory to search for language files.
	 *                    Default WP_LANG_DIR.
	 *
	 * @return string[] An array of language codes or an empty array if no languages are present. Language codes are
	 *                  formed by stripping the .mo extension from the language file names.
	 * @since 3.0.0
	 * @since 4.7.0 The results are now filterable with the {@see 'get_available_languages'} filter.
	 *
	 */
	public function get_available_languages( $dir = null );

	/**
	 * Get installed translations.
	 *
	 * Looks in the wp-content/languages directory for translations of
	 * plugins or themes.
	 *
	 * @param string $type What to search for. Accepts 'plugins', 'themes', 'core'.
	 *
	 * @return array Array of language data.
	 * @since 3.7.0
	 *
	 */
	public function wp_get_installed_translations( $type );

	/**
	 * Extract headers from a PO file.
	 *
	 * @param string $po_file Path to PO file.
	 *
	 * @return string[] Array of PO file header values keyed by header name.
	 * @since 3.7.0
	 *
	 */
	public function wp_get_pomo_file_data( $po_file );

	/**
	 * Language selector.
	 *
	 * @param string|array $args                        {
	 *                                                  Optional. Array or string of arguments for outputting the
	 *                                                  language selector.
	 *
	 * @type string        $id                          ID attribute of the select element. Default 'locale'.
	 * @type string        $name                        Name attribute of the select element. Default 'locale'.
	 * @type array         $languages                   List of installed languages, contain only the locales.
	 *                                                  Default empty array.
	 * @type array         $translations                List of available translations. Default result of
	 *                                                  wp_get_available_translations().
	 * @type string        $selected                    Language which should be selected. Default empty.
	 * @type bool|int      $echo                        Whether to echo the generated markup. Accepts 0, 1, or their
	 *                                                  boolean equivalents. Default 1.
	 * @type bool          $show_available_translations Whether to show available translations. Default true.
	 * @type bool          $show_option_site_default    Whether to show an option to fall back to the site's locale.
	 *       Default false.
	 * @type bool          $show_option_en_us           Whether to show an option for English (United States). Default
	 *       true.
	 * }
	 * @return string HTML dropdown list of languages.
	 * @since 4.7.0 Introduced the `show_option_site_default` argument.
	 * @since 5.1.0 Introduced the `show_option_en_us` argument.
	 *
	 * @see   get_available_languages()
	 * @see   wp_get_available_translations()
	 *
	 * @since 4.0.0
	 * @since 4.3.0 Introduced the `echo` argument.
	 */
	public function wp_dropdown_languages( $args = [] );

	/**
	 * Determines whether the current locale is right-to-left (RTL).
	 *
	 * For more information on this and similar theme public functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @return bool Whether locale is RTL.
	 * @global WP_Locale $wp_locale WordPress date and time locale object.
	 *
	 * @since 3.0.0
	 *
	 */
	public function is_rtl();

	/**
	 * Switches the translations according to the given locale.
	 *
	 * @param string              $locale             The locale.
	 *
	 * @return bool True on success, false on failure.
	 * @since 4.7.0
	 *
	 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
	 *
	 */
	public function switch_to_locale( $locale );

	/**
	 * Restores the translations according to the previous locale.
	 *
	 * @return string|false Locale on success, false on error.
	 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
	 *
	 * @since 4.7.0
	 *
	 */
	public function restore_previous_locale();

	/**
	 * Restores the translations according to the original locale.
	 *
	 * @return string|false Locale on success, false on error.
	 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
	 *
	 * @since 4.7.0
	 *
	 */
	public function restore_current_locale();

	/**
	 * Whether switch_to_locale() is in effect.
	 *
	 * @return bool True if the locale has been switched, false otherwise.
	 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
	 *
	 * @since 4.7.0
	 *
	 */
	public function is_locale_switched();

	/**
	 * Translates the provided settings value using its i18n schema.
	 *
	 * @param string|string[]|array[]|object $i18n_schema I18n schema for the setting.
	 * @param string|string[]|array[]        $settings    Value for the settings.
	 * @param string                         $textdomain  Textdomain to use with translations.
	 *
	 * @return string|string[]|array[] Translated settings.
	 * @since  5.9.0
	 * @access private
	 *
	 */
	public function translate_settings_using_i18n_schema( $i18n_schema, $settings, $textdomain );
}

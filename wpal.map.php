<?php declare( strict_types=1 );
/**
 * Where WordPress functions belong in WPAL. `bin/wpal fix` generates `src/Service` from this file and the
 * WordPress snapshot in `api/wordpress.json`; `bin/wpal coverage` reports on it.
 *
 * - `services`: generated services and the WordPress functions they wrap, in order.
 * - `planned`: where functions will go once they're moved into `services`.
 * - `types`: native types `Service` declares although WordPress doesn't. Kept so existing implementations stay
 *   compatible; never add new ones.
 * - `extendable`: `Wp*` classes that stay non-final because they shipped that way.
 * - `ignore`, `ignore_files`: functions WPAL deliberately doesn't wrap. Private (`_`-prefixed or `@access private`)
 *   and deprecated functions are ignored automatically.
 *
 * Everything else shows up as untriaged.
 */

return [
	// Service => WordPress function names, in the order the methods are generated.
	'services'     => [
		'Assets'          => [
			'wp_enqueue_script', 'wp_add_inline_script', 'wp_enqueue_style',
		],
		'Capabilities'    => [
			'map_meta_cap', 'current_user_can', 'current_user_can_for_blog', 'author_can', 'user_can', 'wp_roles',
			'get_role', 'add_role', 'remove_role', 'get_super_admins', 'is_super_admin', 'grant_super_admin',
			'revoke_super_admin', 'wp_maybe_grant_install_languages_cap', 'wp_maybe_grant_resume_extensions_caps',
			'wp_maybe_grant_site_health_caps',
		],
		'Comments'        => [
			'check_comment', 'get_approved_comments', 'get_comment', 'get_comments', 'get_comment_statuses',
			'get_default_comment_status', 'get_lastcommentmodified', 'get_comment_count', 'add_comment_meta',
			'delete_comment_meta', 'get_comment_meta', 'update_comment_meta',
			'wp_queue_comments_for_comment_meta_lazyload', 'wp_set_comment_cookies', 'sanitize_comment_cookies',
			'wp_allow_comment', 'check_comment_flood_db', 'wp_check_comment_flood', 'separate_comments',
			'get_comment_pages_count', 'get_page_of_comment', 'wp_get_comment_fields_max_lengths',
			'wp_check_comment_data_max_lengths', 'wp_check_comment_disallowed_list', 'wp_count_comments',
			'wp_delete_comment', 'wp_trash_comment', 'wp_untrash_comment', 'wp_spam_comment', 'wp_unspam_comment',
			'wp_get_comment_status', 'wp_transition_comment_status', 'wp_get_current_commenter',
			'wp_get_unapproved_comment_author_email', 'wp_insert_comment', 'wp_filter_comment',
			'wp_throttle_comment_flood', 'wp_new_comment', 'wp_new_comment_notify_moderator',
			'wp_new_comment_notify_postauthor', 'wp_set_comment_status', 'wp_update_comment',
			'wp_defer_comment_counting', 'wp_update_comment_count', 'wp_update_comment_count_now',
			'discover_pingback_server_uri', 'do_all_pings', 'do_all_pingbacks', 'do_all_enclosures',
			'do_all_trackbacks', 'do_trackbacks', 'generic_ping', 'pingback', 'privacy_ping_filter', 'trackback',
			'weblog_ping', 'pingback_ping_source_uri', 'xmlrpc_pingback_error', 'clean_comment_cache',
			'update_comment_cache', 'wp_handle_comment_submission', 'wp_register_comment_personal_data_exporter',
			'wp_comments_personal_data_exporter', 'wp_register_comment_personal_data_eraser',
			'wp_comments_personal_data_eraser', 'wp_cache_set_comments_last_changed', '_wp_batch_update_comment_type',
			'_wp_check_for_scheduled_update_comment_type',
		],
		'Hooks'           => [
			'add_filter', 'apply_filters', 'apply_filters_ref_array', 'has_filter', 'remove_filter',
			'remove_all_filters', 'current_filter', 'doing_filter', 'add_action', 'do_action', 'do_action_ref_array',
			'has_action', 'remove_action', 'remove_all_actions', 'current_action', 'doing_action', 'did_action',
			'apply_filters_deprecated', 'do_action_deprecated',
		],
		'Localization'    => [
			'get_locale', 'get_user_locale', 'determine_locale', 'translate', 'before_last_bar',
			'translate_with_gettext_context', '__', 'esc_attr__', 'esc_html__', '_e', 'esc_attr_e', 'esc_html_e', '_x',
			'_ex', 'esc_attr_x', 'esc_html_x', '_n', '_nx', '_n_noop', '_nx_noop', 'translate_nooped_plural',
			'load_textdomain', 'unload_textdomain', 'load_default_textdomain', 'load_plugin_textdomain',
			'load_muplugin_textdomain', 'load_theme_textdomain', 'load_child_theme_textdomain',
			'load_script_textdomain', 'load_script_translations', '_load_textdomain_just_in_time',
			'_get_path_to_translation', '_get_path_to_translation_from_lang_dir', 'get_translations_for_domain',
			'is_textdomain_loaded', 'translate_user_role', 'get_available_languages', 'wp_get_installed_translations',
			'wp_get_pomo_file_data', 'wp_dropdown_languages', 'is_rtl', 'switch_to_locale', 'restore_previous_locale',
			'restore_current_locale', 'is_locale_switched', 'translate_settings_using_i18n_schema',
		],
		'Plugins'         => [
			'plugin_basename', 'wp_register_plugin_realpath', 'plugin_dir_path', 'plugin_dir_url',
			'register_activation_hook', 'register_deactivation_hook', 'register_uninstall_hook', 'get_plugin_data',
			'_get_plugin_data_markup_translate', 'get_plugin_files', 'get_plugins', 'get_mu_plugins',
			'_sort_uname_callback', 'get_dropins', '_get_dropins', 'is_plugin_active', 'is_plugin_inactive',
			'is_plugin_active_for_network', 'is_network_only_plugin', 'activate_plugin', 'deactivate_plugins',
			'activate_plugins', 'delete_plugins', 'validate_active_plugins', 'validate_plugin',
			'validate_plugin_requirements', 'is_uninstallable_plugin', 'uninstall_plugin', 'add_menu_page',
			'add_submenu_page', 'add_management_page', 'add_options_page', 'add_theme_page', 'add_plugins_page',
			'add_users_page', 'add_dashboard_page', 'add_posts_page', 'add_media_page', 'add_links_page',
			'add_pages_page', 'add_comments_page', 'remove_menu_page', 'remove_submenu_page', 'menu_page_url',
			'get_admin_page_parent', 'get_admin_page_title', 'get_plugin_page_hook', 'get_plugin_page_hookname',
			'user_can_access_admin_page', 'option_update_filter', 'add_allowed_options', 'remove_allowed_options',
			'settings_fields', 'wp_clean_plugins_cache', 'plugin_sandbox_scrape', 'wp_add_privacy_policy_content',
			'is_plugin_paused', 'wp_get_plugin_error', 'resume_plugin', 'paused_plugins_notice',
			'deactivated_plugins_notice',
		],
		'PostAttachments' => [
			'get_attached_file', 'update_attached_file', 'wp_count_attachments', 'is_local_attachment',
			'wp_insert_attachment', 'wp_delete_attachment', 'wp_delete_attachment_files', 'wp_get_attachment_metadata',
			'wp_update_attachment_metadata', 'wp_get_attachment_url', 'wp_get_attachment_caption',
			'wp_get_attachment_thumb_file', 'wp_get_attachment_thumb_url', 'wp_attachment_is', 'wp_attachment_is_image',
			'clean_attachment_cache',
		],
		'PostMeta'        => [
			'add_post_meta', 'delete_post_meta', 'get_post_meta', 'update_post_meta', 'delete_post_meta_by_key',
			'register_post_meta', 'unregister_post_meta', 'get_post_custom', 'get_post_custom_keys',
			'get_post_custom_values', 'update_postmeta_cache',
		],
		'PostStatuses'    => [
			'get_post_status', 'get_post_statuses', 'get_page_statuses', 'register_post_status',
			'get_post_status_object', 'get_post_stati', 'is_post_status_viewable',
		],
		'PostTypes'       => [
			'is_post_type_hierarchical', 'post_type_exists', 'get_post_type', 'get_post_type_object', 'get_post_types',
			'register_post_type', 'unregister_post_type', 'get_post_type_capabilities', 'add_post_type_support',
			'remove_post_type_support', 'get_all_post_type_supports', 'post_type_supports', 'get_post_types_by_support',
			'set_post_type', 'is_post_type_viewable',
		],
		'Posts'           => [
			'get_children', 'get_extended', 'get_post', 'get_post_ancestors', 'get_post_field', 'get_post_mime_type',
			'is_post_publicly_viewable', 'get_posts', 'is_sticky', 'sanitize_post', 'sanitize_post_field', 'stick_post',
			'unstick_post', 'wp_count_posts', 'get_post_mime_types', 'wp_match_mime_types', 'wp_post_mime_type_where',
			'wp_delete_post', 'wp_trash_post', 'wp_untrash_post', 'wp_trash_post_comments', 'wp_untrash_post_comments',
			'wp_get_post_categories', 'wp_get_post_tags', 'wp_get_post_terms', 'wp_get_recent_posts', 'wp_insert_post',
			'wp_update_post', 'wp_publish_post', 'check_and_publish_future_post', 'wp_resolve_post_date',
			'wp_unique_post_slug', 'wp_add_post_tags', 'wp_set_post_tags', 'wp_set_post_terms',
			'wp_set_post_categories', 'wp_transition_post_status', 'wp_after_insert_post', 'add_ping', 'get_enclosed',
			'get_pung', 'get_to_ping', 'trackback_url_list', 'get_all_page_ids', 'get_page', 'get_page_by_path',
			'get_page_by_title', 'get_page_children', 'get_page_hierarchy', 'get_page_uri', 'get_pages',
			'wp_mime_type_icon', 'wp_check_for_changed_slugs', 'wp_check_for_changed_dates',
			'get_private_posts_cap_sql', 'get_posts_by_author_sql', 'get_lastpostdate', 'get_lastpostmodified',
			'update_post_cache', 'clean_post_cache', 'update_post_caches', 'wp_get_post_parent_id',
			'wp_check_post_hierarchy_for_loops', 'set_post_thumbnail', 'delete_post_thumbnail', 'wp_delete_auto_drafts',
			'wp_queue_posts_for_term_meta_lazyload', 'wp_cache_set_posts_last_changed', 'get_available_post_mime_types',
			'wp_get_original_image_path', 'wp_get_original_image_url', 'wp_untrash_post_set_previous_status',
		],
		'Screen'          => [
			'get_current_screen', 'set_current_screen', 'add_screen_option',
		],
		'Taxonomies'      => [
			'create_initial_taxonomies', 'get_taxonomies', 'get_object_taxonomies', 'get_taxonomy', 'taxonomy_exists',
			'is_taxonomy_hierarchical', 'register_taxonomy', 'unregister_taxonomy', 'get_taxonomy_labels',
			'register_taxonomy_for_object_type', 'unregister_taxonomy_for_object_type', 'get_objects_in_term',
			'get_tax_sql', 'get_term', 'get_term_by', 'get_term_children', 'get_term_field', 'get_term_to_edit',
			'get_terms', 'add_term_meta', 'delete_term_meta', 'get_term_meta', 'update_term_meta',
			'update_termmeta_cache', 'has_term_meta', 'register_term_meta', 'unregister_term_meta', 'term_exists',
			'term_is_ancestor_of', 'sanitize_term', 'sanitize_term_field', 'wp_count_terms',
			'wp_delete_object_term_relationships', 'wp_delete_term', 'wp_delete_category', 'wp_get_object_terms',
			'wp_insert_term', 'wp_set_object_terms', 'wp_add_object_terms', 'wp_remove_object_terms',
			'wp_unique_term_slug', 'wp_update_term', 'wp_defer_term_counting', 'wp_update_term_count',
			'wp_update_term_count_now', 'clean_object_term_cache', 'clean_term_cache', 'clean_taxonomy_cache',
			'get_object_term_cache', 'update_object_term_cache', 'update_term_cache', '_update_generic_term_count',
			'_split_shared_term', '_wp_batch_split_terms', '_wp_check_for_scheduled_split_terms',
			'_wp_check_split_default_terms', '_wp_check_split_terms_in_menus', '_wp_check_split_nav_menu_terms',
			'wp_get_split_terms', 'wp_get_split_term', 'wp_term_is_shared', 'get_term_link', 'the_taxonomies',
			'get_the_taxonomies', 'get_post_taxonomies', 'is_object_in_term', 'is_object_in_taxonomy', 'get_ancestors',
			'wp_get_term_taxonomy_parent_id', 'wp_check_term_hierarchy_for_loops', 'is_taxonomy_viewable',
			'wp_cache_set_terms_last_changed', 'wp_check_term_meta_support_prefilter',
		],
		'Transient'       => [
			'get_transient', 'set_transient', 'delete_transient',
		],
	],

	// Service => function names, or /regex/ matched against the name.
	'planned'      => [
		// Admin screens: form widgets and page chrome from wp-admin/includes/template.php.
		'AdminTemplate'   => [
			'compression_test', 'find_posts_div', 'get_inline_data', 'get_media_states', 'get_post_states',
			'get_submit_button', 'iframe_footer', 'iframe_header', 'list_meta', 'meta_form', 'page_template_dropdown',
			'parent_dropdown', 'register_admin_color_schemes', 'submit_button', 'the_post_password', 'touch_time',
			'wp_admin_css', 'wp_admin_css_color', 'wp_admin_css_uri', 'wp_category_checklist', 'wp_comment_reply',
			'wp_comment_trashnotice', 'wp_dropdown_roles', 'wp_heartbeat_settings', 'wp_import_upload_form',
			'wp_link_category_checklist', 'wp_popular_terms_checklist', 'wp_star_rating', 'wp_terms_checklist',
		],
		'ArchiveTemplate' => [
			'calendar_week_mod', 'delete_get_calendar_cache', 'get_archives_link', 'get_calendar',
			'get_the_archive_description', 'get_the_archive_title', 'get_the_post_type_description',
			'post_type_archive_title', 'single_cat_title', 'single_month_title', 'single_post_title',
			'single_tag_title', 'single_term_title', 'the_archive_description', 'the_archive_title', 'wp_get_archives',
		],
		'Assets'          => [
			'add_thickbox',
		],
		'AuthorTemplate'  => [
			'get_author_posts_url', 'get_the_author', 'get_the_author_link', 'get_the_author_meta',
			'get_the_author_posts', 'get_the_author_posts_link', 'get_the_modified_author', 'is_multi_author',
			'the_author', 'the_author_link', 'the_author_meta', 'the_author_posts', 'the_author_posts_link',
			'the_modified_author', 'wp_list_authors',
		],
		'Avatars'         => [
			'get_avatar_data', 'get_avatar_url', 'is_avatar_comment_type',
		],
		'BlockTemplates'  => [
			'block_footer_area', 'block_header_area', 'block_template_part', 'get_allowed_block_template_part_areas',
			'get_block_file_template', 'get_block_template', 'get_block_templates', 'get_block_theme_folders',
			'get_default_block_template_types', 'get_template_hierarchy', 'locate_block_template',
			'register_block_template', 'unregister_block_template', 'wp_generate_block_templates_export_file',
			'wp_is_theme_directory_ignored', 'wp_render_empty_block_template_warning',
			'wp_set_unique_slug_on_create_template_part',
		],
		'Bookmarks'       => [
			'wp_list_bookmarks',
		],
		'CommentTemplate' => [
			'cancel_comment_reply_link', 'comment_author', 'comment_author_email', 'comment_author_email_link',
			'comment_author_ip', 'comment_author_link', 'comment_author_url', 'comment_author_url_link',
			'comment_class', 'comment_date', 'comment_excerpt', 'comment_form', 'comment_form_title', 'comment_id',
			'comment_id_fields', 'comment_reply_link', 'comment_text', 'comment_time', 'comment_type', 'comments_link',
			'comments_number', 'comments_open', 'comments_popup_link', 'comments_template',
			'get_cancel_comment_reply_link', 'get_comment_author', 'get_comment_author_email',
			'get_comment_author_email_link', 'get_comment_author_ip', 'get_comment_author_link',
			'get_comment_author_url', 'get_comment_author_url_link', 'get_comment_class', 'get_comment_date',
			'get_comment_excerpt', 'get_comment_id', 'get_comment_id_fields', 'get_comment_link',
			'get_comment_reply_link', 'get_comment_text', 'get_comment_time', 'get_comment_type', 'get_comments_link',
			'get_comments_number', 'get_comments_number_text', 'get_post_reply_link', 'get_trackback_url', 'pings_open',
			'post_reply_link', 'trackback_rdf', 'trackback_url', 'wp_comment_form_unfiltered_html_nonce',
			'wp_list_comments',
		],
		// What goes in <head> and around the page: wp_head(), titles, resource hints, robots.
		'DocumentHead'    => [
			'adjacent_posts_rel_link_wp_head', 'get_the_generator', 'rel_canonical', 'rsd_link', 'the_generator',
			'wp_body_open', 'wp_dependencies_unique_hosts', 'wp_footer', 'wp_generator', 'wp_get_document_title',
			'wp_head', 'wp_meta', 'wp_preload_resources', 'wp_resource_hints', 'wp_robots',
			'wp_robots_max_image_preview_large', 'wp_robots_no_robots', 'wp_robots_noindex', 'wp_robots_noindex_embeds',
			'wp_robots_noindex_search', 'wp_robots_sensitive_page', 'wp_shortlink_header', 'wp_shortlink_wp_head',
			'wp_strict_cross_origin_referrer', 'wp_title',
		],
		'Editor'          => [
			'user_can_richedit', 'wp_default_editor', 'wp_editor', 'wp_enqueue_code_editor', 'wp_enqueue_editor',
			'wp_get_code_editor_settings',
		],
		'EditLinks'       => [
			'edit_bookmark_link', 'edit_comment_link', 'edit_post_link', 'edit_tag_link', 'edit_term_link',
			'get_delete_post_link', 'get_edit_bookmark_link', 'get_edit_comment_link', 'get_edit_post_link',
			'get_edit_tag_link', 'get_edit_term_link', 'get_edit_user_link',
		],
		// Feed links, plus the template tags feed templates use (wp-includes/feed.php).
		'Feeds'           => [
			'atom_enclosure', 'atom_site_icon', 'bloginfo_rss', 'comment_author_rss', 'comment_guid', 'comment_link',
			'comment_text_rss', 'comments_link_feed', 'feed_content_type', 'feed_links', 'feed_links_extra',
			'fetch_feed', 'get_author_feed_link', 'get_bloginfo_rss', 'get_category_feed_link',
			'get_comment_author_rss', 'get_comment_guid', 'get_default_feed', 'get_feed_build_date', 'get_feed_link',
			'get_post_comments_feed_link', 'get_post_type_archive_feed_link', 'get_search_comments_feed_link',
			'get_search_feed_link', 'get_self_link', 'get_tag_feed_link', 'get_term_feed_link', 'get_the_category_rss',
			'get_the_content_feed', 'get_the_title_rss', 'get_wp_title_rss', 'html_type_rss', 'post_comments_feed_link',
			'prep_atom_text_construct', 'rss2_site_icon', 'rss_enclosure', 'self_link', 'the_category_rss',
			'the_content_feed', 'the_excerpt_rss', 'the_feed_link', 'the_permalink_rss', 'the_title_rss',
			'wp_title_rss',
		],
		// Small form helpers: checked(), selected(), required-field markers, tooltips.
		'Forms'           => [
			'allowed_tags', 'checked', 'disabled', 'selected', 'wp_get_toggletip', 'wp_get_tooltip',
			'wp_get_tooltip_helper', 'wp_readonly', 'wp_required_field_indicator', 'wp_required_field_message',
		],
		'Login'           => [
			'wp_login_form', 'wp_login_url', 'wp_loginout', 'wp_logout_url', 'wp_lostpassword_url', 'wp_register',
			'wp_registration_url',
		],
		'MediaTemplate'   => [
			'wp_print_media_templates', 'wp_underscore_audio_template', 'wp_underscore_video_template',
		],
		'MetaBoxes'       => [
			'add_meta_box', 'do_accordion_sections', 'do_block_editor_incompatible_meta_box', 'do_meta_boxes',
			'remove_meta_box',
		],
		// Moving between posts, pages of posts and pages of comments.
		'Navigation'      => [
			'adjacent_post_link', 'adjacent_posts_rel_link', 'get_adjacent_post', 'get_adjacent_post_link',
			'get_adjacent_post_rel_link', 'get_boundary_post', 'get_comments_pagenum_link', 'get_next_comments_link',
			'get_next_post', 'get_next_post_link', 'get_next_posts_link', 'get_next_posts_page_link',
			'get_pagenum_link', 'get_posts_nav_link', 'get_previous_comments_link', 'get_previous_post',
			'get_previous_post_link', 'get_previous_posts_link', 'get_previous_posts_page_link',
			'get_the_comments_navigation', 'get_the_comments_pagination', 'get_the_post_navigation',
			'get_the_posts_navigation', 'get_the_posts_pagination', 'next_comments_link', 'next_post_link',
			'next_post_rel_link', 'next_posts', 'next_posts_link', 'paginate_comments_links', 'paginate_links',
			'posts_nav_link', 'prev_post_rel_link', 'previous_comments_link', 'previous_post_link', 'previous_posts',
			'previous_posts_link', 'the_comments_navigation', 'the_comments_pagination', 'the_post_navigation',
			'the_posts_navigation', 'the_posts_pagination', 'wp_link_pages',
		],
		'NavMenus'        => [
			'walk_nav_menu_tree', 'wp_nav_menu', 'wp_nav_menu_remove_menu_item_has_children_class',
		],
		'Permalinks'      => [
			'get_attachment_link', 'get_category_link', 'get_day_link', 'get_month_link', 'get_page_link',
			'get_permalink', 'get_post_permalink', 'get_post_type_archive_link', 'get_preview_post_link',
			'get_tag_link', 'get_the_permalink', 'get_year_link', 'permalink_anchor', 'the_permalink', 'the_shortlink',
			'wp_force_plain_post_permalink', 'wp_get_canonical_url', 'wp_get_shortlink',
		],
		'PostTemplate'    => [
			'body_class', 'get_body_class', 'get_page_template_slug', 'get_post_class', 'get_post_datetime',
			'get_post_modified_time', 'get_post_parent', 'get_post_time', 'get_post_timestamp', 'get_the_content',
			'get_the_date', 'get_the_excerpt', 'get_the_guid', 'get_the_id', 'get_the_modified_date',
			'get_the_modified_time', 'get_the_password_form', 'get_the_time', 'get_the_title', 'has_excerpt',
			'has_post_parent', 'is_page_template', 'post_class', 'post_custom', 'post_password_required',
			'prepend_attachment', 'the_attachment_link', 'the_content', 'the_date', 'the_date_xml', 'the_excerpt',
			'the_guid', 'the_id', 'the_modified_date', 'the_modified_time', 'the_time', 'the_title',
			'the_title_attribute', 'the_weekday', 'the_weekday_date', 'walk_page_dropdown_tree', 'walk_page_tree',
			'wp_dropdown_pages', 'wp_get_attachment_link', 'wp_list_pages', 'wp_list_post_revisions', 'wp_page_menu',
			'wp_post_revision_title', 'wp_post_revision_title_expanded',
		],
		'PostThumbnails'  => [
			'get_post_thumbnail_id', 'get_the_post_thumbnail', 'get_the_post_thumbnail_caption',
			'get_the_post_thumbnail_url', 'has_post_thumbnail', 'the_post_thumbnail', 'the_post_thumbnail_caption',
			'the_post_thumbnail_url', 'update_post_thumbnail_cache',
		],
		'Screen'          => [
			'convert_to_screen',
		],
		'Search'          => [
			'get_search_form', 'get_search_link', 'get_search_query', 'the_search_query',
		],
		'Settings'        => [
			'add_settings_error', 'add_settings_field', 'add_settings_section', 'do_settings_fields',
			'do_settings_sections', 'get_settings_errors', 'settings_errors',
		],
		'SiteIdentity'    => [
			'bloginfo', 'get_bloginfo', 'get_custom_logo', 'get_language_attributes', 'get_site_icon_url',
			'has_custom_logo', 'has_site_icon', 'language_attributes', 'site_icon_url', 'the_custom_logo',
			'wp_site_icon',
		],
		// Theme template files: the template hierarchy and template parts.
		'Templates'       => [
			'get_404_template', 'get_archive_template', 'get_attachment_template', 'get_author_template',
			'get_category_template', 'get_date_template', 'get_embed_template', 'get_footer', 'get_front_page_template',
			'get_header', 'get_home_template', 'get_index_template', 'get_page_template', 'get_parent_theme_file_path',
			'get_parent_theme_file_uri', 'get_post_type_archive_template', 'get_privacy_policy_template',
			'get_query_template', 'get_search_template', 'get_sidebar', 'get_single_template', 'get_singular_template',
			'get_tag_template', 'get_taxonomy_template', 'get_template_part', 'get_theme_file_path',
			'get_theme_file_uri', 'load_template', 'locate_template', 'wp_finalize_template_enhancement_output_buffer',
			'wp_set_template_globals', 'wp_should_output_buffer_template_for_enhancement',
			'wp_start_template_enhancement_output_buffer',
		],
		'TermTemplate'    => [
			'category_description', 'default_topic_count_scale', 'get_category_parents', 'get_term_parents_list',
			'get_the_category', 'get_the_category_by_id', 'get_the_category_list', 'get_the_tag_list', 'get_the_tags',
			'get_the_term_list', 'get_the_terms', 'has_category', 'has_tag', 'has_term', 'in_category',
			'tag_description', 'term_description', 'the_category', 'the_tags', 'the_terms',
			'walk_category_dropdown_tree', 'walk_category_tree', 'wp_dropdown_categories', 'wp_generate_tag_cloud',
			'wp_list_categories', 'wp_tag_cloud',
		],
		'Urls'            => [
			'admin_url', 'content_url', 'get_admin_url', 'get_dashboard_url', 'get_edit_profile_url', 'get_home_url',
			'get_privacy_policy_url', 'get_site_url', 'get_the_privacy_policy_link', 'home_url', 'includes_url',
			'network_admin_url', 'network_home_url', 'network_site_url', 'plugins_url', 'self_admin_url',
			'set_url_scheme', 'site_url', 'the_privacy_policy_link', 'user_admin_url', 'user_trailingslashit',
			'wp_internal_hosts', 'wp_is_internal_link',
		],
	],

	// Function => [ parameter => type, 'return' => type ].
	'types'        => [
		'add_action' => [ 'hook_name' => 'string', 'callback' => 'callable', 'priority' => 'int', 'accepted_args' => 'int', 'return' => 'bool' ],
		'add_filter' => [ 'hook_name' => 'string', 'callback' => 'callable', 'priority' => 'int', 'accepted_args' => 'int', 'return' => 'bool' ],
		'apply_filters' => [ 'hook_name' => 'string' ],
		'apply_filters_deprecated' => [ 'hook_name' => 'string', 'args' => 'array', 'version' => 'string', 'replacement' => 'string', 'message' => 'string' ],
		'apply_filters_ref_array' => [ 'hook_name' => 'string', 'args' => 'array' ],
		'delete_transient' => [ 'transient' => 'string', 'return' => 'bool' ],
		'did_action' => [ 'hook_name' => 'string', 'return' => 'int' ],
		'do_action' => [ 'hook_name' => 'string' ],
		'do_action_deprecated' => [ 'hook_name' => 'string', 'args' => 'array', 'version' => 'string', 'replacement' => 'string', 'message' => 'string' ],
		'do_action_ref_array' => [ 'hook_name' => 'string', 'args' => 'array' ],
		'doing_action' => [ 'return' => 'bool' ],
		'doing_filter' => [ 'return' => 'bool' ],
		'get_attached_file' => [ 'attachment_id' => 'int' ],
		'get_children' => [ 'output' => 'string' ],
		'get_transient' => [ 'transient' => 'string' ],
		'has_action' => [ 'hook_name' => 'string' ],
		'has_filter' => [ 'hook_name' => 'string' ],
		'plugin_basename' => [ 'file' => 'string', 'return' => 'string' ],
		'plugin_dir_path' => [ 'file' => 'string', 'return' => 'string' ],
		'plugin_dir_url' => [ 'file' => 'string', 'return' => 'string' ],
		'register_activation_hook' => [ 'file' => 'string', 'callback' => 'callable' ],
		'register_deactivation_hook' => [ 'file' => 'string', 'callback' => 'callable' ],
		'register_post_meta' => [ 'args' => 'array' ],
		'register_term_meta' => [ 'args' => 'array' ],
		'register_uninstall_hook' => [ 'file' => 'string', 'callback' => 'callable' ],
		'remove_action' => [ 'hook_name' => 'string', 'callback' => 'callable', 'priority' => 'int', 'return' => 'bool' ],
		'remove_all_actions' => [ 'hook_name' => 'string' ],
		'remove_all_filters' => [ 'hook_name' => 'string', 'return' => 'bool' ],
		'remove_filter' => [ 'hook_name' => 'string', 'callback' => 'callable', 'priority' => 'int', 'return' => 'bool' ],
		'set_transient' => [ 'transient' => 'string', 'expiration' => 'int', 'return' => 'bool' ],
		'update_attached_file' => [ 'attachment_id' => 'int', 'file' => 'string' ],
		'wp_add_inline_script' => [ 'handle' => 'string', 'data' => 'string', 'position' => 'string', 'return' => 'bool' ],
		'wp_enqueue_script' => [ 'handle' => 'string', 'src' => 'string', 'deps' => 'array', 'return' => 'void' ],
		'wp_enqueue_style' => [ 'handle' => 'string', 'src' => 'string', 'deps' => 'array', 'media' => 'string', 'return' => 'void' ],
		'wp_register_plugin_realpath' => [ 'file' => 'string', 'return' => 'bool' ],
	],

	'extendable'   => [ 'Assets', 'Capabilities', 'Comments' ],

	// Function name, or /regex/ matched against it => reason.
	'ignore'       => [
		'/^wp_ajax_/'     => 'admin AJAX handler',
		'/^upgrade_\d+$/' => 'database upgrade step',
	],

	// Source file, or a directory ending in "/" => reason.
	'ignore_files' => [
		'wp-includes/compat.php' => 'PHP polyfill',
		'wp-includes/blocks/'    => 'core block internals',
	],
];

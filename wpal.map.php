<?php declare( strict_types=1 );
/**
 * Where WordPress functions belong in WPAL. Read by `bin/wpal coverage`; the Phase 2 generator will build
 * `Service` from it.
 *
 * - `services`: the service each function belongs to. Functions already in `src/Service` don't need listing;
 *   listed ones that aren't wrapped yet show up as "planned".
 * - `ignore`: functions WPAL deliberately doesn't wrap. Private (`_`-prefixed or `@access private`) and deprecated
 *   functions are ignored automatically.
 *
 * Everything else shows up as untriaged.
 */

return [
	// Service => function names, or /regex/ matched against the name.
	'services'     => [
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
			'post_type_archive_title', 'single_cat_title', 'single_month_title', 'single_post_title', 'single_tag_title',
			'single_term_title', 'the_archive_description', 'the_archive_title', 'wp_get_archives',
		],
		'Assets'          => [ 'add_thickbox' ],
		'AuthorTemplate'  => [
			'get_author_posts_url', 'get_the_author', 'get_the_author_link', 'get_the_author_meta',
			'get_the_author_posts', 'get_the_author_posts_link', 'get_the_modified_author', 'is_multi_author',
			'the_author', 'the_author_link', 'the_author_meta', 'the_author_posts', 'the_author_posts_link',
			'the_modified_author', 'wp_list_authors',
		],
		'Avatars'         => [ 'get_avatar_data', 'get_avatar_url', 'is_avatar_comment_type' ],
		'BlockTemplates'  => [
			'block_footer_area', 'block_header_area', 'block_template_part', 'get_allowed_block_template_part_areas',
			'get_block_file_template', 'get_block_template', 'get_block_templates', 'get_block_theme_folders',
			'get_default_block_template_types', 'get_template_hierarchy', 'locate_block_template',
			'register_block_template', 'unregister_block_template', 'wp_generate_block_templates_export_file',
			'wp_is_theme_directory_ignored', 'wp_render_empty_block_template_warning',
			'wp_set_unique_slug_on_create_template_part',
		],
		'Bookmarks'       => [ 'wp_list_bookmarks' ],
		'CommentTemplate' => [
			'cancel_comment_reply_link', 'comment_author', 'comment_author_email', 'comment_author_email_link',
			'comment_author_ip', 'comment_author_link', 'comment_author_url', 'comment_author_url_link', 'comment_class',
			'comment_date', 'comment_excerpt', 'comment_form', 'comment_form_title', 'comment_id', 'comment_id_fields',
			'comment_reply_link', 'comment_text', 'comment_time', 'comment_type', 'comments_link', 'comments_number',
			'comments_open', 'comments_popup_link', 'comments_template', 'get_cancel_comment_reply_link',
			'get_comment_author', 'get_comment_author_email', 'get_comment_author_email_link', 'get_comment_author_ip',
			'get_comment_author_link', 'get_comment_author_url', 'get_comment_author_url_link', 'get_comment_class',
			'get_comment_date', 'get_comment_excerpt', 'get_comment_id', 'get_comment_id_fields', 'get_comment_link',
			'get_comment_reply_link', 'get_comment_text', 'get_comment_time', 'get_comment_type', 'get_comments_link',
			'get_comments_number', 'get_comments_number_text', 'get_post_reply_link', 'get_trackback_url',
			'pings_open', 'post_reply_link', 'trackback_rdf', 'trackback_url', 'wp_comment_form_unfiltered_html_nonce',
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
			'get_search_feed_link', 'get_self_link', 'get_tag_feed_link', 'get_term_feed_link',
			'get_the_category_rss', 'get_the_content_feed', 'get_the_title_rss', 'get_wp_title_rss', 'html_type_rss',
			'post_comments_feed_link', 'prep_atom_text_construct', 'rss2_site_icon', 'rss_enclosure', 'self_link',
			'the_category_rss', 'the_content_feed', 'the_excerpt_rss', 'the_feed_link', 'the_permalink_rss',
			'the_title_rss', 'wp_title_rss',
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
		'MediaTemplate'   => [ 'wp_print_media_templates', 'wp_underscore_audio_template', 'wp_underscore_video_template' ],
		'MetaBoxes'       => [
			'add_meta_box', 'do_accordion_sections', 'do_block_editor_incompatible_meta_box', 'do_meta_boxes',
			'remove_meta_box',
		],
		// Moving between posts, pages of posts and pages of comments.
		'Navigation'      => [
			'adjacent_post_link', 'adjacent_posts_rel_link', 'get_adjacent_post', 'get_adjacent_post_link',
			'get_adjacent_post_rel_link', 'get_boundary_post', 'get_comments_pagenum_link', 'get_next_comments_link',
			'get_next_post', 'get_next_post_link', 'get_next_posts_link', 'get_next_posts_page_link', 'get_pagenum_link',
			'get_posts_nav_link', 'get_previous_comments_link', 'get_previous_post', 'get_previous_post_link',
			'get_previous_posts_link', 'get_previous_posts_page_link', 'get_the_comments_navigation',
			'get_the_comments_pagination', 'get_the_post_navigation', 'get_the_posts_navigation',
			'get_the_posts_pagination', 'next_comments_link', 'next_post_link', 'next_post_rel_link', 'next_posts',
			'next_posts_link', 'paginate_comments_links', 'paginate_links', 'posts_nav_link', 'prev_post_rel_link',
			'previous_comments_link', 'previous_post_link', 'previous_posts', 'previous_posts_link',
			'the_comments_navigation', 'the_comments_pagination', 'the_post_navigation', 'the_posts_navigation',
			'the_posts_pagination', 'wp_link_pages',
		],
		'NavMenus'        => [ 'walk_nav_menu_tree', 'wp_nav_menu', 'wp_nav_menu_remove_menu_item_has_children_class' ],
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
		'Screen'          => [ 'convert_to_screen' ],
		'Search'          => [ 'get_search_form', 'get_search_link', 'get_search_query', 'the_search_query' ],
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
			'tag_description', 'term_description', 'the_category', 'the_tags', 'the_terms', 'walk_category_dropdown_tree',
			'walk_category_tree', 'wp_dropdown_categories', 'wp_generate_tag_cloud', 'wp_list_categories',
			'wp_tag_cloud',
		],
		'Urls'            => [
			'admin_url', 'content_url', 'get_admin_url', 'get_dashboard_url', 'get_edit_profile_url', 'get_home_url',
			'get_privacy_policy_url', 'get_site_url', 'get_the_privacy_policy_link', 'home_url', 'includes_url',
			'network_admin_url', 'network_home_url', 'network_site_url', 'plugins_url', 'self_admin_url',
			'set_url_scheme', 'site_url', 'the_privacy_policy_link', 'user_admin_url', 'user_trailingslashit',
			'wp_internal_hosts', 'wp_is_internal_link',
		],
	],

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

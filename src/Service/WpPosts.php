<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

final class WpPosts implements Posts {
	/**
	 * @inheritDoc
	 */
	public function get_children( $args = '', string $output = 'OBJECT' ) {
		return get_children( $args, $output );
	}

	/**
	 * @inheritDoc
	 */
	public function get_extended( $post ) {
		return get_extended( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post( $post = null, $output = 'OBJECT', $filter = 'raw' ) {
		return get_post( $post, $output, $filter );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_ancestors( $post ) {
		return get_post_ancestors( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_field( $field, $post = null, $context = 'display' ) {
		return get_post_field( $field, $post, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_mime_type( $post = null ) {
		return get_post_mime_type( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function is_post_publicly_viewable( $post = null ) {
		return is_post_publicly_viewable( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function get_posts( $args = null ) {
		return get_posts( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function is_sticky( $post_id = 0 ) {
		return is_sticky( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_post( $post, $context = 'display' ) {
		return sanitize_post( $post, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_post_field( $field, $value, $post_id, $context = 'display' ) {
		return sanitize_post_field( $field, $value, $post_id, $context );
	}

	/**
	 * @inheritDoc
	 */
	public function stick_post( $post_id ) {
		stick_post( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function unstick_post( $post_id ) {
		unstick_post( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_count_posts( $type = 'post', $perm = '' ) {
		return wp_count_posts( $type, $perm );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_mime_types() {
		return get_post_mime_types();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_match_mime_types( $wildcard_mime_types, $real_mime_types ) {
		return wp_match_mime_types( $wildcard_mime_types, $real_mime_types );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_post_mime_type_where( $post_mime_types, $table_alias = '' ) {
		return wp_post_mime_type_where( $post_mime_types, $table_alias );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_post( $postid = 0, $force_delete = false ) {
		return wp_delete_post( $postid, $force_delete );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_trash_post( $post_id = 0 ) {
		return wp_trash_post( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_untrash_post( $post_id = 0 ) {
		return wp_untrash_post( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_trash_post_comments( $post = null ) {
		return wp_trash_post_comments( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_untrash_post_comments( $post = null ) {
		return wp_untrash_post_comments( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_post_categories( $post_id = 0, $args = [] ) {
		return wp_get_post_categories( $post_id, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_post_tags( $post_id = 0, $args = [] ) {
		return wp_get_post_tags( $post_id, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_post_terms( $post_id = 0, $taxonomy = 'post_tag', $args = [] ) {
		return wp_get_post_terms( $post_id, $taxonomy, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_recent_posts( $args = [], $output = 'ARRAY_A' ) {
		return wp_get_recent_posts( $args, $output );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_insert_post( $postarr, $wp_error = false, $fire_after_hooks = true ) {
		return wp_insert_post( $postarr, $wp_error, $fire_after_hooks );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_post( $postarr = [], $wp_error = false, $fire_after_hooks = true ) {
		return wp_update_post( $postarr, $wp_error, $fire_after_hooks );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_publish_post( $post ) {
		wp_publish_post( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function check_and_publish_future_post( $post_id ) {
		check_and_publish_future_post( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_resolve_post_date( $post_date = '', $post_date_gmt = '' ) {
		return wp_resolve_post_date( $post_date, $post_date_gmt );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent ) {
		return wp_unique_post_slug( $slug, $post_ID, $post_status, $post_status, $post_parent );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_add_post_tags( $post_id = 0, $tags = '' ) {
		return wp_add_post_tags( $post_id, $tags );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_post_tags( $post_id = 0, $tags = '', $append = false ) {
		return wp_set_post_tags( $post_id, $tags, $append );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_post_terms( $post_id = 0, $tags = '', $taxonomy = 'post_tag', $append = false ) {
		return wp_set_post_terms( $post_id, $tags, $taxonomy, $append );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_post_categories( $post_ID = 0, $post_categories = [], $append = false ) {
		return wp_set_post_categories( $post_ID, $post_categories, $append );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_transition_post_status( $new_status, $old_status, $post ) {
		wp_transition_post_status( $new_status, $old_status, $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_after_insert_post( $post, $update, $post_before ) {
		wp_after_insert_post( $post, $update, $post_before );
	}

	/**
	 * @inheritDoc
	 */
	public function add_ping( $post_id, $uri ) {
		return add_ping( $post_id, $uri );
	}

	/**
	 * @inheritDoc
	 */
	public function get_enclosed( $post_id ) {
		return get_enclosed( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_pung( $post_id ) {
		return get_pung( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_to_ping( $post_id ) {
		return get_to_ping( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function trackback_url_list( $tb_list, $post_id ) {
		trackback_url_list( $tb_list, $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_all_page_ids() {
		return get_all_page_ids();
	}

	/**
	 * @inheritDoc
	 */
	public function get_page( $page, $output = 'OBJECT', $filter = 'raw' ) {
		return get_page( $page, $output, $filter );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_by_path( $page_path, $output = 'OBJECT', $post_type = 'page' ) {
		return get_page_by_path( $page_path, $output, $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_by_title( $page_title, $output = 'OBJECT', $post_type = 'page' ) {
		return get_page_by_title( $page_title, $output, $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_children( $page_id, $pages ) {
		return get_page_children( $page_id, $pages );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_hierarchy( &$pages, $page_id = 0 ) {
		return get_page_hierarchy( $pages, $page_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_uri( $page = 0 ) {
		return get_page_uri( $page );
	}

	/**
	 * @inheritDoc
	 */
	public function get_pages( $args = [] ) {
		return get_pages( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_mime_type_icon( $mime = 0 ) {
		return wp_mime_type_icon( $mime );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_for_changed_slugs( $post_id, $post, $post_before ) {
		wp_check_for_changed_slugs( $post_id, $post, $post_before );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_for_changed_dates( $post_id, $post, $post_before ) {
		wp_check_for_changed_dates( $post_id, $post, $post_before );
	}

	/**
	 * @inheritDoc
	 */
	public function get_private_posts_cap_sql( $post_type ) {
		return get_private_posts_cap_sql( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_posts_by_author_sql( $post_type, $full = true, $post_author = null, $public_only = false ) {
		return get_posts_by_author_sql( $post_type, $full, $post_author, $public_only );
	}

	/**
	 * @inheritDoc
	 */
	public function get_lastpostdate( $timezone = 'server', $post_type = 'any' ) {
		return get_lastpostdate( $timezone, $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_lastpostmodified( $timezone = 'server', $post_type = 'any' ) {
		return get_lastpostmodified( $timezone, $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function update_post_cache( &$posts ) {
		update_post_cache( $posts );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_post_cache( $post ) {
		clean_post_cache( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function update_post_caches(
		&$posts,
		$post_type = 'post',
		$update_term_cache = true,
		$update_meta_cache = true
	) {
		update_post_caches( $posts, $post_type, $update_term_cache, $update_meta_cache );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_post_parent_id( $post ) {
		return wp_get_post_parent_id( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_post_hierarchy_for_loops( $post_parent, $post_ID ) {
		return wp_check_post_hierarchy_for_loops( $post_parent, $post_ID );
	}

	/**
	 * @inheritDoc
	 */
	public function set_post_thumbnail( $post, $thumbnail_id ) {
		return set_post_thumbnail( $post, $thumbnail_id );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_post_thumbnail( $post ) {
		return delete_post_thumbnail( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_auto_drafts() {
		wp_delete_auto_drafts();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_queue_posts_for_term_meta_lazyload( $posts ) {
		wp_queue_posts_for_term_meta_lazyload( $posts );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_cache_set_posts_last_changed() {
		wp_cache_set_posts_last_changed();
	}

	/**
	 * @inheritDoc
	 */
	public function get_available_post_mime_types( $type = 'attachment' ) {
		return get_available_post_mime_types( $type );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_original_image_path( $attachment_id, $unfiltered = false ) {
		return wp_get_original_image_path( $attachment_id, $unfiltered );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_original_image_url( $attachment_id ) {
		return wp_get_original_image_url( $attachment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_untrash_post_set_previous_status( $new_status, $post_id, $previous_status ) {
		return wp_untrash_post_set_previous_status( $new_status, $post_id, $previous_status );
	}
}

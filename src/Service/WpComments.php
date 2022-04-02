<?php

namespace Merkushin\Wpal\Service;

class WpComments implements Comments {

	/**
	 * @inheritDoc
	 */
	public function check_comment( $author, $email, $url, $comment, $user_ip, $user_agent, $comment_type ) {
		return check_comment( $author, $email, $url, $comment, $user_ip, $user_agent, $comment_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_approved_comments( $post_id, $args = [] ) {
		return get_approved_comments( $post_id, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comment( $comment = null, $output = 'OBJECT' ) {
		return get_comment( $comment, $output );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comments( $args = '' ) {
		return get_comments( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comment_statuses() {
		return get_comment_statuses();
	}

	/**
	 * @inheritDoc
	 */
	public function get_default_comment_status( $post_type = 'post', $comment_type = 'comment' ) {
		return get_default_comment_status( $post_type, $comment_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_lastcommentmodified( $timezone = 'server' ) {
		return get_lastcommentmodified( $timezone );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comment_count( $post_id = 0 ) {
		return get_comment_count( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function add_comment_meta( $comment_id, $meta_key, $meta_value, $unique = false ) {
		return add_comment_meta( $comment_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_comment_meta( $comment_id, $meta_key, $meta_value = '' ) {
		return delete_comment_meta( $comment_id, $meta_key, $meta_value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comment_meta( $comment_id, $key = '', $single = false ) {
		return get_comment_meta( $comment_id, $key, $single );
	}

	/**
	 * @inheritDoc
	 */
	public function update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_queue_comments_for_comment_meta_lazyload( $comments ) {
		wp_queue_comments_for_comment_meta_lazyload( $comments );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_comment_cookies( $comment, $user, $cookies_consent = true ) {
		wp_set_comment_cookies( $comment, $user, $cookies_consent );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_comment_cookies() {
		sanitize_comment_cookies();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_allow_comment( $commentdata, $wp_error = false ) {
		return wp_allow_comment( $commentdata, $wp_error );
	}

	/**
	 * @inheritDoc
	 */
	public function check_comment_flood_db() {
		check_comment_flood_db();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_comment_flood( $is_flood, $ip, $email, $date, $avoid_die = false ) {
		return wp_check_comment_flood( $is_flood, $ip, $email, $date, $avoid_die );
	}

	/**
	 * @inheritDoc
	 */
	public function separate_comments( &$comments ) {
		return separate_comments( $comments );
	}

	/**
	 * @inheritDoc
	 */
	public function get_comment_pages_count( $comments = null, $per_page = null, $threaded = null ) {
		return get_comment_pages_count( $comments, $per_page, $threaded );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_of_comment( $comment_ID, $args = [] ) {
		return get_page_of_comment( $comment_ID, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_comment_fields_max_lengths() {
		return wp_get_comment_fields_max_lengths();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_comment_data_max_lengths( $comment_data ) {
		return wp_check_comment_data_max_lengths( $comment_data );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent ) {
		return wp_check_comment_disallowed_list( $author, $email, $url, $comment, $user_ip, $user_agent );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_count_comments( $post_id = 0 ) {
		return wp_count_comments( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_comment( $comment_id, $force_delete = false ) {
		return wp_delete_comment( $comment_id, $force_delete );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_trash_comment( $comment_id ) {
		return wp_trash_comment( $comment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_untrash_comment( $comment_id ) {
		return wp_untrash_comment( $comment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_spam_comment( $comment_id ) {
		return wp_spam_comment( $comment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_unspam_comment( $comment_id ) {
		return wp_unspam_comment( $comment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_comment_status( $comment_id ) {
		return wp_get_comment_status( $comment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_transition_comment_status( $new_status, $old_status, $comment ) {
		wp_transition_comment_status( $new_status, $old_status, $comment );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_current_commenter() {
		return wp_get_current_commenter();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_unapproved_comment_author_email() {
		return wp_get_unapproved_comment_author_email();
	}

	/**
	 * @inheritDoc
	 */
	public function wp_insert_comment( $commentdata ) {
		return wp_insert_comment( $commentdata );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_filter_comment( $commentdata ) {
		return wp_filter_comment( $commentdata );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_throttle_comment_flood( $block, $time_lastcomment, $time_newcomment ) {
		return wp_throttle_comment_flood( $block, $time_lastcomment, $time_newcomment );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_new_comment( $commentdata, $wp_error = false ) {
		return wp_new_comment( $commentdata, $wp_error );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_new_comment_notify_moderator( $comment_ID ) {
		return wp_new_comment_notify_moderator( $comment_ID );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_new_comment_notify_postauthor( $comment_ID ) {
		return wp_new_comment_notify_postauthor( $comment_ID );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_set_comment_status( $comment_id, $comment_status, $wp_error = false ) {
		return wp_set_comment_status( $comment_id, $comment_status, $wp_error );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_comment( $commentarr, $wp_error = false ) {
		return wp_update_comment( $commentarr, $wp_error );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_defer_comment_counting( $defer = null ) {
		return wp_defer_comment_counting( $defer );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_comment_count( $post_id, $do_deferred = false ) {
		return wp_update_comment_count( $post_id, $do_deferred );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_comment_count_now( $post_id ) {
		return wp_update_comment_count_now( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function discover_pingback_server_uri( $url, $deprecated = '' ) {
		return discover_pingback_server_uri( $url, $deprecated );
	}

	/**
	 * @inheritDoc
	 */
	public function do_all_pings() {
		do_all_pings();
	}

	/**
	 * @inheritDoc
	 */
	public function do_all_pingbacks() {
		do_all_pingbacks();
	}

	/**
	 * @inheritDoc
	 */
	public function do_all_enclosures() {
		do_all_enclosures();
	}

	/**
	 * @inheritDoc
	 */
	public function do_all_trackbacks() {
		do_all_trackbacks();
	}

	/**
	 * @inheritDoc
	 */
	public function do_trackbacks( $post_id ) {
		return do_trackbacks( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function generic_ping( $post_id = 0 ) {
		return generic_ping( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function pingback( $content, $post_id ) {
		pingback( $content, $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function privacy_ping_filter( $sites ) {
		return privacy_ping_filter( $sites );
	}

	/**
	 * @inheritDoc
	 */
	public function trackback( $trackback_url, $title, $excerpt, $ID ) {
		return trackback( $trackback_url, $title, $excerpt, $ID );
	}

	/**
	 * @inheritDoc
	 */
	public function weblog_ping( $server = '', $path = '' ) {
		weblog_ping( $server, $path );
	}

	/**
	 * @inheritDoc
	 */
	public function pingback_ping_source_uri( $source_uri ) {
		return pingback_ping_source_uri( $source_uri );
	}

	/**
	 * @inheritDoc
	 */
	public function xmlrpc_pingback_error( $ixr_error ) {
		return xmlrpc_pingback_error( $ixr_error );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_comment_cache( $ids ) {
		clean_comment_cache( $ids );
	}

	/**
	 * @inheritDoc
	 */
	public function update_comment_cache( $comments, $update_meta_cache = true ) {
		update_comment_cache( $comments, $update_meta_cache );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_handle_comment_submission( $comment_data ) {
		return wp_handle_comment_submission( $comment_data );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_register_comment_personal_data_exporter( $exporters ) {
		return wp_register_comment_personal_data_exporter( $exporters );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_comments_personal_data_exporter( $email_address, $page = 1 ) {
		return wp_comments_personal_data_exporter( $email_address, $page );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_register_comment_personal_data_eraser( $erasers ) {
		return wp_register_comment_personal_data_eraser( $erasers );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_comments_personal_data_eraser( $email_address, $page = 1 ) {
		return wp_comments_personal_data_eraser( $email_address, $page );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_cache_set_comments_last_changed() {
		wp_cache_set_comments_last_changed();
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_batch_update_comment_type() {
		_wp_batch_update_comment_type();
	}

	/**
	 * @inheritDoc
	 */
	public function _wp_check_for_scheduled_update_comment_type() {
		_wp_check_for_scheduled_update_comment_type();
	}
}

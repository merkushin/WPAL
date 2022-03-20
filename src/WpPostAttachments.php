<?php

namespace Merkushin\Wpal;

class WpPostAttachments implements PostAttachments {
	/**
	 * @inheritDoc
	 */
	public function get_attached_file( int $attachment_id, $unfiltered = false ) {
		return get_attached_file( $attachment_id, $unfiltered );
	}

	/**
	 * @inheritDoc
	 */
	public function update_attached_file( int $attachment_id, string $file ): bool {
		return update_attached_file( $attachment_id, $file );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_count_attachments( $mime_type = '' ) {
		return wp_count_attachments( $mime_type = '' );
	}

	/**
	 * @inheritDoc
	 */
	public function is_local_attachment( $url ) {
		return is_local_attachment( $url );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_insert_attachment(
		$args,
		$file = false,
		$parent = 0,
		$wp_error = false,
		$fire_after_hooks = true
	) {
		return wp_insert_attachment( $args, $file, $parent, $wp_error, $fire_after_hooks );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_attachment( $post_id, $force_delete = false ) {
		return wp_delete_attachment( $post_id, $force_delete );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_delete_attachment_files( $post_id, $meta, $backup_sizes, $file ) {
		return wp_delete_attachment_files( $post_id, $meta, $backup_sizes, $file );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_attachment_metadata( $attachment_id = 0, $unfiltered = false ) {
		return wp_get_attachment_metadata( $attachment_id, $unfiltered );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_update_attachment_metadata( $attachment_id, $data ) {
		return wp_update_attachment_metadata( $attachment_id, $data );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_attachment_url( $attachment_id = 0 ) {
		return wp_get_attachment_url( $attachment_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_attachment_caption( $post_id = 0 ) {
		return wp_get_attachment_caption( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_attachment_thumb_file( $post_id = 0 ) {
		return wp_get_attachment_thumb_file( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_get_attachment_thumb_url( $post_id = 0 ) {
		return wp_get_attachment_thumb_url( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_attachment_is( $type, $post = null ) {
		return wp_attachment_is( $type, $post );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_attachment_is_image( $post = null ) {
		return wp_attachment_is_image( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function clean_attachment_cache( $id, $clean_terms = false ) {
		clean_attachment_cache( $id, $clean_terms );
	}
}

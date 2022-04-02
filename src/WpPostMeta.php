<?php declare(strict_types=1);

namespace Merkushin\Wpal;

final class WpPostMeta implements PostMeta {
	/**
	 * @inheritDoc
	 */
	public function add_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {
		return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_post_meta( $post_id, $meta_key, $meta_value = '' ) {
		return delete_post_meta( $post_id, $meta_key, $meta_value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_meta( $post_id, $key = '', $single = false ) {
		return get_post_meta( $post_id, $key, $single );
	}

	/**
	 * @inheritDoc
	 */
	public function update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * @inheritDoc
	 */
	public function delete_post_meta_by_key( $post_meta_key ) {
		return delete_post_meta_by_key( $post_meta_key );
	}

	/**
	 * @inheritDoc
	 */
	public function register_post_meta( $post_type, $meta_key, array $args ) {
		return register_post_meta( $post_type, $meta_key, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister_post_meta( $post_type, $meta_key ) {
		return unregister_post_meta( $post_type, $meta_key );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_custom( $post_id = 0 ) {
		return get_post_custom( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_custom_keys( $post_id = 0 ) {
		return get_post_custom_keys( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_custom_values( $key = '', $post_id = 0 ) {
		return get_post_custom_values( $key, $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function update_postmeta_cache( $post_ids ) {
		return update_postmeta_cache( $post_ids );
	}
}

<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

final class WpPostTypes implements PostTypes {
	/**
	 * @inheritDoc
	 */
	public function is_post_type_hierarchical( $post_type ) {
		return is_post_type_hierarchical( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function post_type_exists( $post_type ) {
		return post_type_exists( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_type( $post = null ) {
		return get_post_type( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_type_object( $post_type ) {
		return get_post_type_object( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_types( $args = [], $output = 'names', $operator = 'and' ) {
		return get_post_types( $args, $output, $operator );
	}

	/**
	 * @inheritDoc
	 */
	public function register_post_type( $post_type, $args = [] ) {
		return register_post_type( $post_type, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function unregister_post_type( $post_type ) {
		return unregister_post_type( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_type_capabilities( $args ) {
		return get_post_type_capabilities( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function add_post_type_support( $post_type, $feature, ...$args ) {
		add_post_type_support( $post_type, $feature, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_post_type_support( $post_type, $feature ) {
		remove_post_type_support( $post_type, $feature );
	}

	/**
	 * @inheritDoc
	 */
	public function get_all_post_type_supports( $post_type ) {
		return get_all_post_type_supports( $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function post_type_supports( $post_type, $feature ) {
		return post_type_supports( $post_type, $feature );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_types_by_support( $feature, $operator = 'and' ) {
		return get_post_types_by_support( $feature, $operator );
	}

	/**
	 * @inheritDoc
	 */
	public function set_post_type( $post_id = 0, $post_type = 'post' ) {
		return set_post_type( $post_id, $post_type );
	}

	/**
	 * @inheritDoc
	 */
	public function is_post_type_viewable( $post_type ) {
		return is_post_type_viewable( $post_type );
	}
}

<?php declare(strict_types=1);

namespace Merkushin\Wpal;

final class WpPostStatuses implements PostStatuses {
	/**
	 * @inheritDoc
	 */
	public function get_post_status( $post = null ) {
		return get_post_status( $post );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_statuses() {
		return get_post_statuses();
	}

	/**
	 * @inheritDoc
	 */
	public function get_page_statuses() {
		return get_page_statuses();
	}

	/**
	 * @inheritDoc
	 */
	public function register_post_status( $post_status, $args = [] ) {
		return register_post_status( $post_status, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_status_object( $post_status ) {
		return get_post_status_object( $post_status );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_stati( $args = [], $output = 'names', $operator = 'and' ) {
		return get_post_stati( $args, $output, $operator );
	}

	/**
	 * @inheritDoc
	 */
	public function is_post_status_viewable( $post_status ) {
		return is_post_status_viewable( $post_status );
	}
}

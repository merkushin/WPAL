<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Service;

class WpCapabilities implements Capabilities {

	/**
	 * @inheritDoc
	 */
	public function map_meta_cap( $cap, $user_id, ...$args ) {
		return map_meta_cap( $cap, $user_id, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function current_user_can( $capability, ...$args ) {
		return current_user_can( $capability, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function current_user_can_for_blog( $blog_id, $capability, ...$args ) {
		return current_user_can_for_blog( $blog_id, $capability, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function author_can( $post, $capability, ...$args ) {
		return author_can( $post, $capability, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function user_can( $user, $capability, ...$args ) {
		return user_can( $user, $capability, ...$args );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_roles() {
		return wp_roles();
	}

	/**
	 * @inheritDoc
	 */
	public function get_role( $role ) {
		return get_role( $role );
	}

	/**
	 * @inheritDoc
	 */
	public function add_role( $role, $display_name, $capabilities = [] ) {
		return add_role( $role, $display_name, $capabilities );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_role( $role ) {
		remove_role( $role );
	}

	/**
	 * @inheritDoc
	 */
	public function get_super_admins() {
		return get_super_admins();
	}

	/**
	 * @inheritDoc
	 */
	public function is_super_admin( $user_id = false ) {
		return is_super_admin( $user_id );
	}

	/**
	 * @inheritDoc
	 */
	public function grant_super_admin( $user_id ) {
		return grant_super_admin( $user_id );
	}

	/**
	 * @inheritDoc
	 */
	public function revoke_super_admin( $user_id ) {
		return revoke_super_admin( $user_id );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_maybe_grant_install_languages_cap( $allcaps ) {
		return wp_maybe_grant_install_languages_cap( $allcaps );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_maybe_grant_resume_extensions_caps( $allcaps ) {
		return wp_maybe_grant_resume_extensions_caps( $allcaps );
	}

	/**
	 * @inheritDoc
	 */
	public function wp_maybe_grant_site_health_caps( $allcaps, $caps, $args, $user ) {
		return wp_maybe_grant_site_health_caps( $allcaps, $caps, $args, $user );
	}
}

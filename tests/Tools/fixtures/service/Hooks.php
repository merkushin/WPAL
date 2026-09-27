<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Hooks {
	public function has_filter( string $hook_name, $callback = false );

	public function do_action( string $hook_name, ...$args );

	public function get_thing( int $id, $output = 'OBJECT', array $args = [], &$found = null );

	public function old_function( $page_title );

	/**
	 * @deprecated 5.5.0
	 */
	public function renamed_function();

	public function gone_function();

	public function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null );
}

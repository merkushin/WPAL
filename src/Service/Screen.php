<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Screen {
	/**
	 * Get the current screen object
	 *
	 * @since 3.1.0
	 *
	 * @global WP_Screen $current_screen WordPress current screen object.
	 *
	 * @return WP_Screen|null Current screen object or null when screen not defined.
	 */
	function get_current_screen();

	/**
	 * Set the current screen object
	 *
	 * @since 3.0.0
	 *
	 * @param string|WP_Screen $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen,
	 *                                    or an existing screen object.
	 */
	function set_current_screen( $hook_name = '' );

	/**
	 * Register and configure an admin screen option
	 *
	 * @since 3.1.0
	 *
	 * @param string $option An option name.
	 * @param mixed  $args   Option-dependent arguments.
	 */
	function add_screen_option( $option, $args = array() );
}

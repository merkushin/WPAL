<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

final class WpScreen implements Screen {
	/** @inheritDoc */
	public function get_current_screen() {
		return get_current_screen();
	}

	/** @inheritDoc */
	public function set_current_screen($hook_name = '') {
		set_current_screen($hook_name);
	}

	/** @inheritDoc */
	public function add_screen_option($option, $args = array()) {
		add_screen_option($option, $args);
	}
}

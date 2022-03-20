<?php

namespace Merkushin\Wpal;

final class WpPlugins implements Plugins {
	public function plugin_basename( string $file ): string {
		return plugin_basename( $file );
	}

	public function wp_register_plugin_realpath( string $file ): bool {
		return wp_register_plugin_realpath( $file );
	}

	public function plugin_dir_path( string $file ): string {
		return plugin_dir_path( $file );
	}

	public function plugin_dir_url( string $file ): string {
		return plugin_dir_url( $file );
	}

	public function register_activation_hook( string $file, callable $callback ) {
		register_activation_hook( $file, $callback );
	}

	public function register_deactivation_hook( string $file, callable $callback ) {
		register_deactivation_hook( $file, $callback );
	}

	public function register_uninstall_hook( string $file, callable $callback ) {
		register_uninstall_hook( $file, $callback );
	}
}


<?php

namespace Merkushin\Wpal;

interface Plugins {
	public function plugin_basename( string $file ): string;

	public function wp_register_plugin_realpath( string $file ): bool;

	public function plugin_dir_path( string $file ): string;

	public function plugin_dir_url( string $file ): string;

	public function register_activation_hook( string $file, callable $callback );

	public function register_deactivation_hook( string $file, callable $callback );

	public function register_uninstall_hook( string $file, callable $callback );
}


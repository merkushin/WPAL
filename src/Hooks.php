<?php

namespace Merkushin\Wpal;

interface Hooks {
	public function add_filter( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool;

	public function apply_filters( string $hook_name, $value, ...$args );

	public function has_filter( string $hook_name, $callback = false );

	public function remove_filter( string $hook_name, callable $callback, int $priority = 10 ): bool;

	public function remove_all_filters( string $hook_name, $priority = false ): bool;

	public function current_filter();

	public function doing_filter( $hook_name = null ): bool;

	public function add_action( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool;

	public function do_action( string $hook_name, ...$args );

	public function has_action( string $hook_name, $callback = false );

	public function remove_action( string $hook_name, callable $callback, int $priority = 10): bool;

	public function remove_all_actions( string $hook_name, $priority = false );

	public function current_action();

	public function doing_action( $hook_name = null ): bool;

	public function did_action( string $hook_name ): int;

	public function apply_filters_deprecated( string $hook_name, array $args, string $version, string $replacement = '', string $message = '' );

	public function do_action_deprecated( string $hook_name, array $args, string $version, string $replacement = '', string $message = '' );
};


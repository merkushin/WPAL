<?php

namespace Merkushin/Wpal;

final class WpHooks implements Hooks {
	public function add_filter( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return add_filter( $hook_name, $callback, $priority, $accepted_args );
	}

	public function apply_filters( string $hook_name, $value, ...$args ) {
		return apply_filters( $hook_name, $value, ...$args );
	}

	public function has_filter( string $hook_name, $callback = false ) {
		return has_filter( $hook_name, $callback );
	}

	public function remove_filter( string $hook_name, callable $callback, int $priority = 10     ): bool {
		return remove_filter( $hook_name, $callback, $priority );
	}

	public function remove_all_filters( string $hook_name, $priority = false ): bool {
		return remove_all_filters( $hook_name, $priority );
	}

	public function current_filter() {
		return current_filter();
	}

	public function doing_filter( $hook_name = null ): bool {
		return doing_filter( $hook_name );
	}

	public function add_action( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return add_action( $hook_name, $callable, $priority, $accepted_args );
	}

	public function do_action( string $hook_name, ...$args ) {
		do_action( $hook_name, ...$args );
	}

	public function has_action( string $hook_name, $callback = false ) {
		return has_action( $hook_name, $callback );
	}

	public function remove_action( string $hook_name, callable $callback, int $priority = 10): bool {
		return remove_action( $hook_name, $callback, $priority );
	}

	public function remove_all_actions( string $hook_name, $priority = false ) {
		return remove_all_actions( $hook_name, $priority );
	}

	public function current_action() {
		return current_action();
	}

	public function doing_action( $hook_name = null ): bool {
		return doing_action( $hook_name );
	}

	public function did_action( string $hook_name ): int {
		did_action( $hook_name );
	}

	public function apply_filters_deprecated( string $hook_name, array $args, string $version, string $replacement = '', string $message = '' ) {
		return apply_filters_deprecated( $hook_name, $args, $version, $replacement, $message );
	}

	public function do_action_deprecated( string $hook_name, array $args, string $version, string $replacement = '', string $message = '' ) {
		return do_action_deprecated( $hook_name, $args, $version, $replacement, $message );
	}
}


<?php
/**
 * Checks if any filter has been registered for a hook.
 *
 * @since 2.5.0
 * @since 6.9.0 Added the `$priority` parameter.
 */
function has_filter( $hook_name, $callback = false, $priority = false ) {}

/**
 * Calls the callback functions that have been added to an action hook.
 *
 * @since 1.2.0
 */
function do_action( $hook_name, ...$arg ) {}

function _private_helper() {}

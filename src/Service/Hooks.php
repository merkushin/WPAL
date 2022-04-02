<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Hooks {
	/**
	 * Adds a callback function to a filter hook.
	 *
	 * WordPress offers filter hooks to allow plugins to modify
	 * various types of internal data at runtime.
	 *
	 * A plugin can modify data by binding a callback to a filter hook. When the filter
	 * is later applied, each bound callback is run in order of priority, and given
	 * the opportunity to modify a value by returning a new value.
	 *
	 * The following example shows how a callback function is bound to a filter hook.
	 *
	 * Note that `$example` is passed to the callback, (maybe) modified, then returned:
	 *
	 *     function example_callback( $example ) {
	 *         // Maybe modify $example in some way.
	 *         return $example;
	 *     }
	 *     add_filter( 'example_filter', 'example_callback' );
	 *
	 * Bound callbacks can accept from none to the total number of arguments passed as parameters
	 * in the corresponding apply_filters() call.
	 *
	 * In other words, if an apply_filters() call passes four total arguments, callbacks bound to
	 * it can accept none (the same as 1) of the arguments or up to four. The important part is that
	 * the `$accepted_args` value must reflect the number of arguments the bound callback *actually*
	 * opted to accept. If no arguments were accepted by the callback that is considered to be the
	 * same as accepting 1 argument. For example:
	 *
	 *     // Filter call.
	 *     $value = apply_filters( 'hook', $value, $arg2, $arg3 );
	 *
	 *     // Accepting zero/one arguments.
	 *     function example_callback() {
	 *         ...
	 *         return 'some value';
	 *     }
	 *     add_filter( 'hook', 'example_callback' ); // Where $priority is default 10, $accepted_args is default 1.
	 *
	 *     // Accepting two arguments (three possible).
	 *     function example_callback( $value, $arg2 ) {
	 *         ...
	 *         return $maybe_modified_value;
	 *     }
	 *     add_filter( 'hook', 'example_callback', 10, 2 ); // Where $priority is 10, $accepted_args is 2.
	 *
	 * *Note:* The function will return true whether or not the callback is valid.
	 * It is up to you to take care. This is done for optimization purposes, so
	 * everything is as quick as possible.
	 *
	 * @since 0.71
	 *
	 * @global WP_Hook[] $wp_filter A multidimensional array of all hooks and the callbacks hooked to them.
	 *
	 * @param string   $hook_name     The name of the filter to add the callback to.
	 * @param callable $callback      The callback to be run when the filter is applied.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular filter are executed.
	 *                                Lower numbers correspond with earlier execution,
	 *                                and functions with the same priority are executed
	 *                                in the order in which they were added to the filter. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 * @return true Always returns true.
	 */
	public function add_filter(
		string $hook_name,
		callable $callback,
		int $priority = 10,
		int $accepted_args = 1
	): bool;

	/**
	 * Calls the callback functions that have been added to a filter hook.
	 *
	 * This function invokes all functions attached to filter hook `$hook_name`.
	 * It is possible to create new filter hooks by simply calling this function,
	 * specifying the name of the new hook using the `$hook_name` parameter.
	 *
	 * The function also allows for multiple additional arguments to be passed to hooks.
	 *
	 * Example usage:
	 *
	 *     // The filter callback function.
	 *     function example_callback( $string, $arg1, $arg2 ) {
	 *         // (maybe) modify $string.
	 *         return $string;
	 *     }
	 *     add_filter( 'example_filter', 'example_callback', 10, 3 );
	 *
	 *     /*
	 *      * Apply the filters by calling the 'example_callback()' function
	 *      * that's hooked onto `example_filter` above.
	 *      *
	 *      * - 'example_filter' is the filter hook.
	 *      * - 'filter me' is the value being filtered.
	 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
	 *     $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 );
	 *
	 * @since 0.71
	 *
	 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
	 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
	 *
	 * @param string $hook_name The name of the filter hook.
	 * @param mixed  $value     The value to filter.
	 * @param mixed  ...$args   Additional parameters to pass to the callback functions.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function apply_filters( string $hook_name, $value, ...$args );

	/**
	 * Calls the callback functions that have been added to a filter hook, specifying arguments in an array.
	 *
	 * @since 3.0.0
	 *
	 * @see apply_filters() This function is identical, but the arguments passed to the
	 *                      functions hooked to `$hook_name` are supplied using an array.
	 *
	 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
	 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
	 *
	 * @param string $hook_name The name of the filter hook.
	 * @param array  $args      The arguments supplied to the functions hooked to `$hook_name`.
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function apply_filters_ref_array( string $hook_name, array $args );

	/**
	 * Checks if any filter has been registered for a hook.
	 *
	 * When using the `$callback` argument, this function may return a non-boolean value
	 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
	 *
	 * @since 2.5.0
	 *
	 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
	 *
	 * @param string         $hook_name The name of the filter hook.
	 * @param callable|false $callback  Optional. The callback to check for. Default false.
	 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
	 *                  anything registered. When checking a specific function, the priority
	 *                  of that hook is returned, or false if the function is not attached.
	 */
	public function has_filter( string $hook_name, $callback = false );

	/**
	 * Removes a callback function from a filter hook.
	 *
	 * This can be used to remove default functions attached to a specific filter
	 * hook and possibly replace them with a substitute.
	 *
	 * To remove a hook, the `$callback` and `$priority` arguments must match
	 * when the hook was added. This goes for both filters and actions. No warning
	 * will be given on removal failure.
	 *
	 * @since 1.2.0
	 *
	 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
	 *
	 * @param string   $hook_name The filter hook to which the function to be removed is hooked.
	 * @param callable $callback  The name of the function which should be removed.
	 * @param int      $priority  Optional. The exact priority used when adding the original
	 *                            filter callback. Default 10.
	 * @return bool Whether the function existed before it was removed.
	 */
	public function remove_filter( string $hook_name, callable $callback, int $priority = 10 ): bool;

	/**
	 * Removes all of the callback functions from a filter hook.
	 *
	 * @since 2.7.0
	 *
	 * @global WP_Hook[] $wp_filter Stores all of the filters and actions.
	 *
	 * @param string    $hook_name The filter to remove callbacks from.
	 * @param int|false $priority  Optional. The priority number to remove them from.
	 *                             Default false.
	 * @return true Always returns true.
	 */
	public function remove_all_filters( string $hook_name, $priority = false ): bool;

	/**
	 * Retrieves the name of the current filter hook.
	 *
	 * @since 2.5.0
	 *
	 * @global string[] $wp_current_filter Stores the list of current filters with the current one last
	 *
	 * @return string|false Hook name of the current filter.
	 */
	public function current_filter();

	/**
	 * Returns whether or not a filter hook is currently being processed.
	 *
	 * The function current_filter() only returns the most recent filter or action
	 * being executed. did_action() returns true once the action is initially
	 * processed.
	 *
	 * This function allows detection for any filter currently being executed
	 * (regardless of whether it's the most recent filter to fire, in the case of
	 * hooks called from hook callbacks) to be verified.
	 *
	 * @since 3.9.0
	 *
	 * @see current_filter()
	 * @see did_action()
	 * @global string[] $wp_current_filter Current filter.
	 *
	 * @param null|string $hook_name Optional. Filter hook to check. Defaults to null,
	 *                               which checks if any filter is currently being run.
	 * @return bool Whether the filter is currently in the stack.
	 */
	public function doing_filter( $hook_name = null ): bool;

	/**
	 * Adds a callback function to an action hook.
	 *
	 * Actions are the hooks that the WordPress core launches at specific points
	 * during execution, or when specific events occur. Plugins can specify that
	 * one or more of its PHP functions are executed at these points, using the
	 * Action API.
	 *
	 * @since 1.2.0
	 *
	 * @param string   $hook_name       The name of the action to add the callback to.
	 * @param callable $callback        The callback to be run when the action is called.
	 * @param int      $priority        Optional. Used to specify the order in which the functions
	 *                                  associated with a particular action are executed.
	 *                                  Lower numbers correspond with earlier execution,
	 *                                  and functions with the same priority are executed
	 *                                  in the order in which they were added to the action. Default 10.
	 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
	 * @return true Always returns true.
	 */
	public function add_action(
		string $hook_name,
		callable $callback,
		int $priority = 10,
		int $accepted_args = 1
	): bool;

	/**
	 * Calls the callback functions that have been added to an action hook.
	 *
	 * This function invokes all functions attached to action hook `$hook_name`.
	 * It is possible to create new action hooks by simply calling this function,
	 * specifying the name of the new hook using the `$hook_name` parameter.
	 *
	 * You can pass extra arguments to the hooks, much like you can with `apply_filters()`.
	 *
	 * Example usage:
	 *
	 *     // The action callback function.
	 *     function example_callback( $arg1, $arg2 ) {
	 *         // (maybe) do something with the args.
	 *     }
	 *     add_action( 'example_action', 'example_callback', 10, 2 );
	 *
	 *     /*
	 *      * Trigger the actions by calling the 'example_callback()' function
	 *      * that's hooked onto `example_action` above.
	 *      *
	 *      * - 'example_action' is the action hook.
	 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
	 *     $value = do_action( 'example_action', $arg1, $arg2 );
	 *
	 * @since 1.2.0
	 * @since 5.3.0 Formalized the existing and already documented `...$arg` parameter
	 *              by adding it to the function signature.
	 *
	 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
	 * @global int[]     $wp_actions        Stores the number of times each action was triggered.
	 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
	 *
	 * @param string $hook_name The name of the action to be executed.
	 * @param mixed  ...$arg    Optional. Additional arguments which are passed on to the
	 *                          functions hooked to the action. Default empty.
	 */
	public function do_action( string $hook_name, ...$args );

	/**
	 * Calls the callback functions that have been added to an action hook, specifying arguments in an array.
	 *
	 * @since 2.1.0
	 *
	 * @see do_action() This function is identical, but the arguments passed to the
	 *                  functions hooked to `$hook_name` are supplied using an array.
	 *
	 * @global WP_Hook[] $wp_filter         Stores all of the filters and actions.
	 * @global int[]     $wp_actions        Stores the number of times each action was triggered.
	 * @global string[]  $wp_current_filter Stores the list of current filters with the current one last.
	 *
	 * @param string $hook_name The name of the action to be executed.
	 * @param array  $args      The arguments supplied to the functions hooked to `$hook_name`.
	 */
	public function do_action_ref_array( string $hook_name, array $args );

	/**
	 * Checks if any action has been registered for a hook.
	 *
	 * When using the `$callback` argument, this function may return a non-boolean value
	 * that evaluates to false (e.g. 0), so use the `===` operator for testing the return value.
	 *
	 * @since 2.5.0
	 *
	 * @see has_filter() has_action() is an alias of has_filter().
	 *
	 * @param string         $hook_name The name of the action hook.
	 * @param callable|false $callback  Optional. The callback to check for. Default false.
	 * @return bool|int If `$callback` is omitted, returns boolean for whether the hook has
	 *                  anything registered. When checking a specific function, the priority
	 *                  of that hook is returned, or false if the function is not attached.
	 */
	public function has_action( string $hook_name, $callback = false );

	/**
	 * Removes a callback function from an action hook.
	 *
	 * This can be used to remove default functions attached to a specific action
	 * hook and possibly replace them with a substitute.
	 *
	 * To remove a hook, the `$callback` and `$priority` arguments must match
	 * when the hook was added. This goes for both filters and actions. No warning
	 * will be given on removal failure.
	 *
	 * @since 1.2.0
	 *
	 * @param string   $hook_name The action hook to which the function to be removed is hooked.
	 * @param callable $callback  The name of the function which should be removed.
	 * @param int      $priority  Optional. The exact priority used when adding the original
	 *                            action callback. Default 10.
	 * @return bool Whether the function is removed.
	 */
	public function remove_action( string $hook_name, callable $callback, int $priority = 10 ): bool;

	/**
	 * Removes all of the callback functions from an action hook.
	 *
	 * @since 2.7.0
	 *
	 * @param string    $hook_name The action to remove callbacks from.
	 * @param int|false $priority  Optional. The priority number to remove them from.
	 *                             Default false.
	 * @return true Always returns true.
	 */
	public function remove_all_actions( string $hook_name, $priority = false );

	/**
	 * Retrieves the name of the current action hook.
	 *
	 * @since 3.9.0
	 *
	 * @return string Hook name of the current action.
	 */
	public function current_action();

	/**
	 * Returns whether or not an action hook is currently being processed.
	 *
	 * @since 3.9.0
	 *
	 * @param string|null $hook_name Optional. Action hook to check. Defaults to null,
	 *                               which checks if any action is currently being run.
	 * @return bool Whether the action is currently in the stack.
	 */
	public function doing_action( $hook_name = null ): bool;

	/**
	 * Retrieves the number of times an action has been fired during the current request.
	 *
	 * @since 2.1.0
	 *
	 * @global int[] $wp_actions Stores the number of times each action was triggered.
	 *
	 * @param string $hook_name The name of the action hook.
	 * @return int The number of times the action hook has been fired.
	 */
	public function did_action( string $hook_name ): int;

	/**
	 * Fires functions attached to a deprecated filter hook.
	 *
	 * When a filter hook is deprecated, the apply_filters() call is replaced with
	 * apply_filters_deprecated(), which triggers a deprecation notice and then fires
	 * the original filter hook.
	 *
	 * Note: the value and extra arguments passed to the original apply_filters() call
	 * must be passed here to `$args` as an array. For example:
	 *
	 *     // Old filter.
	 *     return apply_filters( 'wpdocs_filter', $value, $extra_arg );
	 *
	 *     // Deprecated.
	 *     return apply_filters_deprecated( 'wpdocs_filter', array( $value, $extra_arg ), '4.9.0', 'wpdocs_new_filter' );
	 *
	 * @since 4.6.0
	 *
	 * @see _deprecated_hook()
	 *
	 * @param string $hook_name   The name of the filter hook.
	 * @param array  $args        Array of additional function arguments to be passed to apply_filters().
	 * @param string $version     The version of WordPress that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used. Default empty.
	 * @param string $message     Optional. A message regarding the change. Default empty.
	 */
	public function apply_filters_deprecated(
		string $hook_name,
		array $args,
		string $version,
		string $replacement = '',
		string $message = ''
	);

	/**
	 * Fires functions attached to a deprecated action hook.
	 *
	 * When an action hook is deprecated, the do_action() call is replaced with
	 * do_action_deprecated(), which triggers a deprecation notice and then fires
	 * the original hook.
	 *
	 * @since 4.6.0
	 *
	 * @see _deprecated_hook()
	 *
	 * @param string $hook_name   The name of the action hook.
	 * @param array  $args        Array of additional function arguments to be passed to do_action().
	 * @param string $version     The version of WordPress that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used. Default empty.
	 * @param string $message     Optional. A message regarding the change. Default empty.
	 */
	public function do_action_deprecated(
		string $hook_name,
		array $args,
		string $version,
		string $replacement = '',
		string $message = ''
	);
}

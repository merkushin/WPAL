<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

interface Assets {
	/**
	 * Enqueues a script.
	 *
	 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
	 *
	 * @see WP_Dependencies::add()
	 * @see WP_Dependencies::add_data()
	 * @see WP_Dependencies::enqueue()
	 *
	 *
	 * @param string           $handle    Name of the script. Should be unique.
	 * @param string           $src       Full URL of the script, or path of the script relative to the WordPress root directory.
	 *                                    Default empty.
	 * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
	 *                                    as a query string for cache busting purposes. If version is set to false, a version
	 *                                    number is automatically added equal to current installed WordPress version.
	 *                                    If set to null, no version is added.
	 * @param bool             $in_footer Optional. Whether to enqueue the script before `</body>` instead of in the `<head>`.
	 *                                    Default 'false'.
	 */
	public function wp_enqueue_script( string $handle, string $src = '', array $deps = array(), $ver = false, bool $in_footer = false ): void;

	/**
	 * Adds extra code to a registered script.
	 *
	 * Code will only be added if the script is already in the queue.
	 * Accepts a string $data containing the Code. If two or more code blocks
	 * are added to the same script $handle, they will be printed in the order
	 * they were added, i.e. the latter added code can redeclare the previous.
	 *
	 * @see WP_Scripts::add_inline_script()
	 *
	 * @param string $handle   Name of the script to add the inline script to.
	 * @param string $data     String containing the JavaScript to be added.
	 * @param string $position Optional. Whether to add the inline script before the handle
	 *                         or after. Default 'after'.
	 * @return bool True on success, false on failure.
	 */
	public function wp_add_inline_script( string $handle, string $data, string $position = 'after' ): bool;
}

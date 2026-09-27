<?php declare( strict_types=1 );
/**
 * WordPress functions WPAL deliberately doesn't wrap, and why. Read by `bin/wpal coverage`.
 *
 * Private (`_`-prefixed or `@access private`) and deprecated functions are ignored automatically.
 * Everything else is either wrapped by a Service or shows up as untriaged.
 */

return [
	// Function name, or a /regex/ matched against it => reason.
	'ignore'       => [
		'/^wp_ajax_/'    => 'admin AJAX handler',
		'/^upgrade_\d+$/' => 'database upgrade step',
	],

	// Source file, or a directory ending in "/" => reason.
	'ignore_files' => [
		'wp-includes/compat.php' => 'PHP polyfill',
		'wp-includes/blocks/'    => 'core block internals',
	],
];

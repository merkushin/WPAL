<?php
define( 'OBJECT', 'OBJECT' );
define( 'MINUTE_IN_SECONDS', 60 );

function wp_initial_constants() {
	define( 'HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS );
}

/**
 * Retrieves a thing.
 *
 * @since 3.0.0
 *
 * @param int $id Thing ID.
 * @return WP_Post|stdClass|SimplePie\SimplePie|null The thing.
 */
function get_thing( $id, $output = OBJECT, array $args = array(), &$found = null ) {}

/**
 * @since 2.0.0
 */
function renamed_function() {
	_deprecated_function( __FUNCTION__, '5.5.0', 'new_function()' );
}

/**
 * @since 4.0.0
 * @access private
 */
function internal_but_unprefixed() {}

function get_the_ID() {}

class WP_Thing {
	public function get_thing() {}
}

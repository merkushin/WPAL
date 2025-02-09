<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Service;

interface Transient {
	/**
	 * Retrieves the value of a transient.
	 *
	 * If the transient does not exist, does not have a value, or has expired,
	 * then the return value will be false.
	 *
	 * @since 2.8.0
	 *
	 * @param string $transient Transient name. Expected to not be SQL-escaped.
	 * @return mixed Value of transient.
	 */
	function get_transient( string $transient );

	/**
	 * Sets/updates the value of a transient.
	 *
	 * You do not need to serialize values. If the value needs to be serialized,
	 * then it will be serialized before it is set.
	 *
	 * @since 2.8.0
	 *
	 * @param string $transient  Transient name. Expected to not be SQL-escaped.
	 *                           Must be 172 characters or fewer in length.
	 * @param mixed  $value      Transient value. Must be serializable if non-scalar.
	 *                           Expected to not be SQL-escaped.
	 * @param int    $expiration Optional. Time until expiration in seconds. Default 0 (no expiration).
	 * @return bool True if the value was set, false otherwise.
	 */
	function set_transient( string $transient, $value, int $expiration = 0 ): bool;

	/**
	 * Deletes a transient.
	 *
	 * @since 2.8.0
	 *
	 * @param string $transient Transient name. Expected to not be SQL-escaped.
	 * @return bool True if the transient was deleted, false otherwise.
	 */
	function delete_transient( string $transient ): bool;
}

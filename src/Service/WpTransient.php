<?php declare(strict_types=1);

namespace Merkushin\Wpal\Service;

final class WpTransient implements Transient {

	public function get_transient( string $transient ) {
		return get_transient( $transient );
	}

	public function set_transient( string $transient, $value, int $expiration = 0 ): bool {
		return set_transient( $transient, $value, $expiration );
	}

	public function delete_transient(string $transient): bool {
		return delete_transient($transient);
	}
}

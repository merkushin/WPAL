<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Exception;

use LogicException;

/**
 * Something was registered after WordPress stopped accepting registrations for it.
 */
final class RegistrationClosed extends LogicException implements WpalException {
	public function __construct( string $what, string $hook ) {
		parent::__construct( "Too late to register {$what}: WordPress already ran the {$hook} action. Register it earlier, e.g. when your plugin loads." );
	}
}

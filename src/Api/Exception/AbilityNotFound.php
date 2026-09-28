<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Exception;

use RuntimeException;

final class AbilityNotFound extends RuntimeException implements WpalException {
	public function __construct( public readonly string $abilityName ) {
		parent::__construct( "Ability {$abilityName} is not registered." );
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Exception;

use Throwable;

/**
 * Every exception the Api layer throws implements this.
 */
interface WpalException extends Throwable {
}

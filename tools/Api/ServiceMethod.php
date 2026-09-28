<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

/**
 * A method on a `Service` interface.
 */
final class ServiceMethod {
	public function __construct(
		public readonly string $service,
		public readonly FunctionSignature $signature,
		public readonly string $file,
	) {
	}

	public function label(): string {
		return $this->service . '::' . $this->signature->name . '()';
	}
}

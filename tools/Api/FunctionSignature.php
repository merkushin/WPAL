<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

/**
 * A WordPress function, or a WPAL service method standing in for one.
 */
final class FunctionSignature {
	/**
	 * @param Parameter[] $parameters
	 * @param string|null $deprecated Version it was deprecated in, '' when the version is unknown, null if not deprecated.
	 * @param string|null $file       Source file relative to the WordPress root.
	 * @param bool        $pluggable  Declared inside a `function_exists()` guard, so sites may replace it.
	 */
	public function __construct(
		public readonly string $name,
		public readonly array $parameters = [],
		public readonly ?string $returnType = null,
		public readonly ?string $doc = null,
		public readonly ?string $since = null,
		public readonly ?string $deprecated = null,
		public readonly bool $private = false,
		public readonly ?string $file = null,
		public readonly bool $pluggable = false,
	) {
	}

	public function isDeprecated(): bool {
		return $this->deprecated !== null;
	}

	/**
	 * Human-readable signature, e.g. `has_filter($hook_name, $callback = false, $priority = false)`.
	 */
	public function describe(): string {
		return $this->name . '(' . implode( ', ', array_map( static fn ( Parameter $p ): string => $p->describe(), $this->parameters ) ) . ')';
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array {
		$data = [
			'parameters' => array_map( static fn ( Parameter $p ): array => $p->toArray(), $this->parameters ),
		];
		foreach ( [ 'returnType', 'since', 'deprecated', 'file', 'doc' ] as $key ) {
			if ( $this->$key !== null ) {
				$data[ $key ] = $this->$key;
			}
		}
		if ( $this->private ) {
			$data['private'] = true;
		}
		if ( $this->pluggable ) {
			$data['pluggable'] = true;
		}

		return $data;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public static function fromArray( string $name, array $data ): self {
		return new self(
			$name,
			array_map( static fn ( array $p ): Parameter => Parameter::fromArray( $p ), $data['parameters'] ?? [] ),
			$data['returnType'] ?? null,
			$data['doc'] ?? null,
			$data['since'] ?? null,
			$data['deprecated'] ?? null,
			(bool) ( $data['private'] ?? false ),
			$data['file'] ?? null,
			(bool) ( $data['pluggable'] ?? false ),
		);
	}
}

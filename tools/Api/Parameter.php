<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

/**
 * One parameter of a function or method signature.
 */
final class Parameter {
	/**
	 * @param string      $name            Name without the leading `$`.
	 * @param string|null $type            Native type declaration as written, e.g. `?string`.
	 * @param string|null $default         Default value as written in source, e.g. `OBJECT` or `array()`.
	 * @param bool        $defaultResolved Whether the default could be evaluated to a constant value.
	 * @param mixed       $defaultValue    The evaluated default, when resolved.
	 */
	public function __construct(
		public readonly string $name,
		public readonly ?string $type = null,
		public readonly ?string $default = null,
		public readonly bool $defaultResolved = false,
		public readonly mixed $defaultValue = null,
		public readonly bool $byRef = false,
		public readonly bool $variadic = false,
	) {
	}

	public function hasDefault(): bool {
		return $this->default !== null;
	}

	/**
	 * Human-readable form, e.g. `&$terms`, `...$args`, `$output = OBJECT`.
	 */
	public function describe(): string {
		$text = ( $this->type !== null ? $this->type . ' ' : '' )
			. ( $this->byRef ? '&' : '' )
			. ( $this->variadic ? '...' : '' )
			. '$' . $this->name;

		return $this->default !== null ? $text . ' = ' . $this->default : $text;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array {
		$data = [ 'name' => $this->name ];
		if ( $this->type !== null ) {
			$data['type'] = $this->type;
		}
		if ( $this->default !== null ) {
			$data['default'] = $this->default;
			if ( $this->defaultResolved ) {
				$data['defaultValue'] = $this->defaultValue;
			}
		}
		if ( $this->byRef ) {
			$data['byRef'] = true;
		}
		if ( $this->variadic ) {
			$data['variadic'] = true;
		}

		return $data;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public static function fromArray( array $data ): self {
		return new self(
			(string) $data['name'],
			$data['type'] ?? null,
			$data['default'] ?? null,
			array_key_exists( 'defaultValue', $data ),
			$data['defaultValue'] ?? null,
			(bool) ( $data['byRef'] ?? false ),
			(bool) ( $data['variadic'] ?? false ),
		);
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Abilities;

/**
 * A registered ability, as other code sees it.
 */
final readonly class AbilityInfo {
	/**
	 * @param array<string, mixed> $inputSchema
	 * @param array<string, mixed> $outputSchema
	 * @param array<string, mixed> $meta
	 */
	public function __construct(
		public string $name,
		public string $label,
		public string $description,
		public string $category,
		public array $inputSchema = [],
		public array $outputSchema = [],
		public ?bool $readonly = null,
		public ?bool $destructive = null,
		public ?bool $idempotent = null,
		public bool $public = false,
		public bool $showInRest = false,
		public array $meta = [],
	) {
	}

	public static function fromDefinition( Ability $ability ): self {
		return new self(
			$ability->name,
			$ability->label,
			$ability->description,
			$ability->category,
			$ability->inputSchema,
			$ability->outputSchema,
			$ability->readonly,
			$ability->destructive,
			$ability->idempotent,
			$ability->public,
			$ability->showInRest,
			$ability->meta,
		);
	}

	/**
	 * @param object $ability A `WP_Ability`.
	 */
	public static function fromWordPress( object $ability ): self {
		$call = static fn ( string $method, mixed $default ): mixed => method_exists( $ability, $method ) ? $ability->$method() : $default;
		$meta = $call( 'get_meta', [] );
		$meta = is_array( $meta ) ? $meta : [];
		$flag = static fn ( string $key ): ?bool => isset( $meta['annotations'][ $key ] ) ? (bool) $meta['annotations'][ $key ] : null;

		return new self(
			(string) $call( 'get_name', '' ),
			(string) $call( 'get_label', '' ),
			(string) $call( 'get_description', '' ),
			(string) $call( 'get_category', '' ),
			(array) $call( 'get_input_schema', [] ),
			(array) $call( 'get_output_schema', [] ),
			$flag( 'readonly' ),
			$flag( 'destructive' ),
			$flag( 'idempotent' ),
			(bool) ( $meta['public'] ?? false ),
			(bool) ( $meta['show_in_rest'] ?? false ),
			array_diff_key( $meta, array_flip( [ 'annotations', 'public', 'show_in_rest' ] ) ),
		);
	}
}

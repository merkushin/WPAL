<?php
// Minimal stand-in for WordPress's WP_Ability, loaded by tests that need one.

class WP_Ability {
	/** @var list<mixed> */
	public array $executed = [];

	/**
	 * @param array<string, mixed> $args
	 */
	public function __construct( private string $name, private array $args, private mixed $result = null ) {
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_label(): string {
		return $this->args['label'] ?? '';
	}

	public function get_description(): string {
		return $this->args['description'] ?? '';
	}

	public function get_category(): string {
		return $this->args['category'] ?? '';
	}

	public function get_input_schema(): array {
		return $this->args['input_schema'] ?? [];
	}

	public function get_output_schema(): array {
		return $this->args['output_schema'] ?? [];
	}

	public function get_meta(): array {
		return $this->args['meta'] ?? [];
	}

	public function execute( mixed $input = null ): mixed {
		$this->executed[] = $input;

		return $this->result;
	}
}

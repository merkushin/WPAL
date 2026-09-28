<?php
// Minimal stand-in for WordPress's WP_AI_Client_Prompt_Builder: records calls, returns a canned result.

class WP_AI_Client_Prompt_Builder {
	/** @var list<array{string, list<mixed>}> */
	public array $calls = [];

	public function __construct( public mixed $result = '' ) {
	}

	/**
	 * @param list<mixed> $args
	 */
	public function __call( string $method, array $args ): mixed {
		$this->calls[] = [ $method, $args ];
		if ( $method === 'generate_text' ) {
			return $this->result;
		}
		if ( $method === 'is_supported_for_text_generation' ) {
			return true;
		}

		return $this;
	}
}

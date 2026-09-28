<?php
// Minimal stand-in for WordPress's WP_Error, loaded by tests that need one.

class WP_Error {
	public function __construct( private string $code = '', private string $message = '', private mixed $data = null ) {
	}

	public function get_error_code(): string {
		return $this->code;
	}

	public function get_error_message(): string {
		return $this->message;
	}

	public function get_error_data(): mixed {
		return $this->data;
	}
}

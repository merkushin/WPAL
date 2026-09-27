<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

/**
 * Minimal reader for the docblock tags the tools care about.
 */
final class DocBlock {
	public function __construct( private ?string $text ) {
	}

	public function text(): ?string {
		return $this->text;
	}

	public function has( string $tag ): bool {
		return $this->text !== null && preg_match( '/^\s*\*\s*@' . preg_quote( $tag, '/' ) . '\b/m', $this->text ) === 1;
	}

	/**
	 * First value of a tag, e.g. `5.9.0` for `@since 5.9.0 Added the $priority parameter.`
	 */
	public function first( string $tag ): ?string {
		if ( $this->text === null || ! preg_match( '/^\s*\*\s*@' . preg_quote( $tag, '/' ) . '(?:[ \t]+([^\s*]+))?/m', $this->text, $m ) ) {
			return null;
		}

		return $m[1] ?? '';
	}
}

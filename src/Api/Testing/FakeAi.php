<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use LogicException;
use Merkushin\Wpal\Api\Ai;
use Merkushin\Wpal\Api\Ai\Prompt;
use Merkushin\Wpal\Api\Exception\WordPressError;

/**
 * Scripted AI for tests: returns the responses you give it, in order, and records every prompt.
 *
 *     $ai = new FakeAi( [ 'A short summary.' ] );
 *     // ... run code that calls $ai->prompt( ... )->generateText() ...
 *     $this->assertStringContainsString( 'Summarize', $ai->prompts[0]->texts[0] );
 */
final class FakeAi implements Ai {
	/** @var list<Prompt> Prompts generated so far. */
	public private(set) array $prompts = [];

	/**
	 * @param list<string|array<mixed>|WordPressError> $responses Returned in order: text, decoded JSON, or an error to throw.
	 */
	public function __construct( private array $responses = [], private readonly bool $available = true ) {
	}

	public function isAvailable(): bool {
		return $this->available;
	}

	public function prompt( string $text = '' ): Prompt {
		return new Prompt( $this, $text );
	}

	public function supports( Prompt $prompt ): bool {
		return $this->available;
	}

	public function generateText( Prompt $prompt ): string {
		$response = $this->next( $prompt );

		return is_array( $response ) ? (string) json_encode( $response ) : $response;
	}

	public function generateJson( Prompt $prompt, ?array $schema = null ): array {
		$response = $this->next( $prompt );
		if ( is_array( $response ) ) {
			return $response;
		}

		$data = json_decode( $response, true );
		if ( ! is_array( $data ) ) {
			throw new WordPressError( "The AI response isn't valid JSON.", 'wpal_ai_invalid_json', $response );
		}

		return $data;
	}

	/**
	 * @return string|array<mixed>
	 */
	private function next( Prompt $prompt ): string|array {
		$this->prompts[] = $prompt;
		if ( ! $this->available ) {
			throw new WordPressError( 'AI features are not available on this site.', 'wpal_ai_unavailable' );
		}
		if ( $this->responses === [] ) {
			throw new LogicException( 'FakeAi ran out of responses; pass one per generate call.' );
		}

		$response = array_shift( $this->responses );
		if ( $response instanceof WordPressError ) {
			throw $response;
		}

		return $response;
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Ai;

use Merkushin\Wpal\Api\Ai;
use Merkushin\Wpal\Api\Exception\WordPressError;

/**
 * A prompt for the site's configured AI provider. Configure it, then generate.
 *
 *     $summary = $wp->ai()->prompt( "Summarize:\n\n{$post->content}" )
 *         ->system( 'You write one-paragraph summaries.' )
 *         ->maxTokens( 200 )
 *         ->generateText();
 */
final class Prompt {
	/** @var list<string> */
	public private(set) array $texts = [];

	public private(set) ?string $system = null;

	public private(set) ?float $temperature = null;

	public private(set) ?int $maxTokens = null;

	/** @var list<string> Model IDs in order of preference. */
	public private(set) array $models = [];

	public private(set) ?string $provider = null;

	public function __construct( private readonly Ai $ai, string $text = '' ) {
		if ( $text !== '' ) {
			$this->texts[] = $text;
		}
	}

	/**
	 * Adds text to the message.
	 */
	public function text( string $text ): self {
		$this->texts[] = $text;

		return $this;
	}

	public function system( string $instruction ): self {
		$this->system = $instruction;

		return $this;
	}

	public function temperature( float $temperature ): self {
		$this->temperature = $temperature;

		return $this;
	}

	public function maxTokens( int $maxTokens ): self {
		$this->maxTokens = $maxTokens;

		return $this;
	}

	/**
	 * Preferred models, best first; WordPress uses the first one available.
	 */
	public function models( string ...$models ): self {
		$this->models = array_values( $models );

		return $this;
	}

	/**
	 * @param string $provider Provider ID, e.g. `anthropic`.
	 */
	public function provider( string $provider ): self {
		$this->provider = $provider;

		return $this;
	}

	/**
	 * Whether the site's AI setup can generate text for this prompt.
	 */
	public function isSupported(): bool {
		return $this->ai->supports( $this );
	}

	/**
	 * @throws WordPressError When generation fails or no provider is configured.
	 */
	public function generateText(): string {
		return $this->ai->generateText( $this );
	}

	/**
	 * Asks for JSON and decodes it.
	 *
	 * @param array<string, mixed>|null $schema JSON Schema the response must match.
	 * @return array<mixed>
	 * @throws WordPressError When generation fails or the response isn't JSON.
	 */
	public function generateJson( ?array $schema = null ): array {
		return $this->ai->generateJson( $this, $schema );
	}
}

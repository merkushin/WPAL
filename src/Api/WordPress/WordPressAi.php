<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use JsonException;
use Merkushin\Wpal\Api\Ai;
use Merkushin\Wpal\Api\Ai\Prompt;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Service\Ai as AiService;

final class WordPressAi implements Ai {
	public function __construct( private readonly AiService $ai ) {
	}

	public function isAvailable(): bool {
		return $this->ai->wp_supports_ai();
	}

	public function prompt( string $text = '' ): Prompt {
		return new Prompt( $this, $text );
	}

	public function supports( Prompt $prompt ): bool {
		return $this->isAvailable() && $this->builder( $prompt )->is_supported_for_text_generation() === true;
	}

	public function generateText( Prompt $prompt ): string {
		return $this->text( $this->builder( $prompt ) );
	}

	public function generateJson( Prompt $prompt, ?array $schema = null ): array {
		$text = $this->text( $this->builder( $prompt )->as_json_response( $schema ) );

		try {
			$data = json_decode( $text, true, 512, JSON_THROW_ON_ERROR );
		} catch ( JsonException $e ) {
			throw new WordPressError( "The AI response isn't valid JSON: {$e->getMessage()}", 'wpal_ai_invalid_json', $text );
		}

		return is_array( $data ) ? $data : [ $data ];
	}

	/**
	 * WordPress's prompt builder, configured from a Prompt.
	 */
	private function builder( Prompt $prompt ): \WP_AI_Client_Prompt_Builder {
		$texts   = $prompt->texts;
		$builder = $this->ai->wp_ai_client_prompt( $texts === [] ? null : array_shift( $texts ) );
		foreach ( $texts as $text ) {
			$builder = $builder->with_text( $text );
		}
		if ( $prompt->system !== null ) {
			$builder = $builder->using_system_instruction( $prompt->system );
		}
		if ( $prompt->temperature !== null ) {
			$builder = $builder->using_temperature( $prompt->temperature );
		}
		if ( $prompt->maxTokens !== null ) {
			$builder = $builder->using_max_tokens( $prompt->maxTokens );
		}
		if ( $prompt->models !== [] ) {
			$builder = $builder->using_model_preference( ...$prompt->models );
		}
		if ( $prompt->provider !== null ) {
			$builder = $builder->using_provider( $prompt->provider );
		}

		return $builder;
	}

	private function text( \WP_AI_Client_Prompt_Builder $builder ): string {
		$result = $builder->generate_text();
		if ( WordPressError::isWpError( $result ) ) {
			throw WordPressError::fromWpError( $result );
		}

		return (string) $result;
	}
}

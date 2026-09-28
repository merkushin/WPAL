<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Merkushin\Wpal\Api\Ai\Prompt;
use Merkushin\Wpal\Api\Exception\WordPressError;

/**
 * The site's AI provider, through WordPress's AI Client.
 */
interface Ai {
	/**
	 * Whether the site has AI features enabled and a provider set up.
	 */
	public function isAvailable(): bool;

	/**
	 * Starts a prompt; finish with generateText() or generateJson().
	 */
	#[\NoDiscard]
	public function prompt( string $text = '' ): Prompt;

	public function supports( Prompt $prompt ): bool;

	/**
	 * @throws WordPressError
	 */
	public function generateText( Prompt $prompt ): string;

	/**
	 * @param array<string, mixed>|null $schema
	 * @return array<mixed>
	 * @throws WordPressError
	 */
	public function generateJson( Prompt $prompt, ?array $schema = null ): array;
}

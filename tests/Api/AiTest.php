<?php declare(strict_types=1);

namespace Merkushin\Wpal\Tests\Api;

use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Api\Testing\FakeAi;
use Merkushin\Wpal\Api\WordPress\WordPressAi;
use Merkushin\Wpal\Service\Ai as AiService;
use Merkushin\Wpal\Tests\Api\Stubs\Stubs;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Merkushin\Wpal\Api\WordPress\WordPressAi
 * @covers \Merkushin\Wpal\Api\Testing\FakeAi
 * @covers \Merkushin\Wpal\Api\Ai\Prompt
 */
class AiTest extends TestCase
{
	public function testGenerateText_WhenConfigured_PassesSettingsToWordPress(): void
	{
		$builder = Stubs::promptBuilder( 'Summary.' );
		$service = $this->createMock( AiService::class );
		$service->expects( self::once() )->method( 'wp_ai_client_prompt' )->with( 'Summarize this.' )->willReturn( $builder );

		$text = ( new WordPressAi( $service ) )->prompt( 'Summarize this.' )
			->text( 'Post body' )
			->system( 'Be brief.' )
			->temperature( 0.2 )
			->maxTokens( 100 )
			->models( 'claude-sonnet-5', 'claude-haiku-4-5' )
			->generateText();

		self::assertSame( 'Summary.', $text );
		self::assertSame(
			[
				[ 'with_text', [ 'Post body' ] ],
				[ 'using_system_instruction', [ 'Be brief.' ] ],
				[ 'using_temperature', [ 0.2 ] ],
				[ 'using_max_tokens', [ 100 ] ],
				[ 'using_model_preference', [ 'claude-sonnet-5', 'claude-haiku-4-5' ] ],
				[ 'generate_text', [] ],
			],
			Stubs::calls( $builder )
		);
	}

	public function testGenerateText_WhenWordPressFails_ThrowsWordPressError(): void
	{
		$service = $this->createMock( AiService::class );
		$service->method( 'wp_ai_client_prompt' )->willReturn( Stubs::promptBuilder( Stubs::error( 'no_provider', 'No AI provider is set up.' ) ) );

		$this->expectException( WordPressError::class );
		$this->expectExceptionMessage( 'No AI provider is set up.' );

		( new WordPressAi( $service ) )->prompt( 'Hi' )->generateText();
	}

	public function testGenerateJson_WhenResponseIsJson_DecodesIt(): void
	{
		$builder = Stubs::promptBuilder( '{"tags":["a","b"]}' );
		$service = $this->createMock( AiService::class );
		$service->method( 'wp_ai_client_prompt' )->willReturn( $builder );

		$data = ( new WordPressAi( $service ) )->prompt( 'Suggest tags' )->generateJson( [ 'type' => 'object' ] );

		self::assertSame( [ 'tags' => [ 'a', 'b' ] ], $data );
		self::assertSame( [ 'as_json_response', [ [ 'type' => 'object' ] ] ], Stubs::calls( $builder )[0] );
	}

	public function testFake_WhenGenerating_ReturnsScriptedResponsesAndRecordsPrompts(): void
	{
		$ai = new FakeAi( [ 'First.', [ 'ok' => true ], new WordPressError( 'Rate limited.', 'rate_limited' ) ] );

		self::assertSame( 'First.', $ai->prompt( 'One' )->generateText() );
		self::assertSame( [ 'ok' => true ], $ai->prompt( 'Two' )->system( 'JSON only' )->generateJson() );
		self::assertSame( [ 'One' ], $ai->prompts[0]->texts );
		self::assertSame( 'JSON only', $ai->prompts[1]->system );

		$this->expectException( WordPressError::class );
		$ai->prompt( 'Three' )->generateText();
	}

	public function testFake_WhenUnavailable_Throws(): void
	{
		$ai = new FakeAi( [ 'x' ], available: false );

		self::assertFalse( $ai->isAvailable() );
		$this->expectException( WordPressError::class );
		$ai->prompt( 'Hi' )->generateText();
	}
}

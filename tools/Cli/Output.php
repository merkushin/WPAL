<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Cli;

/**
 * Writes results as text or JSON.
 */
final class Output {
	/** @var resource */
	private $stream;

	/**
	 * @param resource|null $stream Defaults to STDOUT.
	 */
	public function __construct( $stream = null ) {
		$this->stream = $stream ?? STDOUT;
	}

	public function line( string $text = '' ): void {
		fwrite( $this->stream, $text . "\n" );
	}

	/**
	 * @param array<mixed> $data
	 */
	public function json( array $data ): void {
		$this->line( (string) json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	}
}

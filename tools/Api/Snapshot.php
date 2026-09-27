<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

use JsonException;
use RuntimeException;

/**
 * The public function API of one WordPress version.
 */
final class Snapshot {
	private const FORMAT = 1;

	/**
	 * @param array<string, FunctionSignature> $functions Keyed by function name.
	 * @param array<string, mixed>             $constants Scalar constants from `define()` calls, used to resolve defaults.
	 */
	public function __construct(
		public readonly string $version,
		public readonly array $functions,
		public readonly array $constants = [],
	) {
	}

	public function get( string $name ): ?FunctionSignature {
		return $this->functions[ strtolower( $name ) ] ?? null;
	}

	public static function load( string $file ): self {
		$json = @file_get_contents( $file );
		if ( $json === false ) {
			throw new RuntimeException( "Cannot read snapshot {$file}." );
		}

		try {
			$data = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );
		} catch ( JsonException $e ) {
			throw new RuntimeException( "Snapshot {$file} is not valid JSON: {$e->getMessage()}", 0, $e );
		}

		if ( ( $data['format'] ?? null ) !== self::FORMAT ) {
			throw new RuntimeException( "Snapshot {$file} has an unsupported format; regenerate it with `bin/wpal snapshot`." );
		}

		$functions = [];
		foreach ( $data['functions'] as $name => $function ) {
			$functions[ strtolower( $name ) ] = FunctionSignature::fromArray( $name, $function );
		}

		return new self( (string) $data['wordpress'], $functions, $data['constants'] ?? [] );
	}

	public function save( string $file ): void {
		$functions = $this->functions;
		ksort( $functions, SORT_STRING );
		$constants = $this->constants;
		ksort( $constants, SORT_STRING );

		$data = [
			'format'    => self::FORMAT,
			'wordpress' => $this->version,
			'constants' => $constants,
			'functions' => array_map( static fn ( FunctionSignature $f ): array => $f->toArray(), $functions ),
		];

		$dir = dirname( $file );
		if ( ! is_dir( $dir ) && ! mkdir( $dir, 0777, true ) && ! is_dir( $dir ) ) {
			throw new RuntimeException( "Cannot create directory {$dir}." );
		}

		$json = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR );
		file_put_contents( $file, $json . "\n" );
	}
}

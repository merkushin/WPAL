<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\WordPress;

use RuntimeException;
use ZipArchive;

/**
 * Fetches WordPress releases from wordpress.org into a local cache.
 */
final class Downloader {
	private const VERSION_CHECK_URL = 'https://api.wordpress.org/core/version-check/1.7/';

	private const RELEASE_URL = 'https://wordpress.org/wordpress-%s.zip';

	public function __construct( private string $cacheDir ) {
	}

	public function latestVersion(): string {
		$data    = json_decode( $this->fetch( self::VERSION_CHECK_URL ), true );
		$version = $data['offers'][0]['current'] ?? null;
		if ( ! is_string( $version ) ) {
			throw new RuntimeException( 'Unexpected response from ' . self::VERSION_CHECK_URL . '.' );
		}

		return $version;
	}

	/**
	 * @return string Path to the extracted `wordpress` directory.
	 */
	public function download( string $version ): string {
		if ( ! preg_match( '/^\d+\.\d+(\.\d+)?(-[a-z0-9.-]+)?$/i', $version ) ) {
			throw new RuntimeException( "\"{$version}\" is not a WordPress version." );
		}

		$dir  = $this->cacheDir . '/' . $version;
		$root = $dir . '/wordpress';
		if ( is_file( $root . '/wp-includes/version.php' ) ) {
			return $root;
		}

		if ( ! class_exists( ZipArchive::class ) ) {
			throw new RuntimeException( 'The zip PHP extension is required to extract WordPress.' );
		}
		if ( ! is_dir( $dir ) && ! mkdir( $dir, 0777, true ) && ! is_dir( $dir ) ) {
			throw new RuntimeException( "Cannot create {$dir}." );
		}

		$url = sprintf( self::RELEASE_URL, $version );
		fwrite( STDERR, "Downloading {$url}\n" );
		$zipFile = $dir . '/wordpress.zip';
		file_put_contents( $zipFile, $this->fetch( $url ) );

		$zip = new ZipArchive();
		if ( $zip->open( $zipFile ) !== true ) {
			throw new RuntimeException( "Cannot open {$zipFile}." );
		}
		$zip->extractTo( $dir );
		$zip->close();
		unlink( $zipFile );

		if ( ! is_file( $root . '/wp-includes/version.php' ) ) {
			throw new RuntimeException( "{$url} did not contain a WordPress release." );
		}

		return $root;
	}

	private function fetch( string $url ): string {
		$context = stream_context_create(
			[
				'http' => [
					'timeout'       => 120,
					'user_agent'    => 'wpal-tools (+https://github.com/merkushin/WPAL)',
					'ignore_errors' => true,
				],
			]
		);

		$stream = @fopen( $url, 'rb', false, $context );
		if ( $stream === false ) {
			throw new RuntimeException( "Cannot download {$url}." );
		}
		$headers = stream_get_meta_data( $stream )['wrapper_data'] ?? [];
		$body    = stream_get_contents( $stream );
		fclose( $stream );

		$status = 0;
		// After redirects, the last status line is the final response.
		foreach ( is_array( $headers ) ? $headers : [] as $header ) {
			if ( is_string( $header ) && preg_match( '#^HTTP/\S+\s+(\d{3})#', $header, $m ) ) {
				$status = (int) $m[1];
			}
		}
		if ( $body === false || $status !== 200 ) {
			throw new RuntimeException( "Cannot download {$url} (HTTP {$status})." );
		}

		return $body;
	}
}

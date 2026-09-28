<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Generate\ServiceGenerator;
use Merkushin\Wpal\Tools\Workspace;
use RuntimeException;

final class FixCommand implements Command {
	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'fix';
	}

	public function summary(): string {
		return 'Generate src/Service, ServiceFactory and its test from wpal.map.php and api/wordpress.json.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal fix [--check] [--format=json]

  --check  Don't write anything; exit 1 if any generated file is out of date.

Generated files carry a "Do not edit" marker. Files in src/Service with the marker that are no longer
generated are deleted.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'check', 'format' ] );
		$check = $input->flag( 'check' );

		$wordpress = $this->workspace->snapshot( 'current' );
		$files     = ( new ServiceGenerator( $wordpress, $this->workspace->map() ) )->generate();
		$root      = $this->workspace->root;

		$changed = [];
		foreach ( $files as $path => $contents ) {
			$existing = is_file( "{$root}/{$path}" ) ? file_get_contents( "{$root}/{$path}" ) : null;
			if ( $existing === $contents ) {
				continue;
			}
			$changed[] = [ 'file' => $path, 'action' => $existing === null ? 'created' : 'updated' ];
			if ( ! $check ) {
				$this->write( "{$root}/{$path}", $contents );
			}
		}

		foreach ( glob( "{$root}/src/Service/*.php" ) ?: [] as $file ) {
			$path = substr( $file, strlen( $root ) + 1 );
			if ( isset( $files[ $path ] ) || ! str_contains( (string) file_get_contents( $file ), ServiceGenerator::MARKER ) ) {
				continue;
			}
			$changed[] = [ 'file' => $path, 'action' => 'deleted' ];
			if ( ! $check ) {
				unlink( $file );
			}
		}

		if ( $input->json() ) {
			$output->json( [ 'wordpress' => $wordpress->version, 'check' => $check, 'files' => $changed ] );
		} elseif ( $changed === [] ) {
			$output->line( sprintf( 'Generated files are up to date with WordPress %s.', $wordpress->version ) );
		} else {
			$output->line( $check ? 'Out of date (run bin/wpal fix):' : "Generated from WordPress {$wordpress->version}:" );
			foreach ( $changed as $item ) {
				$output->line( sprintf( '  %-8s %s', $item['action'], $item['file'] ) );
			}
		}

		return $check && $changed !== [] ? self::FOUND : self::OK;
	}

	private function write( string $file, string $contents ): void {
		$dir = dirname( $file );
		if ( ! is_dir( $dir ) && ! mkdir( $dir, 0777, true ) && ! is_dir( $dir ) ) {
			throw new RuntimeException( "Cannot create {$dir}." );
		}
		file_put_contents( $file, $contents );
	}
}

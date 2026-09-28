<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Docs\DocsGenerator;
use Merkushin\Wpal\Tools\Workspace;

final class DocsCommand implements Command {
	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'docs';
	}

	public function summary(): string {
		return 'Generate docs/api.md, docs/services.md and llms.txt from the code.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal docs [--check] [--format=json]

  --check  Don't write anything; exit 1 if any generated doc is out of date.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'check', 'format' ] );
		$check = $input->flag( 'check' );
		$root  = $this->workspace->root;

		$files   = ( new DocsGenerator( $root, $this->workspace->map(), $this->workspace->snapshot( 'current' ) ) )->generate();
		$changed = [];
		foreach ( $files as $path => $contents ) {
			$file = "{$root}/{$path}";
			if ( is_file( $file ) && file_get_contents( $file ) === $contents ) {
				continue;
			}
			$changed[] = $path;
			if ( ! $check ) {
				if ( ! is_dir( dirname( $file ) ) ) {
					mkdir( dirname( $file ), 0777, true );
				}
				file_put_contents( $file, $contents );
			}
		}

		if ( $input->json() ) {
			$output->json( [ 'check' => $check, 'files' => $changed ] );
		} elseif ( $changed === [] ) {
			$output->line( 'Docs are up to date.' );
		} else {
			$output->line( ( $check ? 'Out of date (run bin/wpal docs): ' : 'Wrote: ' ) . implode( ', ', $changed ) );
		}

		return $check && $changed !== [] ? self::FOUND : self::OK;
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Api\WordPressParser;
use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Workspace;

final class SnapshotCommand implements Command {
	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'snapshot';
	}

	public function summary(): string {
		return 'Record the function API of a WordPress version (default: latest) as the target in api/wordpress.json.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal snapshot [<version>|latest] [--source=<dir>] [--output=<file>] [--format=json]

  <version>        WordPress version to download from wordpress.org. Default: latest.
  --source=<dir>   Parse a local WordPress checkout instead of downloading.
  --output=<file>  Where to write the snapshot. Default: api/wordpress.json.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'source', 'output', 'format' ] );

		$source = $input->option( 'source' );
		if ( $source === null ) {
			$version    = $input->argument( 0 ) ?? 'latest';
			$downloader = $this->workspace->downloader();
			$source     = $downloader->download( $version === 'latest' ? $downloader->latestVersion() : $version );
		}

		$snapshot = ( new WordPressParser() )->parse( $source );
		$file     = $input->option( 'output' ) ?? $this->workspace->currentSnapshotFile();
		$snapshot->save( $file );

		$public = count( array_filter( $snapshot->functions, static fn ( FunctionSignature $f ): bool => ! $f->private && ! $f->isDeprecated() ) );

		if ( $input->json() ) {
			$output->json(
				[
					'wordpress' => $snapshot->version,
					'file'      => $file,
					'functions' => count( $snapshot->functions ),
					'public'    => $public,
				]
			);
		} else {
			$output->line( sprintf( 'Wrote %s: WordPress %s, %d functions (%d public, not deprecated).', $file, $snapshot->version, count( $snapshot->functions ), $public ) );
		}

		return self::OK;
	}
}

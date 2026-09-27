<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Coverage\Coverage;
use Merkushin\Wpal\Tools\Workspace;

final class CoverageCommand implements Command {
	private const TOP_FILES = 20;

	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'coverage';
	}

	public function summary(): string {
		return 'Report which WordPress functions are wrapped, deliberately ignored (wpal.map.php) or still untriaged.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal coverage [--wp=<spec>] [--untriaged] [--format=json]

  --wp=<spec>   current (api/wordpress.json, default), latest, a version, or a snapshot file.
  --untriaged   List every untriaged function, grouped by source file.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'wp', 'untriaged', 'format' ] );

		$wordpress = $this->workspace->snapshot( $input->option( 'wp' ) );
		$coverage  = ( new Coverage() )->compute( $wordpress, $this->workspace->serviceMethods( $wordpress ), $this->workspace->map() );

		if ( $input->json() ) {
			$output->json( [ 'wordpress' => $wordpress->version ] + $coverage );

			return self::OK;
		}

		$count     = static fn ( array $groups ): int => array_sum( array_map( 'count', $groups ) );
		$wrapped   = $count( $coverage['wrapped'] );
		$ignored   = $count( $coverage['ignored'] );
		$untriaged = $count( $coverage['untriaged'] );
		$wrappable = $wrapped + $untriaged;

		$output->line( sprintf( 'WordPress %s: %d functions', $wordpress->version, $coverage['total'] ) );
		$output->line( sprintf( '  Wrapped    %5d  (%d%% of %d not ignored)', $wrapped, $wrappable > 0 ? round( 100 * $wrapped / $wrappable ) : 0, $wrappable ) );
		$output->line( sprintf( '  Ignored    %5d', $ignored ) );
		foreach ( $coverage['ignored'] as $reason => $names ) {
			$output->line( sprintf( '    %-24s %5d', $reason, count( $names ) ) );
		}
		$output->line( sprintf( '  Untriaged  %5d', $untriaged ) );

		$output->line();
		$output->line( 'Wrapped by service:' );
		foreach ( $coverage['wrapped'] as $service => $names ) {
			$output->line( sprintf( '  %-24s %5d', $service, count( $names ) ) );
		}

		$files = $coverage['untriaged'];
		uasort( $files, static fn ( array $a, array $b ): int => count( $b ) <=> count( $a ) );

		if ( $input->flag( 'untriaged' ) ) {
			$output->line();
			$output->line( 'Untriaged by file:' );
			foreach ( $files as $file => $names ) {
				$output->line( sprintf( '  %s (%d)', $file, count( $names ) ) );
				$output->line( '    ' . implode( ', ', $names ) );
			}

			return self::OK;
		}

		$output->line();
		$output->line( sprintf( 'Untriaged by file (top %d; --untriaged lists all):', self::TOP_FILES ) );
		foreach ( array_slice( $files, 0, self::TOP_FILES, true ) as $file => $names ) {
			$output->line( sprintf( '  %-48s %5d', $file, count( $names ) ) );
		}

		return self::OK;
	}
}

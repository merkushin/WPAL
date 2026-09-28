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
		return 'Report which WordPress functions are wrapped, planned or ignored (wpal.map.php), or untriaged; exits 1 on map mistakes.';
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

		$result = $coverage['problems'] === [] ? self::OK : self::FOUND;

		if ( $input->json() ) {
			$output->json( [ 'wordpress' => $wordpress->version ] + $coverage );

			return $result;
		}

		$count     = static fn ( array $groups ): int => array_sum( array_map( 'count', $groups ) );
		$wrapped   = $count( $coverage['wrapped'] );
		$planned   = $count( $coverage['planned'] );
		$ignored   = $count( $coverage['ignored'] );
		$untriaged = $count( $coverage['untriaged'] );
		$wrappable = $wrapped + $planned + $untriaged;

		$output->line( sprintf( 'WordPress %s: %d functions', $wordpress->version, $coverage['total'] ) );
		$output->line( sprintf( '  Wrapped    %5d  (%d%% of %d not ignored)', $wrapped, $wrappable > 0 ? round( 100 * $wrapped / $wrappable ) : 0, $wrappable ) );
		$output->line( sprintf( '  Planned    %5d  (assigned a service in wpal.map.php)', $planned ) );
		$output->line( sprintf( '  Ignored    %5d', $ignored ) );
		foreach ( $coverage['ignored'] as $reason => $names ) {
			$output->line( sprintf( '    %-24s %5d', $reason, count( $names ) ) );
		}
		$output->line( sprintf( '  Untriaged  %5d', $untriaged ) );

		$services = array_unique( array_merge( array_keys( $coverage['wrapped'] ), array_keys( $coverage['planned'] ) ) );
		sort( $services, SORT_STRING );
		$output->line();
		$output->line( sprintf( '%-26s %7s %7s', 'Service', 'wrapped', 'planned' ) );
		foreach ( $services as $service ) {
			$output->line( sprintf( '  %-24s %7d %7d', $service, count( $coverage['wrapped'][ $service ] ?? [] ), count( $coverage['planned'][ $service ] ?? [] ) ) );
		}

		if ( $coverage['problems'] !== [] ) {
			$output->line();
			$output->line( sprintf( 'Problems in wpal.map.php (%d):', count( $coverage['problems'] ) ) );
			foreach ( $coverage['problems'] as $problem ) {
				$output->line( '  ' . $problem );
			}
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

			return $result;
		}

		$output->line();
		$output->line( sprintf( 'Untriaged by file (top %d; --untriaged lists all):', self::TOP_FILES ) );
		foreach ( array_slice( $files, 0, self::TOP_FILES, true ) as $file => $names ) {
			$output->line( sprintf( '  %-48s %5d', $file, count( $names ) ) );
		}

		return $result;
	}
}

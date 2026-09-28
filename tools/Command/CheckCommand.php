<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Compare\Checker;
use Merkushin\Wpal\Tools\Compare\Difference;
use Merkushin\Wpal\Tools\Workspace;

final class CheckCommand implements Command {
	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'check';
	}

	public function summary(): string {
		return 'Compare src/Service with a WordPress version; exits 1 when methods drift.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal check [--wp=<spec>] [--format=json]

  --wp=<spec>  current (api/wordpress.json, default), latest, a version such as 7.1.2, or a snapshot file.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'wp', 'format' ] );

		$wordpress = $this->workspace->snapshot( $input->option( 'wp' ) );
		$methods   = $this->workspace->serviceMethods( $wordpress );
		$drift     = ( new Checker() )->check( $wordpress, $methods );

		$summary = [];
		foreach ( $drift as $item ) {
			foreach ( $item['differences'] as $difference ) {
				$summary[ $difference->kind ] = ( $summary[ $difference->kind ] ?? 0 ) + 1;
			}
		}
		ksort( $summary );

		if ( $input->json() ) {
			$output->json(
				[
					'wordpress' => $wordpress->version,
					'methods'   => count( $methods ),
					'drifting'  => count( $drift ),
					'summary'   => $summary,
					'drift'     => array_map(
						static fn ( array $item ): array => [
							'service'     => $item['method']->service,
							'method'      => $item['method']->signature->name,
							'file'        => 'src/Service/' . $item['method']->file,
							'differences' => array_map( static fn ( Difference $d ): array => $d->toArray(), $item['differences'] ),
						],
						$drift
					),
				]
			);

			return $drift === [] ? self::OK : self::FOUND;
		}

		if ( $drift === [] ) {
			$output->line( sprintf( 'All %d Service methods match WordPress %s.', count( $methods ), $wordpress->version ) );

			return self::OK;
		}

		$output->line( "Checking src/Service against WordPress {$wordpress->version}" );
		foreach ( $drift as $item ) {
			$output->line();
			$output->line( $item['method']->label() );
			foreach ( $item['differences'] as $difference ) {
				$output->line( sprintf( '  %-18s %s', $difference->kind, $difference->message ) );
			}
		}
		$output->line();
		$output->line(
			sprintf(
				'%d of %d methods drift from WordPress %s: %s.',
				count( $drift ),
				count( $methods ),
				$wordpress->version,
				implode( ', ', array_map( static fn ( string $kind, int $n ): string => "{$n} {$kind}", array_keys( $summary ), $summary ) )
			)
		);

		return self::FOUND;
	}
}

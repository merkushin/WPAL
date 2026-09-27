<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Api\FunctionSignature;
use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;
use Merkushin\Wpal\Tools\Compare\Difference;
use Merkushin\Wpal\Tools\Compare\SnapshotDiff;
use Merkushin\Wpal\Tools\Workspace;
use RuntimeException;

final class DiffCommand implements Command {
	public function __construct( private Workspace $workspace ) {
	}

	public function name(): string {
		return 'diff';
	}

	public function summary(): string {
		return 'Show what WordPress added, removed, deprecated or re-signed between two versions.';
	}

	public function usage(): string {
		return <<<'TXT'
wpal diff <from> [<to>] [--internals] [--format=json]

  <from>, <to>  current (api/wordpress.json), latest, a version such as 7.0, or a snapshot file.
                <to> defaults to latest, so `wpal diff current` shows what a new release changes.
  --internals   Also list new private and deprecated functions.

Functions wrapped by a Service are marked with the service name.
TXT;
	}

	public function run( Input $input, Output $output ): int {
		$input->assertOptions( [ 'internals', 'format' ] );

		$fromSpec = $input->argument( 0 );
		if ( $fromSpec === null ) {
			throw new RuntimeException( 'Missing <from>. Usage: ' . strtok( $this->usage(), "\n" ) );
		}

		$from    = $this->workspace->snapshot( $fromSpec );
		$to      = $this->workspace->snapshot( $input->argument( 1 ) ?? 'latest' );
		$wrapped = $this->workspace->serviceMethods( $to );
		$diff    = ( new SnapshotDiff() )->diff( $from, $to, $wrapped, $input->flag( 'internals' ) );

		if ( $input->json() ) {
			$entry = static function ( array $item ): array {
				/** @var FunctionSignature $function */
				$function = $item['function'];
				$data     = [
					'function'  => $function->name,
					'signature' => $function->describe(),
					'file'      => $function->file,
					'service'   => $item['service'],
				];
				if ( isset( $item['differences'] ) ) {
					$data['differences'] = array_map( static fn ( Difference $d ): array => $d->toArray(), $item['differences'] );
				}

				return $data;
			};

			$output->json(
				[
					'from'       => $from->version,
					'to'         => $to->version,
					'added'      => array_map( $entry, $diff['added'] ),
					'removed'    => array_map( $entry, $diff['removed'] ),
					'deprecated' => array_map( $entry, $diff['deprecated'] ),
					'changed'    => array_map( $entry, $diff['changed'] ),
				]
			);

			return self::OK;
		}

		$output->line( "WordPress {$from->version} → {$to->version}" );

		$sections = [
			'Added'      => $diff['added'],
			'Removed'    => $diff['removed'],
			'Deprecated' => $diff['deprecated'],
			'Changed'    => $diff['changed'],
		];
		foreach ( $sections as $title => $items ) {
			$output->line();
			$output->line( sprintf( '%s (%d)', $title, count( $items ) ) );
			foreach ( $items as $item ) {
				$service = $item['service'] !== null ? "  [Service\\{$item['service']}]" : '';
				$output->line( '  ' . ( $title === 'Added' ? $item['function']->describe() : $item['function']->name . '()' ) . $service );
				foreach ( $item['differences'] ?? [] as $difference ) {
					$output->line( '      ' . $difference->message );
				}
			}
		}

		return self::OK;
	}
}

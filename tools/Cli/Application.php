<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Cli;

use Merkushin\Wpal\Tools\Command\CheckCommand;
use Merkushin\Wpal\Tools\Command\Command;
use Merkushin\Wpal\Tools\Command\CoverageCommand;
use Merkushin\Wpal\Tools\Command\DiffCommand;
use Merkushin\Wpal\Tools\Command\SnapshotCommand;
use Merkushin\Wpal\Tools\Workspace;
use RuntimeException;

/**
 * `bin/wpal`: keeps `Service` in step with WordPress.
 */
final class Application {
	/** @var array<string, Command> */
	private array $commands = [];

	/**
	 * @param resource|null $errors Defaults to STDERR.
	 */
	public function __construct( Workspace $workspace, private Output $output = new Output(), private $errors = null ) {
		foreach ( [ new SnapshotCommand( $workspace ), new DiffCommand( $workspace ), new CheckCommand( $workspace ), new CoverageCommand( $workspace ) ] as $command ) {
			$this->commands[ $command->name() ] = $command;
		}
	}

	/**
	 * @param string[] $argv Including the script name.
	 */
	public function run( array $argv ): int {
		$name = $argv[1] ?? 'help';

		if ( in_array( $name, [ 'help', '--help', '-h' ], true ) ) {
			return $this->help( $argv[2] ?? null );
		}

		$command = $this->commands[ $name ] ?? null;
		if ( $command === null ) {
			$this->error( "Unknown command \"{$name}\". Run `bin/wpal help`." );

			return Command::ERROR;
		}

		try {
			return $command->run( Input::parse( array_slice( $argv, 2 ) ), $this->output );
		} catch ( RuntimeException $e ) {
			$this->error( $e->getMessage() );

			return Command::ERROR;
		}
	}

	private function help( ?string $name ): int {
		if ( $name !== null ) {
			$command = $this->commands[ $name ] ?? null;
			if ( $command === null ) {
				$this->error( "Unknown command \"{$name}\"." );

				return Command::ERROR;
			}
			$this->output->line( $command->summary() );
			$this->output->line();
			$this->output->line( $command->usage() );

			return Command::OK;
		}

		$this->output->line( 'Usage: bin/wpal <command> [arguments] [--format=json]' );
		$this->output->line();
		foreach ( $this->commands as $command ) {
			$this->output->line( sprintf( '  %-10s %s', $command->name(), $command->summary() ) );
		}
		$this->output->line();
		$this->output->line( 'Exit codes: 0 nothing found, 1 drift or changes found, 2 error. `bin/wpal help <command>` for details.' );

		return Command::OK;
	}

	private function error( string $message ): void {
		fwrite( $this->errors ?? STDERR, "Error: {$message}\n" );
	}
}

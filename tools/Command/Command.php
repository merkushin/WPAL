<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Command;

use Merkushin\Wpal\Tools\Cli\Input;
use Merkushin\Wpal\Tools\Cli\Output;

interface Command {
	/** Everything matched. */
	public const OK = 0;

	/** The command ran and found something: drift, changes, a problem to act on. */
	public const FOUND = 1;

	/** Bad usage or an error such as a failed download. */
	public const ERROR = 2;

	public function name(): string;

	/** One line for `wpal help`. */
	public function summary(): string;

	/** Arguments and options, for `wpal help <command>`. */
	public function usage(): string;

	public function run( Input $input, Output $output ): int;
}

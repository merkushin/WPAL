<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Prune;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Removes the services a plugin doesn't use from an installed copy of WPAL, used by bin/wpal-prune.
 *
 * Services are kept by name, per layer:
 *
 * - `Hooks` keeps the Service interface `Service\Hooks` and its `WpHooks` class.
 * - `api:Hooks` keeps the Api interface `Api\Hooks`, its WordPress implementation and its fake.
 *
 * Everything the kept classes refer to is kept too, transitively: the Api's value objects and exceptions, and the
 * Services an Api implementation is built on. Every other class is removed. A plugin that keeps only Services ships
 * no Api code, which needs PHP 8.4.
 *
 * Files directly in src/ (`ServiceFactory`, `Wpal`) are never followed: they refer to every service, but only load
 * one when its method is called, so methods for removed services must not be called. `ServiceFactory` is always kept,
 * `Wpal` only along with an Api service.
 *
 * scan() finds the services a plugin uses in its code, so the list doesn't need maintaining by hand.
 *
 * Ships with the package and runs on a plugin's build machine, so it stays PHP 7.4-compatible and dependency-free.
 */
final class Pruner {
	public const API_PREFIX = 'api:';

	private const ROOT_NAMESPACE = 'Merkushin\\Wpal\\';

	/** @var string */
	private $src;

	/** @var array<string, string> Class name => path relative to src/. */
	private $classes;

	public function __construct( string $src ) {
		if ( ! is_file( $src . '/ServiceFactory.php' ) || ! is_dir( $src . '/Service' ) ) {
			throw new InvalidArgumentException( "$src is not WPAL's src directory." );
		}

		$this->src     = rtrim( $src, '/' );
		$this->classes = $this->find_classes();
	}

	/**
	 * Names of the services in this copy of WPAL: Services (`Hooks`), then Api services (`api:Hooks`).
	 *
	 * @return string[]
	 */
	public function services(): array {
		$services = [];
		$api      = [];
		foreach ( $this->classes as $class => $file ) {
			if ( preg_match( '{^Service/(\w+)\.php$}', $file, $match ) && isset( $this->classes[ self::ROOT_NAMESPACE . 'Service\\Wp' . $match[1] ] ) ) {
				$services[] = $match[1];
			} elseif ( preg_match( '{^Api/(\w+)\.php$}', $file, $match ) && isset( $this->classes[ self::ROOT_NAMESPACE . 'Api\\WordPress\\WordPress' . $match[1] ] ) ) {
				$api[] = self::API_PREFIX . $match[1];
			}
		}
		sort( $services );
		sort( $api );

		return array_merge( $services, $api );
	}

	/**
	 * Finds the services that PHP files under the given directories use:
	 *
	 * - Services whose classes they refer to (`Merkushin\Wpal\Service\Posts`) or that they get from
	 *   `ServiceFactory::create_*()`.
	 * - Api services whose classes they refer to (`Merkushin\Wpal\Api\Posts\SortBy`) or, when they use `Wpal`, whose
	 *   accessor they call (`->posts()`).
	 *
	 * @param string[] $directories
	 * @return string[] Service names, as services() lists them.
	 * @throws InvalidArgumentException When the code refers to a WPAL class or service this copy doesn't have.
	 */
	public function scan( array $directories ): array {
		$known = [];
		foreach ( $this->services() as $service ) {
			$known[ strtolower( $service ) ] = $service;
		}

		$services = [];
		$missing  = [];
		$calls    = [];
		$wpal     = false;
		foreach ( $directories as $directory ) {
			if ( ! is_dir( $directory ) ) {
				throw new InvalidArgumentException( "$directory is not a directory." );
			}

			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $files as $file ) {
				if ( ! $file->isFile() || $file->getExtension() !== 'php' ) {
					continue;
				}

				foreach ( $this->names( $file->getPathname() ) as $name ) {
					if ( stripos( $name, self::ROOT_NAMESPACE ) !== 0 ) {
						continue;
					}

					$class   = substr( $name, strlen( self::ROOT_NAMESPACE ) );
					$service = null;
					if ( preg_match( '{^Service\\\\(?:Wp)?(\w+)$}i', $class, $match ) ) {
						$service = strtolower( $match[1] );
					} elseif ( preg_match( '{^Api\\\\(?:WordPress\\\\WordPress|Testing\\\\Fake)?(\w+)}i', $class, $match ) ) {
						$service = strtolower( self::API_PREFIX . $match[1] );
					}

					if ( strcasecmp( $class, 'Wpal' ) === 0 ) {
						$wpal = true;
					} elseif ( $service !== null && isset( $known[ $service ] ) ) {
						$services[ $known[ $service ] ] = true;
					} elseif ( ! isset( $this->classes[ self::ROOT_NAMESPACE . $class ] ) ) {
						$missing[ $class ] = true;
					}
				}

				$code = '';
				foreach ( token_get_all( (string) file_get_contents( $file->getPathname() ) ) as $token ) {
					if ( ! is_array( $token ) || ! in_array( $token[0], [ T_COMMENT, T_DOC_COMMENT ], true ) ) {
						$code .= is_array( $token ) ? $token[1] : $token;
					}
				}
				preg_match_all( '{ServiceFactory\s*::\s*(?:create|set_custom)_(\w+)\s*\(}', $code, $matches );
				foreach ( $matches[1] as $method ) {
					$service = strtolower( str_replace( '_', '', $method ) );
					if ( isset( $known[ $service ] ) ) {
						$services[ $known[ $service ] ] = true;
					} else {
						$missing[ "ServiceFactory::create_$method()" ] = true;
					}
				}

				preg_match_all( '{->\s*(\w+)\s*\(}', $code, $matches );
				foreach ( $matches[1] as $method ) {
					$calls[ self::API_PREFIX . strtolower( $method ) ] = true;
				}
			}
		}

		if ( $wpal ) {
			foreach ( $known as $key => $service ) {
				if ( isset( $calls[ $key ] ) ) {
					$services[ $service ] = true;
				}
			}
		}

		if ( $missing ) {
			throw new InvalidArgumentException( 'The code refers to WPAL classes this version doesn\'t have: ' . implode( ', ', array_keys( $missing ) ) . '.' );
		}

		return $this->sorted( array_keys( $services ) );
	}

	/**
	 * Works out which files to keep for the given services.
	 *
	 * @param string[] $services Service names as services() lists them, case-insensitive.
	 * @return array{services: string[], keep: string[], remove: string[]} Service names, and paths relative to src/.
	 * @throws InvalidArgumentException When a service doesn't exist.
	 */
	public function plan( array $services ): array {
		$names = [];
		$queue = [];
		foreach ( $services as $service ) {
			$name           = $this->service_name( $service );
			$names[ $name ] = true;

			$api     = strpos( $name, self::API_PREFIX ) === 0;
			$name    = $api ? substr( $name, strlen( self::API_PREFIX ) ) : $name;
			$classes = $api ? [ "Api\\$name", "Api\\WordPress\\WordPress$name", "Api\\Testing\\Fake$name" ] : [ "Service\\$name", "Service\\Wp$name" ];
			foreach ( $classes as $class ) {
				if ( isset( $this->classes[ self::ROOT_NAMESPACE . $class ] ) ) {
					$queue[] = self::ROOT_NAMESPACE . $class;
				}
			}
		}

		$kept = [];
		while ( $queue ) {
			$class = array_pop( $queue );
			if ( isset( $kept[ $class ] ) ) {
				continue;
			}
			$kept[ $class ] = true;

			// A kept Service interface needs its implementation: ServiceFactory creates it.
			if ( preg_match( '{^Merkushin\\\\Wpal\\\\Service\\\\(\w+)$}', $class, $match ) && isset( $this->classes[ self::ROOT_NAMESPACE . 'Service\\Wp' . $match[1] ] ) ) {
				$queue[] = self::ROOT_NAMESPACE . 'Service\\Wp' . $match[1];
			}

			foreach ( $this->references( $this->src . '/' . $this->classes[ $class ] ) as $reference ) {
				$queue[] = $reference;
			}
		}

		$api = false;
		foreach ( array_keys( $kept ) as $class ) {
			$api = $api || strpos( $class, self::ROOT_NAMESPACE . 'Api\\' ) === 0;
		}

		$keep   = [];
		$remove = [];
		foreach ( $this->classes as $class => $file ) {
			if ( isset( $kept[ $class ] ) || ( strpos( $file, '/' ) === false && ( $api || $file !== 'Wpal.php' ) ) ) {
				$keep[] = $file;
			} else {
				$remove[] = $file;
			}
		}
		sort( $keep );
		sort( $remove );

		return [
			'services' => $this->sorted( array_keys( $names ) ),
			'keep'     => $keep,
			'remove'   => $remove,
		];
	}

	/**
	 * Removes the files plan() doesn't keep, and directories left empty.
	 *
	 * @param string[] $services Service names, case-insensitive.
	 * @return array{services: string[], keep: string[], remove: string[]} What plan() returned.
	 * @throws InvalidArgumentException When a service doesn't exist.
	 */
	public function prune( array $services ): array {
		$plan = $this->plan( $services );
		foreach ( $plan['remove'] as $file ) {
			unlink( $this->src . '/' . $file );
		}

		$directories = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $this->src, FilesystemIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $directories as $directory ) {
			if ( $directory->isDir() && ! ( new FilesystemIterator( $directory->getPathname() ) )->valid() ) {
				rmdir( $directory->getPathname() );
			}
		}

		$this->classes = $this->find_classes();

		return $plan;
	}

	/**
	 * Total size of the given files, in bytes.
	 *
	 * @param string[] $files Paths relative to src/.
	 */
	public function size( array $files ): int {
		$size = 0;
		foreach ( $files as $file ) {
			$size += is_file( $this->src . '/' . $file ) ? (int) filesize( $this->src . '/' . $file ) : 0;
		}

		return $size;
	}

	private function service_name( string $service ): string {
		foreach ( $this->services() as $name ) {
			if ( strcasecmp( $name, $service ) === 0 ) {
				return $name;
			}
		}

		throw new InvalidArgumentException( "There is no service named $service. Run with --list to see them." );
	}

	/**
	 * Sorts service names as services() does: Services, then Api services.
	 *
	 * @param string[] $services
	 * @return string[]
	 */
	private function sorted( array $services ): array {
		return array_values( array_intersect( $this->services(), $services ) );
	}

	/**
	 * @return array<string, string> Class name => path relative to src/, by PSR-4.
	 */
	private function find_classes(): array {
		$classes = [];
		$files   = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->src, FilesystemIterator::SKIP_DOTS ) );
		foreach ( $files as $file ) {
			if ( $file->isFile() && $file->getExtension() === 'php' ) {
				$relative = substr( $file->getPathname(), strlen( $this->src ) + 1 );
				$relative = str_replace( '\\', '/', $relative );

				$classes[ self::ROOT_NAMESPACE . str_replace( '/', '\\', substr( $relative, 0, -4 ) ) ] = $relative;
			}
		}
		ksort( $classes );

		return $classes;
	}

	/**
	 * WPAL classes a file refers to.
	 *
	 * @return string[]
	 */
	private function references( string $file ): array {
		return array_values(
			array_filter(
				$this->names( $file ),
				function ( string $name ): bool {
					return isset( $this->classes[ $name ] );
				}
			)
		);
	}

	/**
	 * Class names a file refers to in code: imports, type declarations, `new`, `extends`, static calls…
	 * Docblocks and strings don't count, since they never load a class. A name that only looks like a class name
	 * may be included; that keeps a file too many, never too few.
	 *
	 * Handles both PHP 7 (names split into T_STRING and T_NS_SEPARATOR) and PHP 8 (T_NAME_*) tokens.
	 *
	 * @return string[] Fully qualified, without the leading backslash.
	 */
	private function names( string $file ): array {
		$tokens = token_get_all( (string) file_get_contents( $file ) );
		$tokens = array_values(
			array_filter(
				$tokens,
				static function ( $token ): bool {
					return ! is_array( $token ) || ! in_array( $token[0], [ T_WHITESPACE, T_COMMENT, T_DOC_COMMENT ], true );
				}
			)
		);

		$namespace       = '';
		$namespace_depth = 0;
		$aliases         = [];
		$names           = [];
		$depth           = 0;
		$count           = count( $tokens );
		$skip_after      = [ T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_CONST, T_GOTO ];
		if ( defined( 'T_NULLSAFE_OBJECT_OPERATOR' ) ) {
			$skip_after[] = constant( 'T_NULLSAFE_OBJECT_OPERATOR' );
		}

		for ( $i = 0; $i < $count; $i++ ) {
			$token = $tokens[ $i ];
			$type  = is_array( $token ) ? $token[0] : $token;

			if ( $type === '{' || $type === T_CURLY_OPEN || $type === T_DOLLAR_OPEN_CURLY_BRACES ) {
				++$depth;
			} elseif ( $type === '}' ) {
				--$depth;
			} elseif ( $type === T_NAMESPACE && ! $this->is_relative( $tokens[ $i + 1 ] ?? null ) ) {
				// A namespace declaration, `namespace X;` or `namespace X { … }`.
				$namespace = '';
				if ( $this->is_name( $tokens[ $i + 1 ] ?? null ) ) {
					++$i;
					$namespace = $this->read_name( $tokens, $i );
				}
				$aliases         = [];
				$namespace_depth = ( $tokens[ $i + 1 ] ?? null ) === '{' ? 1 : 0;
			} elseif ( $type === T_USE && $depth === $namespace_depth ) {
				$i = $this->read_imports( $tokens, $i, $aliases, $names );
			} elseif ( $this->is_name( $token ) ) {
				$previous = $tokens[ $i - 1 ] ?? null;
				$name     = $this->read_name( $tokens, $i );
				if ( ! is_array( $previous ) || ! in_array( $previous[0], $skip_after, true ) ) {
					$names[] = $this->resolve( $name, $namespace, $aliases );
				}
			}
		}

		return array_values( array_unique( $names ) );
	}

	/**
	 * Reads a top-level `use` statement, including group imports, starting at its T_USE token.
	 *
	 * @param array<int, mixed>     $tokens
	 * @param array<string, string> $aliases Updated with the imported class aliases.
	 * @param string[]              $names   Updated with the imported class names.
	 * @return int Index of the statement's last token.
	 */
	private function read_imports( array $tokens, int $i, array &$aliases, array &$names ): int {
		$next = $tokens[ $i + 1 ] ?? null;
		if ( is_array( $next ) && in_array( $next[0], [ T_FUNCTION, T_CONST ], true ) ) {
			// Functions and constants aren't classes.
			while ( isset( $tokens[ $i ] ) && $tokens[ $i ] !== ';' ) {
				++$i;
			}

			return $i;
		}

		$prefix = '';
		$name   = '';
		for ( ++$i; isset( $tokens[ $i ] ) && $tokens[ $i ] !== ';'; $i++ ) {
			$token = $tokens[ $i ];
			if ( $this->is_name( $token ) ) {
				$name = $this->read_name( $tokens, $i );
				$name = ltrim( $prefix . $name, '\\' );
				$as   = $tokens[ $i + 1 ] ?? null;
				if ( is_array( $as ) && $as[0] === T_AS ) {
					$i     += 2;
					$alias  = $this->read_name( $tokens, $i );
				} else {
					$alias = substr( (string) strrchr( '\\' . $name, '\\' ), 1 );
				}
				if ( substr( $name, -1 ) !== '\\' ) {
					$aliases[ strtolower( $alias ) ] = $name;
					$names[]                         = $name;
				} else {
					$name = rtrim( $name, '\\' );
				}
			} elseif ( $token === '{' ) {
				$prefix = $name . '\\';
			} elseif ( $token === '}' ) {
				$prefix = '';
			}
		}

		return $i;
	}

	/**
	 * @param mixed $token
	 */
	private function is_name( $token ): bool {
		if ( ! is_array( $token ) ) {
			return false;
		}
		if ( $token[0] === T_STRING || $token[0] === T_NS_SEPARATOR ) {
			return true;
		}

		foreach ( [ 'T_NAME_QUALIFIED', 'T_NAME_FULLY_QUALIFIED', 'T_NAME_RELATIVE' ] as $constant ) {
			if ( defined( $constant ) && $token[0] === constant( $constant ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether a token after T_NAMESPACE makes it a relative name (`namespace\Foo`) rather than a declaration.
	 *
	 * @param mixed $token
	 */
	private function is_relative( $token ): bool {
		return is_array( $token ) && $token[0] === T_NS_SEPARATOR;
	}

	/**
	 * Reads a name made of one or more tokens, leaving $i on its last token.
	 *
	 * @param array<int, mixed> $tokens
	 */
	private function read_name( array $tokens, int &$i ): string {
		$name = '';
		while ( $this->is_name( $tokens[ $i ] ?? null ) ) {
			$name .= $tokens[ $i ][1];
			++$i;
		}
		--$i;

		return $name;
	}

	/**
	 * @param array<string, string> $aliases
	 */
	private function resolve( string $name, string $namespace, array $aliases ): string {
		if ( $name[0] === '\\' ) {
			return substr( $name, 1 );
		}
		if ( stripos( $name, 'namespace\\' ) === 0 ) {
			return $namespace . substr( $name, 9 );
		}

		$parts = explode( '\\', $name, 2 );
		$first = strtolower( $parts[0] );
		if ( isset( $aliases[ $first ] ) ) {
			return $aliases[ $first ] . ( isset( $parts[1] ) ? '\\' . $parts[1] : '' );
		}

		return ( $namespace === '' ? '' : $namespace . '\\' ) . $name;
	}
}

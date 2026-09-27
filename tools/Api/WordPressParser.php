<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

/**
 * Reads a WordPress checkout and collects its global functions.
 *
 * Only `wp-includes` and `wp-admin/includes` are scanned; bundled third-party libraries are skipped.
 */
final class WordPressParser {
	private const SCANNED_DIRS = [ 'wp-includes', 'wp-admin/includes' ];

	/** Bundled libraries whose functions are not WordPress API. */
	private const SKIPPED_DIRS = [
		'wp-includes/ID3',
		'wp-includes/IXR',
		'wp-includes/PHPMailer',
		'wp-includes/Requests',
		'wp-includes/SimplePie',
		'wp-includes/Text',
		'wp-includes/js',
		'wp-includes/php-ai-client',
		'wp-includes/php-compat',
		'wp-includes/sodium_compat',
	];

	/** Files whose functions are stand-ins, not the real implementation. */
	private const SKIPPED_FILES = [
		'wp-admin/includes/noop.php',
	];

	/** Files that only hold deprecated functions. */
	private const DEPRECATED_FILES = [
		'wp-includes/deprecated.php',
		'wp-includes/ms-deprecated.php',
		'wp-includes/pluggable-deprecated.php',
		'wp-admin/includes/deprecated.php',
		'wp-admin/includes/ms-deprecated.php',
	];

	private Parser $parser;

	private NodeFinder $finder;

	public function __construct() {
		$this->parser = ( new ParserFactory() )->createForNewestSupportedVersion();
		$this->finder = new NodeFinder();
	}

	public function parse( string $root ): Snapshot {
		$root = rtrim( $root, '/' );

		/** @var array<string, array{node: Stmt\Function_, file: string, pluggable: bool}> $found */
		$found = [];
		/** @var array<string, Expr> $defines */
		$defines = [];

		foreach ( $this->files( $root ) as $relative ) {
			$code = (string) file_get_contents( $root . '/' . $relative );
			try {
				$ast = $this->parser->parse( $code ) ?? [];
			} catch ( \PhpParser\Error $e ) {
				throw new RuntimeException( "Cannot parse {$relative}: {$e->getMessage()}", 0, $e );
			}
			$this->collect( $ast, $relative, $found, $defines );
		}

		$constants = $this->resolveConstants( $defines );
		$builder   = new SignatureBuilder( $constants );

		$functions = [];
		foreach ( $found as $key => $item ) {
			$functions[ $key ] = $this->signature( $item['node'], $item['file'], $item['pluggable'], $builder );
		}

		return new Snapshot( $this->version( $root ), $functions, $constants );
	}

	/**
	 * @return iterable<string> Paths relative to the WordPress root, sorted.
	 */
	private function files( string $root ): iterable {
		$ordered = [];
		foreach ( self::SCANNED_DIRS as $dir ) {
			$files = [];
			if ( ! is_dir( $root . '/' . $dir ) ) {
				throw new RuntimeException( "{$root} does not look like a WordPress checkout: {$dir} is missing." );
			}
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, RecursiveDirectoryIterator::SKIP_DOTS ) );
			foreach ( $iterator as $file ) {
				$relative = substr( $file->getPathname(), strlen( $root ) + 1 );
				if ( substr( $relative, -4 ) !== '.php' || $this->skipped( $relative ) ) {
					continue;
				}
				$files[] = $relative;
			}
			// Sorted within each directory; wp-includes comes first so its declarations win.
			sort( $files, SORT_STRING );
			$ordered = array_merge( $ordered, $files );
		}

		return $ordered;
	}

	private function skipped( string $relative ): bool {
		if ( in_array( $relative, self::SKIPPED_FILES, true ) ) {
			return true;
		}
		foreach ( self::SKIPPED_DIRS as $dir ) {
			if ( str_starts_with( $relative, $dir . '/' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param Node[]                                                                  $ast
	 * @param array<string, array{node: Stmt\Function_, file: string, pluggable: bool}> $found
	 * @param array<string, Expr>                                                      $defines
	 */
	private function collect( array $ast, string $file, array &$found, array &$defines ): void {
		$visitor = new class( $file, $found, $defines ) extends NodeVisitorAbstract {
			private int $guards = 0;

			/**
			 * @param array<string, array{node: Stmt\Function_, file: string, pluggable: bool}> $found
			 * @param array<string, Expr>                                                      $defines
			 */
			public function __construct( private string $file, private array &$found, private array &$defines ) {
			}

			public function enterNode( Node $node ): ?int {
				if ( $node instanceof Stmt\ClassLike ) {
					return NodeVisitor::DONT_TRAVERSE_CHILDREN;
				}
				if ( $node instanceof Stmt\If_ && $this->isFunctionExistsGuard( $node->cond ) ) {
					$this->guards++;
				}
				if ( $node instanceof Stmt\Function_ ) {
					// Constants are often defined inside functions, e.g. wp_initial_constants().
					foreach ( ( new NodeFinder() )->findInstanceOf( $node->stmts, Expr\FuncCall::class ) as $call ) {
						$this->define( $call );
					}
					$key = $node->name->toLowerString();
					// The first declaration wins; later ones are fallbacks for older environments.
					$this->found[ $key ] ??= [ 'node' => $node, 'file' => $this->file, 'pluggable' => $this->guards > 0 ];

					return NodeVisitor::DONT_TRAVERSE_CHILDREN;
				}
				if ( $node instanceof Expr\FuncCall ) {
					$this->define( $node );
				}

				return null;
			}

			private function define( Expr\FuncCall $call ): void {
				if ( ! $call->name instanceof Node\Name || $call->name->toLowerString() !== 'define' ) {
					return;
				}
				$args = $call->getArgs();
				if ( count( $args ) >= 2 && $args[0]->value instanceof Node\Scalar\String_ ) {
					$this->defines[ $args[0]->value->value ] ??= $args[1]->value;
				}
			}

			public function leaveNode( Node $node ): ?int {
				if ( $node instanceof Stmt\If_ && $this->isFunctionExistsGuard( $node->cond ) ) {
					$this->guards--;
				}

				return null;
			}

			private function isFunctionExistsGuard( Expr $cond ): bool {
				$finder = new NodeFinder();

				return $finder->findFirst(
					$cond,
					static fn ( Node $n ): bool => $n instanceof Expr\FuncCall && $n->name instanceof Node\Name && $n->name->toLowerString() === 'function_exists'
				) !== null;
			}
		};

		$traverser = new NodeTraverser();
		$traverser->addVisitor( $visitor );
		$traverser->traverse( $ast );
	}

	/**
	 * Resolves `define()`d constants, including ones built from other constants.
	 *
	 * @param array<string, Expr> $defines
	 * @return array<string, mixed>
	 */
	private function resolveConstants( array $defines ): array {
		$constants = [];
		do {
			$progress = false;
			$builder  = new SignatureBuilder( $constants );
			foreach ( $defines as $name => $expr ) {
				[ $ok, $value ] = $builder->evaluate( $expr );
				if ( $ok && is_scalar( $value ) ) {
					$constants[ $name ] = $value;
					unset( $defines[ $name ] );
					$progress = true;
				}
			}
		} while ( $progress && $defines );

		ksort( $constants, SORT_STRING );

		return $constants;
	}

	private function signature( Stmt\Function_ $node, string $file, bool $pluggable, SignatureBuilder $builder ): FunctionSignature {
		$doc  = new DocBlock( $node->getDocComment()?->getText() );
		$name = $node->name->toString();

		$deprecated = $doc->first( 'deprecated' );
		if ( $deprecated === null && ( in_array( $file, self::DEPRECATED_FILES, true ) || $this->callsDeprecatedFunction( $node ) ) ) {
			$deprecated = '';
		}

		return new FunctionSignature(
			$name,
			$builder->parameters( $node->params ),
			$builder->type( $node->returnType ),
			$doc->text(),
			$doc->first( 'since' ),
			$deprecated,
			str_starts_with( $name, '_' ) || $doc->first( 'access' ) === 'private',
			$file,
			$pluggable,
		);
	}

	private function callsDeprecatedFunction( Stmt\Function_ $node ): bool {
		return $this->finder->findFirst(
			$node->stmts,
			static fn ( Node $n ): bool => $n instanceof Expr\FuncCall
				&& $n->name instanceof Node\Name
				&& $n->name->toLowerString() === '_deprecated_function'
				&& isset( $n->getArgs()[0] )
				&& (
					$n->getArgs()[0]->value instanceof Node\Scalar\MagicConst\Function_
					|| ( $n->getArgs()[0]->value instanceof Node\Scalar\String_ && strtolower( $n->getArgs()[0]->value->value ) === $node->name->toLowerString() )
				)
		) !== null;
	}

	private function version( string $root ): string {
		$file = $root . '/wp-includes/version.php';
		if ( is_file( $file ) && preg_match( '/\$wp_version\s*=\s*[\'"]([^\'"]+)[\'"]/', (string) file_get_contents( $file ), $m ) ) {
			return $m[1];
		}

		throw new RuntimeException( "Cannot find the WordPress version in {$file}." );
	}
}

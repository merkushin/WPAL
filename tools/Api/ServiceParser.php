<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Api;

use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use RuntimeException;

/**
 * Reads the `Service` interfaces: each method stands in for the WordPress function of the same name.
 */
final class ServiceParser {
	/**
	 * @param array<string, mixed> $constants WordPress constants, so `'OBJECT'` and `OBJECT` compare as equal.
	 * @return ServiceMethod[] Sorted by service, then method.
	 */
	public function parse( string $dir, array $constants = [] ): array {
		$parser  = ( new ParserFactory() )->createForNewestSupportedVersion();
		$finder  = new NodeFinder();
		$builder = new SignatureBuilder( $constants );
		$methods = [];

		$files = glob( rtrim( $dir, '/' ) . '/*.php' );
		if ( $files === false || $files === [] ) {
			throw new RuntimeException( "No service files found in {$dir}." );
		}

		foreach ( $files as $file ) {
			$ast = $parser->parse( (string) file_get_contents( $file ) ) ?? [];
			foreach ( $finder->findInstanceOf( $ast, Stmt\Interface_::class ) as $interface ) {
				/** @var Stmt\Interface_ $interface */
				$service = (string) $interface->name;
				foreach ( $interface->getMethods() as $method ) {
					$doc       = new DocBlock( $method->getDocComment()?->getText() );
					$methods[] = new ServiceMethod(
						$service,
						new FunctionSignature(
							$method->name->toString(),
							$builder->parameters( $method->params ),
							$builder->type( $method->returnType ),
							$doc->text(),
							$doc->first( 'since' ),
							$doc->first( 'deprecated' ),
						),
						basename( $file ),
					);
				}
			}
		}

		usort( $methods, static fn ( ServiceMethod $a, ServiceMethod $b ): int => [ $a->service, $a->signature->name ] <=> [ $b->service, $b->signature->name ] );

		return $methods;
	}
}

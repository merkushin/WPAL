<?php declare( strict_types=1 );
/**
 * Splits php-stubs/wordpress-stubs into one file per class/function.
 *
 * PHPStan re-parses a scanned file on every symbol cache miss and keeps its source per reflection,
 * so one 6 MB stubs file costs ~6 MB per referenced WordPress symbol. Small files make that negligible.
 *
 * Usage: php bin/split-stubs.php <stubs-file> <output-dir>
 */

if ( PHP_VERSION_ID < 80000 ) {
	// PHP 7 tokenizes the stubs' #[...] attributes as comments.
	fwrite( STDERR, "bin/split-stubs.php needs PHP 8.0 or later.\n" );
	exit( 1 );
}

[ , $source, $target ] = $argv + [ null, null, null ];
if ( ! $source || ! $target ) {
	fwrite( STDERR, "Usage: php bin/split-stubs.php <stubs-file> <output-dir>\n" );
	exit( 1 );
}

$stamp = $target . '/.source-hash';
$hash  = hash_file( 'sha256', $source ) . '-' . hash_file( 'sha256', __FILE__ );
if ( is_file( $stamp ) && trim( (string) file_get_contents( $stamp ) ) === $hash ) {
	exit( 0 );
}

if ( is_dir( $target ) ) {
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $target, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST ) as $file ) {
		$file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
	}
} else {
	mkdir( $target, 0777, true );
}

$tokens    = token_get_all( (string) file_get_contents( $source ) );
$count     = count( $tokens );
$namespace = null;
$depth     = 0;
$rest      = [];
$written   = 0;

$text = static function ( $token ): string {
	return is_array( $token ) ? $token[1] : $token;
};

$write = static function ( string $namespace, string $code, string $name ) use ( $target, &$written ): void {
	$dir = $target . '/' . ( $namespace === '' ? 'global' : str_replace( '\\', '/', $namespace ) );
	if ( ! is_dir( $dir ) ) {
		mkdir( $dir, 0777, true );
	}
	$file = $dir . '/' . $name . '-' . substr( md5( $code ), 0, 8 ) . '.php';
	file_put_contents( $file, "<?php\n\nnamespace {$namespace} {\n{$code}\n}\n" );
	$written++;
};

for ( $i = 0; $i < $count; $i++ ) {
	$token = $tokens[ $i ];

	if ( $depth === 0 && is_array( $token ) && $token[0] === T_NAMESPACE ) {
		$namespace = '';
		for ( $i++; $tokens[ $i ] !== '{'; $i++ ) {
			$namespace .= trim( $text( $tokens[ $i ] ) );
		}
		$depth = 1;
		$rest[ $namespace ] = $rest[ $namespace ] ?? '';
		continue;
	}

	if ( $depth === 1 && is_array( $token ) && in_array( $token[0], [ T_DOC_COMMENT, T_ATTRIBUTE, T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT, T_ABSTRACT, T_FINAL ], true ) ) {
		// Look ahead: is this the start of a class-like or function declaration?
		$j    = $i;
		$name = null;
		while ( $j < $count ) {
			$t = $tokens[ $j ];
			if ( is_array( $t ) && in_array( $t[0], [ T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT ], true ) ) {
				for ( $k = $j + 1; $k < $count; $k++ ) {
					if ( is_array( $tokens[ $k ] ) && $tokens[ $k ][0] === T_STRING ) {
						$name = $tokens[ $k ][1];
						break;
					}
				}
				break;
			}
			if ( is_array( $t ) && $t[0] === T_ATTRIBUTE ) {
				// Skip to the attribute's closing bracket.
				for ( $brackets = 1, $j++; $j < $count && $brackets > 0; $j++ ) {
					if ( $tokens[ $j ] === '[' ) {
						$brackets++;
					} elseif ( $tokens[ $j ] === ']' ) {
						$brackets--;
					}
				}
				continue;
			}
			if ( ! is_array( $t ) || ! in_array( $t[0], [ T_DOC_COMMENT, T_WHITESPACE, T_ABSTRACT, T_FINAL, T_COMMENT ], true ) ) {
				break;
			}
			$j++;
		}

		if ( $name !== null ) {
			$code  = '';
			$inner = 0;
			for ( ; $i < $count; $i++ ) {
				$t     = $tokens[ $i ];
				$code .= $text( $t );
				if ( $t === '{' || ( is_array( $t ) && in_array( $t[0], [ T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ], true ) ) ) {
					$inner++;
				} elseif ( $t === '}' ) {
					$inner--;
					if ( $inner === 0 ) {
						break;
					}
				}
			}
			$write( (string) $namespace, $code, $name );
			continue;
		}
	}

	if ( $token === '{' ) {
		$depth++;
	} elseif ( $token === '}' ) {
		$depth--;
		if ( $depth === 0 ) {
			$namespace = null;
			continue;
		}
	}

	if ( $depth >= 1 && $namespace !== null ) {
		$rest[ $namespace ] .= $text( $token );
	}
}

foreach ( $rest as $namespace => $code ) {
	if ( trim( $code ) !== '' ) {
		$write( (string) $namespace, $code, '_rest' );
	}
}

file_put_contents( $stamp, $hash . "\n" );
fwrite( STDERR, "Split into {$written} files in {$target}\n" );

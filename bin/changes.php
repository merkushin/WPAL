<?php declare( strict_types = 1 );
/**
 * The file tries to find changer in the last WP version.
 */

$tmp_dir = sys_get_temp_dir() . '/wpal-updater';
if ( file_exists( $tmp_dir ) ) {
    echo "The directory $tmp_dir already exists. Please remove it and try again.\n";
    exit( 1 );
}

if ( ! is_dir( $tmp_dir ) ) {
    mkdir( $tmp_dir );
}

$zip_file = $tmp_dir . '/wp.zip';
$zip_url  = 'https://wordpress.org/latest.zip';
echo "Downloading $zip_url to $zip_file\n";
file_put_contents( $zip_file, file_get_contents( $zip_url ) );

$zip = new ZipArchive();
if ( true !== $zip->open( $zip_file ) ) {
    echo "Unable to open $zip_file\n";
    exit( 1 );
}

$zip->extractTo( $tmp_dir );
$zip->close();
echo "Extracted $zip_file to $tmp_dir\n";

$defined_functions = get_functions_from_wordpress( $tmp_dir . '/wordpress/wp-includes' );

exec( "rm -rf {$tmp_dir}" );

echo "Diffing functions...\n\n";
/*
 * Iterate over project files and find methods that exist in the WP as functions.
 */
foreach ( get_project_files() as $file_path ) {
    $content = file_get_contents( $file_path );
    if ( ! preg_match_all( '/function\s+([a-z0-9_]+)\(([^\)]*)\);/i', $content, $matches ) ) {
        continue;
    }

    foreach ( $matches[1] as $key => $function_name ) {
        $args = normalize_args( $matches[2][ $key ] ?? '' );
        if ( ! isset( $defined_functions[ $function_name ] ) ) {
            continue;
        }

        if ( $args === $defined_functions[ $function_name ] ) {
            continue;
        }

        echo "{$function_name}:\n- {$function_name}({$args}) in $file_path\n+ {$function_name}({$defined_functions[ $function_name ]}) in WP\n\n";
    }
}

function get_project_files() {
	$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( __DIR__ . '/../src' ) );
	foreach ( $files as $file ) {
		if ( ! $file->isFile() ) {
			continue;
		}

		$file_path = $file->getPathname();
		if ( ! preg_match( '/\.php$/', $file_path ) ) {
			continue;
		}

		yield $file_path;
	}
}

/*
 * Load all files from wp-includes and collect declared functions.
 */
function get_functions_from_wordpress( string $dir ) : array {
	$defined_functions = [];
	$files             = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $dir ) );
	foreach ( $files as $file ) {
		if ( ! $file->isFile() ) {
			continue;
		}

		$file_path = $file->getPathname();
		if ( ! preg_match( '/\.php$/', $file_path ) ) {
			continue;
		}

		// File name doesn't match the class name.
		if ( preg_match( '/^class\-/', $file->getFilename() ) ) {
			continue;
		}

		$content = file_get_contents( $file_path );
		if ( ! preg_match_all( '/function\s+([a-z0-9_]+)\(([^\)]*)\)/i', $content, $matches ) ) {
			continue;
		}

		foreach ( $matches[1] as $key => $function_name ) {
			$args = normalize_args( $matches[2][ $key ] ?? '' );
			$defined_functions[ $function_name ] = $args;
		}
	}
	return $defined_functions;
}

function normalize_args( string $args ): string {
    $separated = explode( ',', $args );
    $normalized = [];
    foreach ( $separated as $arg ) {
        $arg = trim( $arg );
        if ( ! $arg ) {
            continue;
        }

        $with_type = explode( ' ', $arg );
        if ( count( $with_type ) === 1 ) {
            $normalized[] = trim( $arg );
        } else {
            $i = 0;
            while ( ! is_var_name( $with_type[ $i ] ) && isset( $with_type[ $i + 1 ] ) ) {
                $i++;
            }

            if ( is_var_name( $with_type[ $i ] ) ) {
                $normalized[] = trim( $with_type[ $i ] );
            }
        }
    }

    return implode( ', ', $normalized );
}

function is_var_name( ?string $name ): bool {
    if ( ! $name ) {
        return false;
    }
    return preg_match( '/^\$[a-z_]+$/i', $name ) === 1;
}

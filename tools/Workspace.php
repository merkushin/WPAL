<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools;

use Merkushin\Wpal\Tools\Api\ServiceMethod;
use Merkushin\Wpal\Tools\Api\ServiceParser;
use Merkushin\Wpal\Tools\Api\Snapshot;
use Merkushin\Wpal\Tools\Api\WordPressParser;
use Merkushin\Wpal\Tools\Coverage\Map;
use Merkushin\Wpal\Tools\WordPress\Downloader;

/**
 * Where the project keeps things, and how a snapshot spec turns into a {@see Snapshot}.
 */
final class Workspace {
	public function __construct(
		public readonly string $root,
		private ?Downloader $downloader = null,
	) {
	}

	/** The committed snapshot of the WordPress version `Service` targets. */
	public function currentSnapshotFile(): string {
		return $this->root . '/api/wordpress.json';
	}

	public function serviceDir(): string {
		return $this->root . '/src/Service';
	}

	public function map(): Map {
		return Map::load( $this->root . '/wpal.map.php' );
	}

	public function downloader(): Downloader {
		return $this->downloader ??= new Downloader( $this->root . '/build/wordpress' );
	}

	/**
	 * @param string|null $spec `current` (default), `latest`, a version such as `7.1.2`, or a path to a snapshot JSON file.
	 */
	public function snapshot( ?string $spec ): Snapshot {
		$spec ??= 'current';

		if ( $spec === 'current' ) {
			return Snapshot::load( $this->currentSnapshotFile() );
		}
		if ( str_ends_with( $spec, '.json' ) ) {
			return Snapshot::load( $spec );
		}

		$version = $spec === 'latest' ? $this->downloader()->latestVersion() : $spec;
		$cached  = $this->root . '/build/snapshots/wordpress-' . $version . '.json';
		if ( is_file( $cached ) ) {
			return Snapshot::load( $cached );
		}

		$snapshot = ( new WordPressParser() )->parse( $this->downloader()->download( $version ) );
		$snapshot->save( $cached );

		return $snapshot;
	}

	/**
	 * @return ServiceMethod[]
	 */
	public function serviceMethods( Snapshot $wordpress ): array {
		return ( new ServiceParser() )->parse( $this->serviceDir(), $wordpress->constants );
	}
}

<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Testing;

use InvalidArgumentException;
use Merkushin\Wpal\Api\Abilities;
use Merkushin\Wpal\Api\Abilities\Ability;
use Merkushin\Wpal\Api\Abilities\AbilityInfo;
use Merkushin\Wpal\Api\Exception\AbilityNotFound;
use Merkushin\Wpal\Api\Exception\WordPressError;

/**
 * In-memory abilities for tests: registered abilities really run, with permission checks.
 */
final class FakeAbilities implements Abilities {
	/** @var array<string, Ability> */
	public private(set) array $abilities = [];

	/** @var array<string, array{label: string, description: string, meta: array<string, mixed>}> */
	public private(set) array $categories = [];

	/**
	 * @param string[] $capabilities What the pretend current user can do, for requireCapability().
	 */
	public function __construct( private array $capabilities = [] ) {
	}

	/**
	 * Changes what the pretend current user can do.
	 *
	 * @param string[] $capabilities
	 */
	public function actAs( array $capabilities ): void {
		$this->capabilities = $capabilities;
	}

	public function define( string $name ): Ability {
		return new Ability( $name, $this );
	}

	public function register( Ability $ability ): void {
		$this->abilities[ $ability->name ] = $ability;
	}

	public function registerCategory( string $slug, string $label, string $description, array $meta = [] ): void {
		if ( preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug ) !== 1 ) {
			throw new InvalidArgumentException( "Ability category slug \"{$slug}\" must be lowercase letters, digits and dashes." );
		}
		$this->categories[ $slug ] = [ 'label' => $label, 'description' => $description, 'meta' => $meta ];
	}

	public function has( string $name ): bool {
		return isset( $this->abilities[ $name ] );
	}

	public function find( string $name ): ?AbilityInfo {
		return isset( $this->abilities[ $name ] ) ? AbilityInfo::fromDefinition( $this->abilities[ $name ] ) : null;
	}

	public function get( string $name ): AbilityInfo {
		return $this->find( $name ) ?? throw new AbilityNotFound( $name );
	}

	public function all( ?string $category = null ): array {
		$abilities = array_map( AbilityInfo::fromDefinition( ... ), array_values( $this->abilities ) );

		return $category === null ? $abilities : array_values( array_filter( $abilities, static fn ( AbilityInfo $a ): bool => $a->category === $category ) );
	}

	public function execute( string $name, mixed $input = null ): mixed {
		$ability = $this->abilities[ $name ] ?? throw new AbilityNotFound( $name );

		$allowed = $ability->permission !== null
			? ( $ability->permission )( $input )
			: in_array( $ability->capability, $this->capabilities, true );
		if ( $allowed !== true ) {
			throw new WordPressError( "You are not allowed to run the {$name} ability.", 'ability_invalid_permissions' );
		}
		if ( $ability->inputSchema !== [] && $input === null ) {
			throw new WordPressError( "The {$name} ability needs input.", 'ability_missing_input' );
		}

		return ( $ability->execute ?? static fn (): mixed => null )( $input );
	}

	public function unregister( string $name ): void {
		unset( $this->abilities[ $name ] );
	}
}

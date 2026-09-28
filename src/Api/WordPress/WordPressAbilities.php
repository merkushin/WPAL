<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\WordPress;

use Closure;
use InvalidArgumentException;
use Merkushin\Wpal\Api\Abilities;
use Merkushin\Wpal\Api\Abilities\Ability;
use Merkushin\Wpal\Api\Abilities\AbilityInfo;
use Merkushin\Wpal\Api\Exception\AbilityNotFound;
use Merkushin\Wpal\Api\Exception\RegistrationClosed;
use Merkushin\Wpal\Api\Exception\WordPressError;
use Merkushin\Wpal\Service\Abilities as AbilitiesService;
use Merkushin\Wpal\Service\Capabilities as CapabilitiesService;
use Merkushin\Wpal\Service\Hooks as HooksService;

final class WordPressAbilities implements Abilities {
	private const ABILITIES_HOOK = 'wp_abilities_api_init';

	private const CATEGORIES_HOOK = 'wp_abilities_api_categories_init';

	public function __construct(
		private readonly AbilitiesService $abilities,
		private readonly HooksService $hooks,
		private readonly CapabilitiesService $capabilities,
	) {
	}

	public function define( string $name ): Ability {
		return new Ability( $name, $this );
	}

	public function register( Ability $ability ): void {
		$this->whenOpen(
			self::ABILITIES_HOOK,
			"ability {$ability->name}",
			function () use ( $ability ): void {
				$this->abilities->wp_register_ability( $ability->name, $this->args( $ability ) );
			}
		);
	}

	public function registerCategory( string $slug, string $label, string $description, array $meta = [] ): void {
		if ( preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug ) !== 1 ) {
			throw new InvalidArgumentException( "Ability category slug \"{$slug}\" must be lowercase letters, digits and dashes." );
		}

		$this->whenOpen(
			self::CATEGORIES_HOOK,
			"ability category {$slug}",
			function () use ( $slug, $label, $description, $meta ): void {
				$this->abilities->wp_register_ability_category( $slug, [ 'label' => $label, 'description' => $description, 'meta' => $meta ] );
			}
		);
	}

	public function has( string $name ): bool {
		return $this->abilities->wp_has_ability( $name );
	}

	public function find( string $name ): ?AbilityInfo {
		$ability = $this->abilities->wp_get_ability( $name );

		return $ability !== null ? AbilityInfo::fromWordPress( $ability ) : null;
	}

	public function get( string $name ): AbilityInfo {
		return $this->find( $name ) ?? throw new AbilityNotFound( $name );
	}

	public function all( ?string $category = null ): array {
		$abilities = array_map( AbilityInfo::fromWordPress( ... ), array_values( array_filter( $this->abilities->wp_get_abilities(), is_object( ... ) ) ) );

		return $category === null ? $abilities : array_values( array_filter( $abilities, static fn ( AbilityInfo $a ): bool => $a->category === $category ) );
	}

	public function execute( string $name, mixed $input = null ): mixed {
		$ability = $this->abilities->wp_get_ability( $name ) ?? throw new AbilityNotFound( $name );
		$result  = $ability->execute( $input );

		if ( WordPressError::isWpError( $result ) ) {
			throw WordPressError::fromWpError( $result );
		}

		return $result;
	}

	public function unregister( string $name ): void {
		$this->abilities->wp_unregister_ability( $name );
	}

	/**
	 * Runs `$register` now if `$hook` is firing, later if it hasn't fired yet.
	 *
	 * @param Closure(): void $register
	 * @throws RegistrationClosed When `$hook` already ran.
	 */
	private function whenOpen( string $hook, string $what, Closure $register ): void {
		if ( $this->hooks->doing_action( $hook ) ) {
			$register();

			return;
		}
		if ( $this->hooks->did_action( $hook ) > 0 ) {
			throw new RegistrationClosed( $what, $hook );
		}

		$this->hooks->add_action( $hook, $register, 10, 0 );
	}

	/**
	 * wp_register_ability() arguments for a definition.
	 *
	 * @return array<string, mixed>
	 */
	private function args( Ability $ability ): array {
		$capability = $ability->capability;
		$args       = [
			'label'               => $ability->label,
			'description'         => $ability->description,
			'category'            => $ability->category,
			'execute_callback'    => $ability->execute,
			'permission_callback' => $ability->permission
				?? fn (): bool => (bool) $this->capabilities->current_user_can( (string) $capability ),
			'meta'                => [
				'annotations'  => array_filter(
					[ 'readonly' => $ability->readonly, 'destructive' => $ability->destructive, 'idempotent' => $ability->idempotent ],
					static fn ( ?bool $value ): bool => $value !== null
				),
				'public'       => $ability->public,
				'show_in_rest' => $ability->showInRest,
			] + $ability->meta,
		];
		if ( $ability->inputSchema !== [] ) {
			$args['input_schema'] = $ability->inputSchema;
		}
		if ( $ability->outputSchema !== [] ) {
			$args['output_schema'] = $ability->outputSchema;
		}

		return $args;
	}
}

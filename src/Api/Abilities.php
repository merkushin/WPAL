<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

use Merkushin\Wpal\Api\Abilities\Ability;
use Merkushin\Wpal\Api\Abilities\AbilityInfo;
use Merkushin\Wpal\Api\Exception\AbilityNotFound;
use Merkushin\Wpal\Api\Exception\WordPressError;

/**
 * The Abilities API: what the site can do, described so AI agents, the REST API and other plugins can discover and run
 * it.
 *
 * Registration timing is handled for you: define and register abilities whenever your plugin loads.
 */
interface Abilities {
	/**
	 * Starts describing an ability; finish with register().
	 *
	 * @param string $name `namespace/ability-name`, e.g. `my-plugin/summarize-post`.
	 */
	#[\NoDiscard]
	public function define( string $name ): Ability;

	/**
	 * Registers a described ability, now or when WordPress starts registering abilities.
	 */
	public function register( Ability $ability ): void;

	/**
	 * Registers a category abilities can belong to, now or when WordPress starts registering categories.
	 *
	 * @param string               $slug Lowercase letters, digits and dashes, e.g. `content`.
	 * @param array<string, mixed> $meta
	 */
	public function registerCategory( string $slug, string $label, string $description, array $meta = [] ): void;

	public function has( string $name ): bool;

	public function find( string $name ): ?AbilityInfo;

	/**
	 * @throws AbilityNotFound
	 */
	public function get( string $name ): AbilityInfo;

	/**
	 * @return list<AbilityInfo>
	 */
	public function all( ?string $category = null ): array;

	/**
	 * Runs an ability as the current user: checks permission, validates the input, executes.
	 *
	 * @throws AbilityNotFound
	 * @throws WordPressError When permission is denied, the input is invalid, or the ability fails.
	 */
	public function execute( string $name, mixed $input = null ): mixed;

	public function unregister( string $name ): void;
}

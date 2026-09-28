<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api;

/**
 * Site options (`get_option()` and friends).
 *
 * WordPress returns `false` for a missing option; here a missing option returns your default instead.
 * WordPress stores scalars as strings, so the typed getters convert: `'1'` is `true` for bool(), `'42'` is `42` for int().
 */
interface Options {
	/**
	 * The stored value, or `$default` when the option doesn't exist.
	 */
	public function get( string $name, mixed $default = null ): mixed;

	public function has( string $name ): bool;

	/**
	 * `$default` when missing or not a scalar.
	 */
	public function string( string $name, string $default = '' ): string;

	/**
	 * `$default` when missing or not numeric.
	 */
	public function int( string $name, int $default = 0 ): int;

	/**
	 * Understands `true`/`false`, `1`/`0`, `'yes'`/`'no'`, `'on'`/`'off'`; `$default` for anything else.
	 */
	public function bool( string $name, bool $default = false ): bool;

	/**
	 * @param array<mixed> $default
	 * @return array<mixed> `$default` when missing or not an array.
	 */
	public function array( string $name, array $default = [] ): array;

	/**
	 * Creates or updates the option.
	 *
	 * @param bool|null $autoload Load it on every request; null leaves the choice to WordPress.
	 */
	public function set( string $name, mixed $value, ?bool $autoload = null ): void;

	/**
	 * Creates the option unless it exists.
	 *
	 * @return bool Whether it was created.
	 */
	public function add( string $name, mixed $value, ?bool $autoload = null ): bool;

	/**
	 * @return bool Whether an option was deleted.
	 */
	public function delete( string $name ): bool;
}

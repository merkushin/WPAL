<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Api\Abilities;

use Closure;
use InvalidArgumentException;
use Merkushin\Wpal\Api\Abilities;

/**
 * An ability to register: something the site can do that agents, the REST API and other plugins can discover and run.
 *
 *     $wp->abilities()->define( 'my-plugin/summarize-post' )
 *         ->label( 'Summarize post' )
 *         ->description( 'Returns a one-paragraph summary of a post.' )
 *         ->category( 'content' )
 *         ->input( [ 'type' => 'object', 'properties' => [ 'id' => [ 'type' => 'integer' ] ], 'required' => [ 'id' ] ] )
 *         ->output( [ 'type' => 'string' ] )
 *         ->readonly()
 *         ->requireCapability( 'read_post' )
 *         ->execute( fn ( array $input ): string => summarize( $input['id'] ) )
 *         ->register();
 */
final class Ability {
	public private(set) string $label = '';

	public private(set) string $description = '';

	public private(set) string $category = '';

	/** @var array<string, mixed> JSON Schema of the input; empty when the ability takes none. */
	public private(set) array $inputSchema = [];

	/** @var array<string, mixed> JSON Schema of the result. */
	public private(set) array $outputSchema = [];

	/** @var (Closure(mixed): mixed)|null */
	public private(set) ?Closure $execute = null;

	/** @var (Closure(mixed): bool)|null */
	public private(set) ?Closure $permission = null;

	/** Capability the current user needs, checked when no permission callback is given. */
	public private(set) ?string $capability = null;

	/** Doesn't change anything. Null when unknown. */
	public private(set) ?bool $readonly = null;

	/** May delete or overwrite data. Null when unknown. */
	public private(set) ?bool $destructive = null;

	/** Repeating it with the same input has no further effect. Null when unknown. */
	public private(set) ?bool $idempotent = null;

	/** Meant to be offered to external consumers such as agents. */
	public private(set) bool $public = false;

	public private(set) bool $showInRest = false;

	/** @var array<string, mixed> */
	public private(set) array $meta = [];

	/**
	 * @throws InvalidArgumentException When `$name` isn't `namespace/ability-name` in lowercase letters, digits and dashes.
	 */
	public function __construct( public readonly string $name, private readonly Abilities $abilities ) {
		if ( preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $name ) !== 1 ) {
			throw new InvalidArgumentException( "Ability name \"{$name}\" must look like my-plugin/do-something (lowercase letters, digits, dashes)." );
		}
	}

	public function label( string $label ): self {
		$this->label = $label;

		return $this;
	}

	public function description( string $description ): self {
		$this->description = $description;

		return $this;
	}

	/**
	 * @param string $category Slug of a registered ability category, e.g. `content`.
	 */
	public function category( string $category ): self {
		$this->category = $category;

		return $this;
	}

	/**
	 * @param array<string, mixed> $schema JSON Schema the input must match; WordPress validates it before execute().
	 */
	public function input( array $schema ): self {
		$this->inputSchema = $schema;

		return $this;
	}

	/**
	 * @param array<string, mixed> $schema JSON Schema of the result.
	 */
	public function output( array $schema ): self {
		$this->outputSchema = $schema;

		return $this;
	}

	/**
	 * What the ability does. Receives the validated input; returns the result. Throwing fails the run.
	 */
	public function execute( callable $callback ): self {
		$this->execute = Closure::fromCallable( $callback );

		return $this;
	}

	/**
	 * Who may run it. Receives the input; returns whether the current user may run it.
	 */
	public function permission( callable $callback ): self {
		$this->permission = Closure::fromCallable( $callback );
		$this->capability = null;

		return $this;
	}

	/**
	 * Only users with `$capability` may run it. A shortcut for permission().
	 */
	public function requireCapability( string $capability ): self {
		$this->capability = $capability;
		$this->permission = null;

		return $this;
	}

	public function readonly( bool $readonly = true ): self {
		$this->readonly = $readonly;

		return $this;
	}

	public function destructive( bool $destructive = true ): self {
		$this->destructive = $destructive;

		return $this;
	}

	public function idempotent( bool $idempotent = true ): self {
		$this->idempotent = $idempotent;

		return $this;
	}

	/**
	 * Offer it to external consumers such as AI agents.
	 */
	public function public( bool $public = true ): self {
		$this->public = $public;

		return $this;
	}

	public function showInRest( bool $show = true ): self {
		$this->showInRest = $show;

		return $this;
	}

	/**
	 * @param array<string, mixed> $meta Extra metadata stored with the ability.
	 */
	public function meta( array $meta ): self {
		$this->meta = $meta;

		return $this;
	}

	/**
	 * Registers it now if abilities are being registered, otherwise as soon as WordPress starts registering them.
	 *
	 * @throws InvalidArgumentException When a required part is missing.
	 */
	public function register(): void {
		$missing = $this->missing();
		if ( $missing !== [] ) {
			throw new InvalidArgumentException( "Ability {$this->name} is missing: " . implode( ', ', $missing ) . '.' );
		}

		$this->abilities->register( $this );
	}

	/**
	 * @return string[] Required parts that haven't been set.
	 */
	private function missing(): array {
		$missing = [];
		foreach ( [ 'label' => $this->label, 'description' => $this->description, 'category' => $this->category ] as $field => $value ) {
			if ( $value === '' ) {
				$missing[] = $field;
			}
		}
		if ( $this->execute === null ) {
			$missing[] = 'execute';
		}
		if ( $this->permission === null && $this->capability === null ) {
			$missing[] = 'permission or requireCapability';
		}

		return $missing;
	}
}

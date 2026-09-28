<?php declare( strict_types=1 );

namespace Merkushin\Wpal\Tools\Compare;

/**
 * One way two signatures of the same function differ.
 */
final class Difference {
	/** A parameter exists on the right (newer / WordPress) side only. */
	public const PARAMETER_ADDED = 'parameter-added';

	/** A parameter exists on the left (older / WPAL) side only. */
	public const PARAMETER_REMOVED = 'parameter-removed';

	public const PARAMETER_RENAMED = 'parameter-renamed';

	/** By-reference or variadic changed. */
	public const PARAMETER_KIND = 'parameter-kind';

	public const DEFAULT_ADDED = 'default-added';

	public const DEFAULT_REMOVED = 'default-removed';

	public const DEFAULT_CHANGED = 'default-changed';

	/** WordPress deprecated the function. */
	public const DEPRECATED = 'deprecated';

	/** WordPress no longer has the function. */
	public const REMOVED = 'removed';

	public function __construct(
		public readonly string $kind,
		public readonly string $message,
		public readonly ?string $parameter = null,
	) {
	}

	/**
	 * @return array<string, string>
	 */
	public function toArray(): array {
		return array_filter(
			[
				'kind'      => $this->kind,
				'parameter' => $this->parameter,
				'message'   => $this->message,
			],
			static fn ( $v ): bool => $v !== null
		);
	}
}

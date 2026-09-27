# WPAL – guide for agents and contributors

WPAL (WordPress Abstraction Layer) wraps the WordPress API in PHP objects so plugins can be unit-tested
without WordPress and, eventually, use a far better API than WordPress's own.

## Layers

| Namespace | What it is | PHP |
| --- | --- | --- |
| `Merkushin\Wpal\Service` | Bindings: one method per WordPress function, grouped into domain services (`Posts`, `Hooks`, `Assets`…). Each service is an interface plus a `final class Wp<Name>` that calls the WordPress function. | 7.4 |
| `Merkushin\Wpal\Api` | The designed API built on `Service` (not started yet). | 8.4 |

`ServiceFactory` hands out services: `create_<service>()` returns the `Wp*` class, `set_custom_<service>()` swaps in a
test double.

Today `Service` is hand-written. A generator (`bin/wpal`, planned) will take it over; from then on generated files must
never be edited by hand.

## Compatibility promise

- Calling `Service` never breaks unless WordPress breaks the same call. Signatures mirror WordPress: parameter names,
  order and defaults.
- Implementing `Service` interfaces is not supported. They gain methods and parameters whenever WordPress does.
  PHPUnit mocks (`createMock()`) are fine.
- Never remove or rename a `Service` method or change a signature except to mirror WordPress.
- Obsolete functions get a `@deprecated` docblock pointing to the replacement, not removal and not runtime notices.
- Each release targets the latest WordPress version.

CI enforces the promise with `roave/backward-compatibility-check` (`.roave-backward-compatibility-check.xml` allows
added methods and parameters in `Service`; everything else that breaks fails).

## Adding a WordPress function

1. Pick the service by domain, not by WordPress source file. If none fits, create a new service: interface,
   `final class Wp<Name>`, and a `create_*()` / `set_custom_*()` pair in `ServiceFactory` with tests in
   `tests/ServiceFactoryTest.php` (including the `tearDown()` reset).
2. Copy the signature from the current WordPress source exactly. Add native types only where WordPress guarantees
   them.
3. Copy WordPress's docblock into the interface; the `Wp*` method gets `@inheritDoc`.
4. Global classes in docblocks (`WP_Post`, `WP_Error`, `wpdb`…) need a `use` import, otherwise they resolve to
   `Merkushin\Wpal\Service\WP_Post`.
5. Skip private (`_`-prefixed) and deprecated functions.

## Code style

- `declare(strict_types=1);`, tabs, WordPress-style spacing inside parentheses, snake_case method names.
- `Service` code must stay valid PHP 7.4: no promoted properties, `readonly`, `match`, enums, `mixed` or union types.

## Commands

```bash
composer install
composer test      # PHPUnit
composer phpstan   # PHPStan with WordPress stubs, PHP 7.4 target
composer phpcs     # PHPCompatibility: Service must stay PHP 7.4
composer check     # all of the above
```

`phpstan-baseline.neon` holds errors inherited from WordPress's own docblocks. Don't add to it; fix new code instead.

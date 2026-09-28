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

Today `Service` is hand-written. A generator (`bin/wpal fix`, planned) will take it over; from then on generated files
must never be edited by hand.

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

## bin/wpal

Dev-only tool (PHP 8.1+, code in `tools/`) that keeps `Service` in step with WordPress. Every command accepts
`--format=json`. Exit codes: 0 nothing found, 1 drift or changes found, 2 error.

| Command | What it does |
| --- | --- |
| `bin/wpal snapshot [<version>\|latest]` | Parse a WordPress release into `api/wordpress.json`, the version `Service` targets |
| `bin/wpal check [--wp=<spec>]` | Compare `src/Service` with WordPress: renamed/added/removed parameters, changed defaults, deprecations |
| `bin/wpal diff <from> [<to>]` | What WordPress added, removed, deprecated or re-signed between two versions |
| `bin/wpal coverage [--untriaged]` | Wrapped vs planned vs ignored vs untriaged WordPress functions; flags mistakes in `wpal.map.php` |

A `<spec>` is `current` (the committed `api/wordpress.json`), `latest`, a version such as `7.0`, or a snapshot file.
Downloads and snapshots are cached in `build/`.

`wpal.map.php` says where WordPress functions belong:

- `services`: the service each function goes into, including ones not wrapped yet ("planned"). Theme template tags
  have their own `*Template` services (`PostTemplate`, `CommentTemplate`…) rather than growing the domain services.
- `ignore` / `ignore_files`: functions WPAL deliberately doesn't wrap, with a reason (handlers, internals, polyfills).
  Private and deprecated functions are ignored automatically.

Triage an untriaged function by adding it to one of these, never by leaving it out.

When a new WordPress version ships: `bin/wpal diff current latest` to see what changed, then `bin/wpal snapshot latest`
and `bin/wpal check` to see what `Service` must follow.

## Adding a WordPress function

1. Use the service `wpal.map.php` assigns; if it has none, pick one by domain, not by WordPress source file, and add
   the function to the map. If no service fits, create a new one: interface,
   `final class Wp<Name>`, and a `create_*()` / `set_custom_*()` pair in `ServiceFactory` with tests in
   `tests/ServiceFactoryTest.php` (including the `tearDown()` reset).
2. Copy the signature from the current WordPress source exactly. Add native types only where WordPress guarantees
   them.
3. Copy WordPress's docblock into the interface; the `Wp*` method gets `@inheritDoc`.
4. Global classes in docblocks (`WP_Post`, `WP_Error`, `wpdb`…) need a `use` import, otherwise they resolve to
   `Merkushin\Wpal\Service\WP_Post`.
5. Skip private (`_`-prefixed) and deprecated functions.
6. Run `bin/wpal check`: the new method must not show up as drifting.

## Code style

- `declare(strict_types=1);`, tabs, WordPress-style spacing inside parentheses, snake_case method names.
- `Service` code must stay valid PHP 7.4: no promoted properties, `readonly`, `match`, enums, `mixed` or union types.

## Commands

```bash
composer install
composer test      # PHPUnit
composer phpstan   # PHPStan: src at PHP 7.4 with WordPress stubs, tools at PHP 8.1
composer phpcs     # PHPCompatibility: Service must stay PHP 7.4
composer check     # all of the above
bin/wpal help      # the drift and coverage tool
```

`phpstan-baseline.neon` holds errors inherited from WordPress's own docblocks. Don't add to it; fix new code instead.

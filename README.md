# WPAL - WordPress Abstraction Layer

WPAL gives WordPress an object-oriented API that is pleasant to use and easy to unit-test without WordPress.

It has two layers:

- **Api** (PHP 8.4+): a designed API with real types, value objects, exceptions instead of `WP_Error`, and in-memory
  fakes for tests. Start here.
- **Service** (PHP 7.4+): every WordPress function as a method, generated from WordPress itself and kept in step with
  each release. Use it for anything the Api doesn't cover yet, or on older PHP.

## Api

```php
use Merkushin\Wpal\Api\Posts\SortBy;
use Merkushin\Wpal\Wpal;

$wp = new Wpal();

$wp->hooks()->onAction( 'wp_enqueue_scripts', function () use ( $wp ): void {
	$wp->assets()->script( 'my-plugin' )
		->src( plugins_url( 'build/app.js', __FILE__ ) )
		->deps( 'wp-element' )
		->data( 'myPlugin', [ 'limit' => $wp->options()->int( 'my_plugin_limit', 10 ) ] )
		->defer()
		->enqueue();
} );

$recent = $wp->posts()->query()->type( 'page' )->sortBy( SortBy::Modified )->limit( 5 )->get();
$post   = $wp->posts()->create( title: 'Hello', status: 'publish' ); // throws WordPressError on failure
```

What changes compared to WordPress:

- Hook callbacks receive as many arguments as they declare: no `$accepted_args`. `onAction()` returns a subscription
  you can `remove()`.
- `options()->get()` returns your default for a missing option, not `false`; `int()`, `bool()`, `string()` and
  `array()` convert WordPress's stored strings.
- Posts come back as `Post` value objects. `find()` returns `null`, `get()` throws `PostNotFound`, and failed writes
  throw `WordPressError`.
- Scripts are built fluently; `data()` passes JSON-encoded data safely instead of `wp_localize_script()`'s strings.

### Testing

Pass in-memory fakes for the services your code uses; they behave like WordPress without it:

```php
use Merkushin\Wpal\Api\Testing\FakeHooks;
use Merkushin\Wpal\Api\Testing\FakeOptions;
use Merkushin\Wpal\Wpal;

$wp = new Wpal( hooks: new FakeHooks(), options: new FakeOptions( [ 'my_plugin_limit' => '3' ] ) );

( new MyPlugin( $wp ) )->boot();
$wp->hooks()->doAction( 'init' );
```

## Service

Every WordPress function, grouped into services such as `Posts`, `Hooks` or `Assets`:

```php
use Merkushin\Wpal\ServiceFactory;

$assets = ServiceFactory::create_assets();
$assets->wp_enqueue_script( 'my-plugin', plugins_url( 'build/app.js', __FILE__ ), [], '1.0.0', [ 'in_footer' => true ] );
```

In tests, swap a service for a mock with `ServiceFactory::set_custom_assets( $mock )`; the Api's default services pick
it up too.

## Requirements

- PHP 7.4 or later; the Api layer needs PHP 8.4. On older PHP, `new Wpal()` throws a clear error and the Service layer
  works as before.
- The latest WordPress version. Each WPAL release targets the WordPress version that was current when it shipped; on an
  older WordPress, use an older WPAL release.

## Compatibility

- **Api** follows semantic versioning strictly.
- **Service** mirrors WordPress functions, so it follows WordPress:
  - Code that **calls** a service keeps working across releases unless WordPress itself breaks the same call.
  - **Implementing** service interfaces yourself is not supported: they gain methods and parameters whenever WordPress
    does. Use `ServiceFactory::set_custom_*()` with mocks (e.g. PHPUnit's `createMock()`) for tests.
  - Deprecated WordPress functions stay available and are marked `@deprecated`.

## Contributing

See [AGENTS.md](AGENTS.md) for the layout, conventions and checks. Run `composer check` before opening a pull request.

`merkushin/wpplugin` uses WPAL: https://github.com/merkushin/wpplugin/blob/main/src/Wpplugin.php

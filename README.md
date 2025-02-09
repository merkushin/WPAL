# WPAL - WordPress Abstraction Layer

One purpose of the library is to provide an object-oriented API for WordPress functions.
Another goal is to make it easier to write "pure" unit test for a WordPress plugin.

## How to use

Use ServiceFactory to get access to different WordPress services.

Here is an example:

```php
use Merkushin\Wpal\ServiceFactory;

$hooks = ServiceFactory::create_hooks();
$hooks->add_action( 'wp_enqueue_scripts', 'enqueue_frontend_scripts' );

public function enqueue_frontend_scripts() {
	$plugin_file = __FILE__;
	$assets = ServiceFactory::create_assets();
	$plugins = ServiceFactory::create_plugins();
	$assets->wp_enqueue_script(
		'wpplugin-frontend-scripts',
		$plugins->plugin_dir_url( $plugin_file ) . '/assets/dist/javascript/frontend.js',
		[],
		'1.0.0',
		true
	);
}
```

`merkushin/wpplugin` uses WPAL: https://github.com/merkushin/wpplugin/blob/main/src/Wpplugin.php



# Changelog

## 0.7.0

`Service` is now generated from WordPress by `bin/wpal fix` and mirrors WordPress 7.1.2. See the compatibility
promise in the README: code that calls a service keeps working unless WordPress changed the same call.

### Added

AI and agents:

- `abilities()`: describe abilities fluently (`define()->label()->...->register()`); WPAL registers them on the
  right hook whenever you call it and validates them up front. `execute()` runs one and throws instead of returning
  `WP_Error`. `FakeAbilities` runs them in tests, with a pretend user's capabilities.
- `ai()`: prompts through WordPress's AI Client with `generateText()` and `generateJson()`. `FakeAi` returns scripted
  responses and records prompts.
- `llms.txt`, `docs/api.md` and `docs/services.md`, generated from the code by `bin/wpal docs` and checked in CI, so
  coding agents get accurate docs.


Every public WordPress 7.1.2 function is now wrapped: 2,279 methods in 109 services, from `Abilities` and `Ai` to
`Widgets`. The 1,836 functions left out are private, deprecated, or ignored with a reason in `wpal.map.php` (hook
callbacks, admin screen internals, installer internals…).


The Api layer (PHP 8.4+), a designed API built on `Service`, starting with four services. `new Wpal()` is the entry
point; each service also has an in-memory fake for tests (`Api\Testing\Fake*`).

- `hooks()`: `onAction()`/`onFilter()` return a `Subscription` you can `remove()`; callbacks receive as many arguments
  as they declare.
- `options()`: `get()` returns your default instead of `false`; typed `string()`, `int()`, `bool()`, `array()`.
- `assets()`: fluent `script()`/`style()` builders with `defer()`, `async()`, `inFooter()`, safe `data()` and
  `translations()`.
- `posts()`: `find()`/`get()` return `Post` value objects; immutable `query()` builder; `create()`/`update()` with named
  arguments throw `WordPressError` instead of returning `WP_Error`.

New and extended services the Api builds on: `Options` (options, site and network options), more of `Assets`
(register, dequeue, inline styles, translations), site transients in `Transient`, and `register_setting()` in
`Settings`.


474 methods and 26 services covering WordPress's template tags and the rest of its template files, 887 methods in all:

- Theme output: `PostTemplate`, `CommentTemplate`, `TermTemplate`, `AuthorTemplate`, `ArchiveTemplate`,
  `AdminTemplate`, `MediaTemplate`.
- `Navigation`, `Feeds`, `Permalinks`, `Templates`, `BlockTemplates`, `DocumentHead`, `SiteIdentity`, `EditLinks`,
  `Login`, `Editor`, `Forms`, `MetaBoxes`, `Search`, `Avatars`, `NavMenus`, `Bookmarks`, `PostThumbnails`, `Urls`,
  `Settings`.
- `Assets::add_thickbox()` and `Screen::convert_to_screen()`.

Each new service has a `ServiceFactory::create_*()` / `set_custom_*()` pair.

### Fixed

- `Posts::wp_unique_post_slug()` passed `$post_status` where `$post_type` belonged.
- `PostAttachments::wp_count_attachments()` ignored its argument and always counted all MIME types.
- `Plugins::add_allowed_options()` and `remove_allowed_options()` ignored the `$options` argument.
- `PostAttachments::update_attached_file()` threw a `TypeError` when WordPress returned a meta ID; its `bool` return
  type is gone.
- `Comments::pingback()` now returns WordPress's result instead of discarding it.
- Docblocks now import the WordPress classes they name (`WP_Post`, `WP_Error`, `wpdb`…), so IDEs and static analysis
  resolve them.

### Changed, following WordPress

- `Hooks::has_filter()` and `has_action()` accept `$priority` (WordPress 6.9).
- `Assets::wp_enqueue_script()`'s fifth parameter is `$args` (`array|bool`, WordPress 6.3); passing `true`/`false`
  still works.
- `Localization::load_textdomain()` accepts `$locale`, `unload_textdomain()` accepts `$reloadable`,
  `Posts::wp_mime_type_icon()` accepts `$preferred_ext`.
- `Localization::load_script_textdomain()`'s `$path` defaults to `''`; `Posts::wp_get_post_parent_id()`'s `$post`
  defaults to `null`.
- 39 parameters renamed to WordPress's current names, e.g. `$function` → `$callback` on the `add_*_page()` family.
  This only matters for calls that use named arguments.
- Docblocks updated to WordPress 7.1.2's.

### Deprecated, following WordPress

`Capabilities::current_user_can_for_blog()`, `Comments::wp_queue_comments_for_comment_meta_lazyload()`,
`Localization::_get_path_to_translation()`, `Localization::_get_path_to_translation_from_lang_dir()`,
`PostAttachments::wp_get_attachment_thumb_file()`, `Posts::get_page_by_title()`.

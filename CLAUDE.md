<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.26
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v3
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `fluxui-development` — Develops UIs with Flux UI Free components. Activates when creating buttons, forms, modals, inputs, dropdowns, checkboxes, or UI components; replacing HTML form elements with Flux; working with flux: components; or when the user mentions Flux, component library, UI components, form fields, or asks about available Flux components.
- `livewire-development` — Develops reactive Livewire 4 components. Activates when creating, updating, or modifying Livewire components; working with wire:model, wire:click, wire:loading, or any wire: directives; adding real-time updates, loading states, or reactivity; debugging component behavior; writing Livewire tests; or when the user mentions Livewire, component, counter, or reactive UI.
- `pest-testing` — Tests applications using the Pest 3 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, architecture testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.
- `tailwindcss-development` — Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.
- `developing-with-fortify` — Laravel Fortify headless authentication backend development. Activate when implementing authentication features including login, registration, password reset, email verification, two-factor authentication (2FA/TOTP), profile updates, headless auth, authentication scaffolding, or auth guards in Laravel applications.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd and will be available at: `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs for the user.
- You must not run any commands to make the site available via HTTP(S). It is always available through Laravel Herd.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== fluxui-free/core rules ===

# Flux UI Free

- Flux UI is the official Livewire component library. This project uses the free edition, which includes all free components and variants but not Pro components.
- Use `<flux:*>` components when available; they are the recommended way to build Livewire interfaces.
- IMPORTANT: Activate `fluxui-development` when working with Flux UI components.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces using only PHP — no JavaScript required.
- Instead of writing frontend code in JavaScript frameworks, you use Alpine.js to build the UI when client-side interactions are required.
- State lives on the server; the UI reflects it. Validate and authorize in actions (they're like HTTP requests).
- IMPORTANT: Activate `livewire-development` every time you're working with Livewire-related tasks.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.

=== laravel/fortify rules ===

# Laravel Fortify

- Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.
- IMPORTANT: Always use the `search-docs` tool for detailed Laravel Fortify patterns and documentation.
- IMPORTANT: Activate `developing-with-fortify` skill when working with Fortify authentication features.

</laravel-boost-guidelines>

## Project Overview

- Laravel 12 / Livewire 4 Volt / Flux UI Free / Tailwind v4
- Served by Laravel Herd at `https://webprocms.test`
- `config/navigation.php` — per-website-type nav/footer config, written at runtime
- `routes/web.php` — also written at runtime when pages are created/cloned
- `config('features.website_type')` — current site type (e.g. `saas`) from `.env WEBSITE_TYPE`

## Footer Requirement

Every footer must include "Powered by WebProCMS" linking to `https://www.webprocms.com`.
Applies to: `resources/design-library/rows/footer/`, `resources/views/layouts/public.blade.php`, any new footer section.

## CSS Bundles

Three separate bundles — do NOT cross-contaminate sources:

- `resources/css/app.css` — dashboard/admin UI; includes Flux CSS, sources `views/components`, `views/flux`, `views/layouts/app*`, `views/layouts/auth*`, `views/partials`, `views/pages/auth`, `views/pages/dashboard`, `views/pages/settings`, vendor Flux stubs; does NOT source public pages, design library, or editor views
- `resources/css/public.css` — public-facing site; sources page views and components only; no Flux, no design library
- `resources/css/editor.css` — page editor; includes Flux CSS, sources design library (`../design-library/**/*.blade.php`) and vendor Flux stubs; design library belongs here because the editor is what inserts rows into pages

## CSS Theme Tokens

Tokens defined in `resources/css/app.css` `@theme {}`. When adding new tokens, also update `resources/js/tw-autocomplete.js`.
Current tokens: `primary`, `font-heading`, `py-section`, `rounded-card`, `shadow-card`, `accent`.

## Memories

When the user asks to remember something, ask whether it should go in their **personal memory** (`~/.claude/projects/.../memory/MEMORY.md`, local only) or **CLAUDE.md** (shared with anyone who clones the repo).

## Design Library

Location: `resources/design-library/rows/[category]/[name].blade.php`

### Slug format

Row slugs use the format `{templateName}:{randomId}` — e.g. `features-grid:Z7Jgur`. The template name (filename without `.blade.php`) is embedded so the runtime can look up field definitions without a separate mapping.

### Required metadata block

Every row file must have a frontmatter comment with `@name`, `@description`, and `@sort`. Field type and group are inferred from the key name:

```blade
{{--
@name Category - Name
@description One-line description.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
```

### Key naming conventions (type + group inference)

Type and group are derived entirely from the key name — no metadata columns needed:

| Key pattern | Type | Group (derived by stripping prefix/suffix) |
|-------------|------|--------------------------------------------|
| `toggle_*` | `toggle` | key with `toggle_` stripped |
| `grid_*` | `grid` | key with `grid_` stripped |
| `*_new_tab` | `toggle` | key with `_new_tab` stripped |
| `*_classes` | `classes` | key with `_classes` stripped |
| `*_image` or `image` | `image` | key with `_image` stripped (or `media`) |
| `*_htag` | `text` | key with `_htag` stripped — renders as h1–h4 dropdown in editor |
| `*_url` | `text` | key with `_url` stripped |
| `*_alt` | `text` | key with `_alt` stripped |
| anything else | `text` | key itself |

- `label` is auto-derived: `ucwords(str_replace('_', ' ', $key))`
- Field order in the editor sidebar = document order of `<x-dl.*>` component tags and `@dlItems` directives (first occurrence wins)

**Sidebar ordering rule:** Place `<x-dl.*>` component tags inline where they render in the HTML, in natural top-to-bottom order. `x-dl.*` components register their fields in the order declared in `schemaFields()` — toggle first, then text fields, then classes. `section_classes` and `section_container_classes` are registered first because `<x-dl.section>` is always the outermost element.

### Content Types

| Type | Editor UI | Notes |
|------|-----------|-------|
| `text` | Single-line input | Default |
| `richtext` | Multi-line textarea | HTML supported |
| `toggle` | Switch | Default `'1'` = shown, `''` = hidden |
| `image` | Upload / media picker | Returns Storage URL |
| `classes` | Monospace textarea + TW autocomplete | Falls back to default if empty |
| `grid` | Repeater (add/edit/remove items) | JSON-encoded array; item keys inferred from first item |

### Section / Container Pattern (required on every row)

Use `x-dl.section` to wrap every row. It renders the outer HTML element + inner container div, and registers `section_classes` and `section_container_classes` fields:

```blade
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    {{-- ... row content ... --}}
</x-dl.section>
```

- Default tag is `section`. Pass `tag="footer"`, `tag="header"`, or `tag="article"` when the semantic element differs.
- Extra attributes (e.g. `x-data`, `x-init`) are forwarded to the rendered outer element via `$attributes->merge()`.
- `section_classes` and `section_container_classes` also appear in the inline design panel on the row card (paintbrush button).

### Complete row skeleton

Full example assembling all standard patterns (copy and adapt):

```blade
{{--
@name Category - Name
@description One-line description.
@sort 10
--}}
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    <x-dl.heading slug="__SLUG__" prefix="headline" default="Your Headline"
        default-tag="h2"
        default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
    <x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Your supporting text."
        default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
    <x-dl.buttons slug="__SLUG__"
        default-wrapper-classes="mt-8 flex flex-wrap items-center justify-center gap-4"
        default-primary-label="Get Started"
        default-primary-classes="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
        default-secondary-label="Learn More"
        default-secondary-classes="px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors" />
</x-dl.section>
```

### ALL classes must go through x-dl.* components

**Never use hardcoded class strings directly on elements.** Every element's classes must be editable — wrap it in the appropriate `<x-dl.*>` component. This applies to cards, wrappers, icons, headings, paragraphs, grids, buttons, forms, layout divs — **every single element with a `class` attribute**, no exceptions.

**This is a hard rule. Before finishing any row, scan every line for `class="` and verify each one is inside an `<x-dl.*>` component. If you find a hardcoded `class="..."` that isn't an Alpine `:class` binding or a placeholder-only element (e.g. an image fallback inside `@else`), it must be fixed.**

The only acceptable hardcoded classes are:
- Alpine `:class` dynamic bindings (e.g. `:class="open ? 'rotate-180' : ''"`) — these are runtime expressions, not design values
- Text/icon placeholders shown only inside `@else` when no image/content is set — these are developer fallbacks, not user-facing design

### Conditional (state-variant) classes

When an element has two visual states driven by per-item data (e.g. a featured card vs. a default card), use `x-dl.card` with `default-featured-classes` and `:featured`:

```blade
@php $isFeatured = !empty($plan['toggle_featured']); @endphp
<x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
    default-classes="rounded-card p-8 bg-white border border-zinc-200"
    default-featured-classes="rounded-card p-8 bg-primary text-white ring-2 ring-primary">
    ...
</x-dl.card>
```

Name pairs as `{element}_classes` (default) and `{element}_featured_classes` (highlighted state). Apply this to every element inside the loop that has different classes per state.

If the per-item data itself (names, prices, feature lists, etc.) is hardcoded in PHP, convert it to a `grid_*` field so it becomes editable. Store sub-lists (e.g. bullet features per card) as a pipe-separated string (`5 projects|10GB storage`) and split on render with `explode('|', ...)`. Use `toggle_featured` as the key within grid items so the editor infers it as a toggle switch.

See `resources/design-library/rows/pricing/pricing-cards.blade.php` for the reference implementation.

### Design Library Components (`x-dl.*`)

Four Blade components handle the repeating standard patterns. Each lives in `app/View/Components/Dl/` (PHP class) and `resources/views/components/dl/` (blade view starting with `@blaze`). Each class has a `public static function schemaFields(array $attrs): array` that the parser calls to register fields — so the component tag in the template IS the field declaration.

**`x-dl.section`** — section/container wrapper (2 fields: `section_classes`, `section_container_classes`). Wrapping component — use it as the outermost element of every row:
```blade
<x-dl.section slug="__SLUG__"
    default-section-classes="py-section px-6 bg-white dark:bg-zinc-900"
    default-container-classes="max-w-6xl mx-auto">
    {{-- slot content --}}
</x-dl.section>
```
Pass `tag="footer"` / `tag="header"` / `tag="article"` when the semantic element differs from `section`. Extra attributes (`x-data`, etc.) are forwarded to the rendered element.

**`x-dl.heading`** — toggle + htag dropdown + text + classes (4 fields):
```blade
<x-dl.heading slug="__SLUG__" prefix="headline" default="Your Headline"
    default-tag="h2"
    default-classes="font-heading text-4xl font-bold text-zinc-900 dark:text-white" />
```

**`x-dl.subheadline`** — toggle + text + classes (3 fields):
```blade
<x-dl.subheadline slug="__SLUG__" prefix="subheadline" default="Your subheadline."
    default-classes="mt-4 text-lg text-zinc-500 dark:text-zinc-400" />
```

**`x-dl.buttons`** — wrapper div + primary button + secondary button (11 fields, renders the wrapper `<div>` itself):
```blade
<x-dl.buttons slug="__SLUG__"
    default-wrapper-classes="mt-8 flex flex-wrap items-center gap-4"
    default-primary-label="Get Started"
    default-primary-classes="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors"
    default-secondary-label="Learn More"
    default-secondary-classes="px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors" />
```

**`x-dl.media`** — toggle + image upload + alt + wrapper/image classes (5 fields, always uses `image`, `toggle_image`, `image_alt`, `image_wrapper_classes`, `image_classes` keys). Fixed keys — only one per row. Use `x-dl.image` when you need multiple images or prefix-based keys:
```blade
<x-dl.media slug="__SLUG__"
    default-wrapper-classes="rounded-card overflow-hidden aspect-video"
    default-image-classes="w-full h-full object-cover" />
```

**`x-dl.image`** — prefix-based image (5 fields). Unlike `x-dl.media`, supports a `prefix` so you can have multiple images in one row without key collisions:
```blade
<x-dl.image slug="__SLUG__" prefix="hero_image"
    default-wrapper-classes="rounded-card overflow-hidden aspect-video"
    default-image-classes="w-full h-full object-cover" />
```
Fields registered: `toggle_{prefix}`, `{prefix}_image`, `{prefix}_image_alt`, `{prefix}_wrapper_classes`, `{prefix}_image_classes`.

**`x-dl.video`** — YouTube/Vimeo embed (4 fields). Parses `youtube.com/watch?v=ID`, `youtu.be/ID`, and `vimeo.com/ID` URLs into embed format automatically:
```blade
<x-dl.video slug="__SLUG__" prefix="demo_video"
    default-wrapper-classes="rounded-card overflow-hidden aspect-video"
    default-video-classes="w-full h-full"
    default-video-url="https://www.youtube.com/watch?v=..." />
```
Fields registered: `toggle_{prefix}`, `{prefix}_video_url`, `{prefix}_wrapper_classes`, `{prefix}_video_classes`.

**`x-dl.link`** — toggle + label text + URL + new_tab toggle + classes (5 fields). Use for "View all →" style inline links alongside headings:
```blade
<x-dl.link slug="__SLUG__" prefix="view_all"
    default-label="View all →"
    default-url="/blog"
    default-classes="text-primary font-semibold hover:text-primary/80 transition-colors text-sm" />
```
Fields registered: `toggle_{prefix}`, `{prefix}` (label text), `{prefix}_url`, `{prefix}_new_tab`, `{prefix}_classes`.

**`x-dl.wrapper`** — generic element wrapper (classes only, no toggle). Use for **leaf-level elements** — those whose children are only text, self-closing components (`x-dl.icon`, `x-dl.heading`, etc.), or plain HTML with no non-self-closing wrapper children. Forwards all non-prop attributes (href, type, wire:model, src, alt, etc.) to the rendered element. Void elements (input, img, br, etc.) self-close without a slot.
```blade
{{-- Simple div (leaf) --}}
<x-dl.wrapper slug="__SLUG__" prefix="text_block" default-classes="p-6 rounded-card border border-zinc-200">
    <!-- text or self-closing children only -->
</x-dl.wrapper>

{{-- Void element (no slot) --}}
<x-dl.wrapper slug="__SLUG__" prefix="input" tag="input"
    type="email" name="email" wire:model="email"
    default-classes="block w-full rounded-lg border border-zinc-300 px-4 py-3" />

{{-- Text element in loop --}}
<x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3"
    default-classes="text-lg font-semibold text-zinc-900 dark:text-white mb-2">
    {{ $feature['title'] }}
</x-dl.wrapper>
```
Fields registered: `{prefix}_classes` (+ `{prefix}_featured_classes` if `default-featured-classes` attr is provided).

**`x-dl.card`** — outermost loop item wrapper. Use as the **direct child of `@foreach`** inside a grid or gallery. Identical API to `x-dl.wrapper` but uses a different Blaze-compiled hash, preventing variable collision when loop items contain nested wrappers.
```blade
{{-- Outermost loop item --}}
<x-dl.card slug="__SLUG__" prefix="feature_card"
    default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700">
    <x-dl.wrapper slug="__SLUG__" prefix="feature_title" tag="h3" ...>{{ $feature['title'] }}</x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="feature_desc" tag="p" ...>{{ $feature['desc'] }}</x-dl.wrapper>
</x-dl.card>

{{-- With featured state --}}
<x-dl.card slug="__SLUG__" prefix="card" :featured="$isFeatured"
    default-classes="rounded-card p-8 bg-white border border-zinc-200"
    default-featured-classes="rounded-card p-8 bg-primary text-white ring-2 ring-primary">
    ...
</x-dl.card>
```
Fields registered: same as `x-dl.wrapper`.

**`x-dl.group`** — intermediate wrapper that **itself contains other non-self-closing wrappers** as children (e.g. a price row containing a period span, a features list containing item wrappers). Use when you need a container that is neither the outermost loop item (`x-dl.card`) nor a leaf element (`x-dl.wrapper`).
```blade
{{-- Intermediate container holding other non-self-closing wrappers --}}
<x-dl.group slug="__SLUG__" prefix="author_row"
    default-classes="mt-4 flex items-center gap-3">
    <x-dl.wrapper slug="__SLUG__" prefix="author_name" tag="span" ...>{{ $item['name'] }}</x-dl.wrapper>
    <x-dl.wrapper slug="__SLUG__" prefix="author_role" tag="span" ...>{{ $item['role'] }}</x-dl.wrapper>
</x-dl.group>
```
Fields registered: same as `x-dl.wrapper`.

**Nesting rule — which wrapper to use:**

| Scenario | Use |
|----------|-----|
| Outermost element of a `@foreach` loop item | `x-dl.card` |
| Container inside a loop item that itself wraps other non-self-closing components | `x-dl.group` |
| Leaf element (children are text, self-closing tags, or plain HTML only) | `x-dl.wrapper` |
| Outside of any loop | `x-dl.wrapper` |

**Why this matters:** Blaze compiles non-self-closing `<x-dl.*>` tags using a `$__attrs{hash}` variable where the hash is derived from the component's file path. If two nested non-self-closing tags share the same component (same file path = same hash), the inner assignment overwrites the outer one, causing the outer element to render with the wrong tag/classes. `x-dl.card`, `x-dl.group`, and `x-dl.wrapper` each have unique file paths and therefore unique hashes — they can be safely nested.

**`x-dl.icon`** — heroicon with optional wrapper div. Handles `name:variant` parsing (e.g. `"bolt:solid"`). Supports featured class switching. Wrapper is only rendered when `default-wrapper-classes` is non-empty.
```blade
{{-- With wrapper (features grid) --}}
<x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
    default-wrapper-classes="mb-4 text-primary"
    default-classes="size-8" />

{{-- Without wrapper, with featured state (pricing checkmark) --}}
<x-dl.icon slug="__SLUG__" prefix="card_feature_icon" name="check"
    :featured="$isFeatured"
    default-classes="size-4 shrink-0 text-primary"
    default-featured-classes="size-4 shrink-0 text-white" />
```
Fields registered: `{prefix}_wrapper_classes` (only if wrapper provided), `{prefix}_classes`, `{prefix}_featured_classes` (if `default-featured-classes` attr present).

**`x-dl.grid`** — toggle + JSON repeater + grid wrapper classes (3 fields: `toggle_{prefix}`, `grid_{prefix}`, `{prefix}_grid_classes`). Wrapping component — slot contains the per-item loop using `@dlItems`. Use `x-dl.card` as the outermost loop item (see nesting rule below):
```blade
<x-dl.grid slug="__SLUG__" prefix="features"
    default-grid-classes="grid md:grid-cols-3 gap-8"
    default-items='[{"icon":"bolt","title":"Fast","desc":"Speed."}]'>
    @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Fast","desc":"Speed."}]')
    @foreach ($features as $feature)
        <x-dl.card slug="__SLUG__" prefix="feature_card" default-classes="p-6 rounded-card border border-zinc-200">
            ...
        </x-dl.card>
    @endforeach
</x-dl.grid>
```
Note: `default-items` uses **single quotes** in the template because its value is JSON (which contains double quotes). The attr parser supports both quote styles. **Always pass the same JSON as the 4th arg to `@dlItems`** — without it, fresh rows with no saved content render empty (the default only populates the schema, not the runtime output).

**`x-dl.gallery`** — same 3 fields as `x-dl.grid` (`toggle_{prefix}`, `grid_{prefix}`, `{prefix}_grid_classes`) but semantically for image galleries. The editor shows an **"Add images from library"** bulk-select button on any grid field whose items contain an `image` key. Individual image sub-fields within items render a thumbnail + "Pick from library…" button instead of a text input. Use with `@dlItems` the same way as `x-dl.grid`:
```blade
<x-dl.gallery slug="__SLUG__" prefix="images"
    default-grid-classes="grid grid-cols-2 md:grid-cols-3 gap-4"
    default-items='[{"image":"","alt":"Photo 1","caption":""}]'>
    @dlItems('__SLUG__', 'images', $galleryImages, '[{"image":"","alt":"Photo 1","caption":""}]')
    @foreach ($galleryImages as $img)
        <x-dl.card slug="__SLUG__" prefix="gallery_item" default-classes="...">
            @if ($img['image'])
                <img src="{{ Storage::url($img['image']) }}" alt="{{ $img['alt'] }}" class="w-full h-full object-cover">
            @else
                <div class="...">{{ $img['alt'] ?: 'Placeholder' }}</div>
            @endif
        </x-dl.card>
    @endforeach
</x-dl.gallery>
```
The `@else` fallback div may use hardcoded classes — it's a developer placeholder shown only when no image is set (acceptable exception per CLAUDE.md rules). Include the same default JSON as the 4th arg to `@dlItems` so the design library preview renders placeholder items.

**`@dlItems` directive** — fetches and decodes grid item data. **Always include the 4th default JSON argument** — without it, fresh rows render empty because `content()` returns `''` when nothing is saved yet. The 4th arg must match the `default-items` value on the wrapping component:
```blade
{{-- Inside x-dl.grid / x-dl.gallery — always include the default JSON --}}
@dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Fast","desc":"Speed."}]')

{{-- Standalone (parser registers grid_testimonials via this directive; field not registered by a component) --}}
@dlItems('__SLUG__', 'testimonials', $testimonials, '[{"quote":"...","name":"...","role":"..."}]')
```

**One pattern only:** Row templates use exclusively `<x-dl.*>` component tags and `@dlItems`. There are no raw `content()` calls in row files.

**Adding a new `x-dl.*` component:** Create `app/View/Components/Dl/Name.php` with a `schemaFields(array $attrs): array` static method and a `render()` method. Create `resources/views/components/dl/name.blade.php` starting with `@blaze`. The parser auto-discovers it by class name via `Str::studly($componentSlug)`. The PHP class file is already scanned by `public.css` and `editor.css` via `@source '../../app/View/Components/Dl/*.php'` — no CSS config change needed when adding new components to this directory.

**Editor icons checklist for new components:** The editor shows up to three icons per field group — Content (has text/toggle/grid/image/richtext fields), Design (has `_classes` fields), Advanced (has `_id`/`_attrs` fields). When creating a new component that renders an element with styleable classes, also register `{prefix}_id` and `{prefix}_attrs` fields in `schemaFields()` so the Advanced icon appears. Use `'label' => 'Element ID'` and `'label' => 'Custom Attributes'` with defaults `''` and `'[]'`. Also apply them in the blade view (see `wrapper.blade.php` for the pattern). Grid item sub-field types: `desc`/`description`/`answer` → textarea; `icon` → icon picker; `image`/`*_image` → image picker; everything else → text input.

The editor auto-promotes a `toggle_X` field to the group header switch when every other field in the group has a key containing `X` or ending with `_new_tab`.

### Grid rows

- Use the `grid_` prefix for the grid field key: `grid_features`, `grid_items`, etc.
- The `default-items` value is a single-line JSON array (use single quotes around the attr because JSON uses double quotes)
- Item keys are inferred from the keys of the first item; new items use those same keys
- Always use `@dlItems` inside the slot to fetch the decoded array, **with the same JSON as the 4th argument** (without it fresh rows render empty)

```blade
<x-dl.grid slug="__SLUG__" prefix="features"
    default-grid-classes="grid md:grid-cols-3 gap-8"
    default-items='[{"icon":"bolt","title":"Fast","desc":"Speed."}]'>
    @dlItems('__SLUG__', 'features', $features, '[{"icon":"bolt","title":"Fast","desc":"Speed."}]')
    @foreach ($features as $feature)
        <x-dl.card slug="__SLUG__" prefix="feature_card"
            default-classes="p-6 rounded-card border border-zinc-200 dark:border-zinc-700">
            {{-- ... render $feature fields using x-dl.wrapper, x-dl.icon, etc. ... --}}
        </x-dl.card>
    @endforeach
</x-dl.grid>
```

### Heroicons in grid rows

Icons stored as `"bolt"` (outline) or `"bolt:solid"` (solid). Use `x-dl.icon` — it handles the `name:variant` parsing internally:

```blade
<x-dl.icon slug="__SLUG__" prefix="icon" name="{{ $feature['icon'] }}"
    default-wrapper-classes="mb-4 text-primary"
    default-classes="size-8" />
```

`<x-heroicon>` is **public side only** — it is NOT available via `<flux:icon>` on the public layout (Flux only works on the dashboard side). `x-dl.icon` uses `<x-heroicon>` internally.

### Adding a new row to the design library

1. Create a new `.blade.php` file in the appropriate category folder
2. Add the metadata comment with `@name`, `@description`, and `@sort`
3. Use only `<x-dl.*>` component tags and `@dlItems` — the parser only scans these, not raw PHP
4. Run `php artisan design-library:index` to register it in the database

No manual DB insert is required — the index command handles it.

### Custom IDs and Attributes

Every row must support custom element IDs and HTML attributes for jump links and third-party integrations. This is provided automatically by the standard `x-dl.*` components:

- `x-dl.section` — always registers `section_id` and `section_attrs` fields (Advanced tab in editor)
- `x-dl.heading` — always registers `{prefix}_id` (Advanced tab)
- `x-dl.wrapper` / `x-dl.card` / `x-dl.group` — always registers `{prefix}_id` (Advanced tab)

Since these are built into the components, any row built using the standard `x-dl.*` skeleton automatically inherits this support. No extra steps needed — just use `x-dl.section` as the outer wrapper (required on every row) and the `id`/`attrs` fields appear in the Advanced tab automatically.

### Other notes

- Template files only affect newly inserted rows. Existing page blade files have row code copied inline at insert time — update them separately if needed.

## Key Lessons

### Alpine + Livewire: Always Use `wire:ignore` on Alpine Grid Containers

The editor's grid/attrs `x-data` containers in `content-field.blade.php` use `wire:ignore`. **Never remove it.** When `$wire.set` triggers a Livewire round-trip, morphdom can overwrite or reinitialize the Alpine `x-data` element, resetting `items` to the stale PHP-rendered value — even if Alpine already pushed a new item. `wire:ignore` prevents Livewire from touching the container; the `x-on:content-grid-reset.window` / `x-on:content-attrs-reset.window` listeners handle all server-initiated state resets (gallery picks, Reset/Remove All).

**Applies to:** any `<div x-data="{ items: ... }">` that has its own `sync()` → `$wire.set()` cycle and a corresponding reset event listener.

### Alpine v3: Never Nest `x-data` Inside `x-for` When Parent Methods Are Needed

In Alpine v3, a nested `x-data` inside an `x-for` loop creates a child component scope. When methods like `removeItem(idx)` are called from inside that child scope, `this` is bound to the **child** data object (e.g. `{ open: false }`), not the parent — so `this.items` is `undefined` and the call fails silently.

**Wrong:**
```html
<template x-for="(item, idx) in items">
    <div x-data="{ open: false }">                          {{-- child scope --}}
        <button @click="removeItem(idx)">...</button>       {{-- this.items = undefined --}}
    </div>
</template>
```

**Correct:** track open state in the parent's `x-data` using an object keyed by index:
```html
{{-- parent x-data: add openItems: {} --}}
<template x-for="(item, idx) in items">
    <div>                                                   {{-- no child x-data --}}
        <button @click="openItems[idx] = !openItems[idx]">...</button>
        <button @click="removeItem(idx)">...</button>       {{-- this.items = parent's array ✓ --}}
        <div x-show="openItems[idx]">...</div>
    </div>
</template>
```

This applies anywhere parent methods or reactive data are needed inside a loop item.

### Vite HMR Reloads on Runtime-Written Files

`refresh: true` watches `config/**` by default. Runtime writes to config files trigger full page reload, wiping Alpine state. Fix: add to `watch.ignored` in `vite.config.js`. Already ignored: `config/navigation.php`, `routes/web.php`, `resources/views/pages/⚡*.blade.php`.

### `:class` vs `x-bind:class` on Blade components

Any Blade component (including `x-dl.*` and Flux) PHP-evaluates `:class="expr"` when `expr` contains **no `{{ }}`**. If `expr` has Alpine vars (no `$` prefix), PHP treats them as undefined constants → error. Always use `x-bind:class="expr"` to pass pure Alpine expressions through to the DOM.

Exception: `:class="something === {{ $phpVar }} ? ..."` works because the `{{ }}` echo tag makes Blade treat the value as a string with PHP interpolation, not full PHP evaluation — the resulting string is passed as-is to the DOM for Alpine to evaluate.

### Sidebar Nav (flux:sidebar)

File: `resources/views/layouts/app/sidebar.blade.php`

- Never nest `flux:sidebar.group` inside another — Blaze reuses the same PHP variable, causing outer group to render with wrong props.
- Expandable groups must be siblings of (not children of) the Platform group.

### Livewire File Uploads: Two Size Limits

1. Livewire temp upload endpoint — `config/livewire.php` `temporary_file_upload.rules` (returns 422 if exceeded)
2. Component-level `$this->validate()` — runs after temp upload

Both must match. Default temp limit is 12MB.

### Infrastructure Data in Migrations

Seeders are for demo data only. Data every install must have belongs in the migration using `DB::table()->insert()`.

### Bulk Design Library Row Edits

When asked to update many design library rows at once (e.g. "make all classes editable"), **skip directly to a single Task subagent call** (subagent_type: `general-purpose`). Do NOT attempt Read → Write/Edit in the main context — context compression causes the "File has not been read yet" error on Write/Edit even after successful reads. The subagent has fresh context, reads and writes files without that issue, and handles all files in one shot. Provide the full list of files and required changes in the prompt.

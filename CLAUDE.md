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
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
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
- Field order in the editor sidebar = order of `content()` calls in the blade (first occurrence wins)

**Sidebar ordering rule:** Hoist `*_classes` variables to the top of the template (they are hidden in content mode anyway). Place all non-classes `content()` calls — `toggle_*`, text fields, `grid_*` — inline where they are used in the HTML, in the natural top-to-bottom order a user would expect. Never hoist a `grid_*` or text `content()` call above the section it belongs to just to pre-declare a variable — move the decode/variable assignment to just before the loop instead. This keeps the content mode sidebar in a logical reading order (e.g. Headline → Subheadline → Plans, not Plans → Headline → Subheadline).

### content() helper

```php
content(string $slug, string $key, ?string $default = null): string
```

- `__SLUG__` is replaced at insert time with the row's unique slug
- Type and group are resolved from `SchemaCache` (populated by `design-library:index` from parsing the blade)
- The inline default is a PHP runtime fallback; use `''` only if the value is optional

**Standard call (3 args only):**
```php
@php $headlineText = content('__SLUG__', 'headline', 'My Headline'); @endphp
```

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

```blade
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        {{-- ... row content ... --}}
    </div>
</section>
```

`section_classes` and `section_container_classes` also appear in the inline design panel on the row card (paintbrush button). The key `section_container_classes` infers type `classes`, group `section_container`.

### Complete row skeleton

Full example assembling all standard patterns (copy and adapt):

```blade
{{--
@name Category - Name
@description One-line description.
@sort 10
--}}
@php $sectionClasses = content('__SLUG__', 'section_classes', 'py-section px-6 bg-white dark:bg-zinc-900'); @endphp
<section class="{{ $sectionClasses }}">
    @php $sectionContainerClasses = content('__SLUG__', 'section_container_classes', 'max-w-6xl mx-auto'); @endphp
    <div class="{{ $sectionContainerClasses }}">
        @php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
        @if($toggleHeadline)
        @php $headlineTag = content('__SLUG__', 'headline_htag', 'h2'); @endphp
        @php $headlineText = content('__SLUG__', 'headline', 'Your Headline'); @endphp
        @php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
        {!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
        @endif
        @php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
        @if($toggleSubheadline)
        @php $subheadlineText = content('__SLUG__', 'subheadline', 'Your supporting text.'); @endphp
        @php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
        <p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
        @endif
        @php $buttonsWrapperClasses = content('__SLUG__', 'buttons_wrapper_classes', 'mt-8 flex flex-wrap items-center justify-center gap-4'); @endphp
        <div class="{{ $buttonsWrapperClasses }}">
            @php $togglePrimaryButton = content('__SLUG__', 'toggle_primary_button', '1'); @endphp
            @php $primaryButtonLabel = content('__SLUG__', 'primary_button', 'Get Started'); @endphp
            @php $primaryButtonClasses = content('__SLUG__', 'primary_button_classes', 'px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
            @if($togglePrimaryButton)
            <a
                href="{{ content('__SLUG__', 'primary_button_url', '#') }}"
                @if(content('__SLUG__', 'primary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $primaryButtonClasses }}"
            >{{ $primaryButtonLabel }}</a>
            @endif
            @if(content('__SLUG__', 'toggle_secondary_button', '1'))
            @php $secondaryButtonLabel = content('__SLUG__', 'secondary_button', 'Learn More'); @endphp
            @php $secondaryButtonClasses = content('__SLUG__', 'secondary_button_classes', 'px-8 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors'); @endphp
            <a
                href="{{ content('__SLUG__', 'secondary_button_url', '#') }}"
                @if(content('__SLUG__', 'secondary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
                class="{{ $secondaryButtonClasses }}"
            >{{ $secondaryButtonLabel }}</a>
            @endif
        </div>
    </div>
</section>
```

### ALL classes must use content()

**Never use hardcoded class strings directly on elements.** Every element's classes must be editable via `content()` with a `*_classes` key. This applies to cards, wrappers, icons, headings, paragraphs, grids, buttons, forms, layout divs — **every single element with a `class` attribute**, no exceptions.

**This is a hard rule. Before finishing any row, scan every line for `class="` and verify each one routes through a `$variable` set by `content()`. If you find a hardcoded `class="..."` that isn't an Alpine `:class` binding or a placeholder-only element (e.g. an image fallback inside `@else`), it must be fixed.**

The only acceptable hardcoded classes are:
- Alpine `:class` dynamic bindings (e.g. `:class="open ? 'rotate-180' : ''"`) — these are runtime expressions, not design values
- Text/icon placeholders shown only inside `@else` when no image/content is set — these are developer fallbacks, not user-facing design

### Conditional (state-variant) classes

When an element has two visual states driven by per-item data (e.g. a featured card vs. a default card), use **paired `content()` fields** — one for each state — and resolve with a ternary at render time.

**Pattern:**
```blade
@php $cardClasses = content('__SLUG__', 'card_classes', 'rounded-card p-8 bg-white border border-zinc-200'); @endphp
@php $cardFeaturedClasses = content('__SLUG__', 'card_featured_classes', 'rounded-card p-8 bg-primary text-white ring-2 ring-primary'); @endphp
```
```blade
@php $isFeatured = !empty($plan['toggle_featured']); @endphp
<div class="{{ $isFeatured ? $cardFeaturedClasses : $cardClasses }}">
```

Name pairs as `{element}_classes` (default) and `{element}_featured_classes` (highlighted state). Apply this to every element inside the loop that has different classes per state.

If the per-item data itself (names, prices, feature lists, etc.) is hardcoded in PHP, convert it to a `grid_*` field so it becomes editable. Store sub-lists (e.g. bullet features per card) as a pipe-separated string (`5 projects|10GB storage`) and split on render with `explode('|', ...)`. Use `toggle_featured` as the key within grid items so the editor infers it as a toggle switch.

See `resources/design-library/rows/pricing/pricing-cards.blade.php` for the reference implementation.

### Standard Group Patterns

**Headline:**
```blade
@php $toggleHeadline = content('__SLUG__', 'toggle_headline', '1'); @endphp
@if($toggleHeadline)
@php $headlineTag = content('__SLUG__', 'headline_htag', 'h2'); @endphp
@php $headlineText = content('__SLUG__', 'headline', 'Your Headline'); @endphp
@php $headlineClasses = content('__SLUG__', 'headline_classes', 'font-heading text-4xl font-bold text-zinc-900 dark:text-white'); @endphp
{!! "<{$headlineTag} class=\"" . e($headlineClasses) . "\">" . e($headlineText) . "</{$headlineTag}>" !!}
@endif
```

**Subheadline:**
```blade
@php $toggleSubheadline = content('__SLUG__', 'toggle_subheadline', '1'); @endphp
@if($toggleSubheadline)
@php $subheadlineText = content('__SLUG__', 'subheadline', 'Your subheadline.'); @endphp
@php $subheadlineClasses = content('__SLUG__', 'subheadline_classes', 'mt-4 text-lg text-zinc-500 dark:text-zinc-400'); @endphp
<p class="{{ $subheadlineClasses }}">{{ $subheadlineText }}</p>
@endif
```

**Primary button:**
```blade
@php $togglePrimaryButton = content('__SLUG__', 'toggle_primary_button', '1'); @endphp
@php $primaryButtonLabel = content('__SLUG__', 'primary_button', 'Get Started'); @endphp
@php $primaryButtonClasses = content('__SLUG__', 'primary_button_classes', 'px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors'); @endphp
@if($togglePrimaryButton)
<a href="{{ content('__SLUG__', 'primary_button_url', '#') }}"
   @if(content('__SLUG__', 'primary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
   class="{{ $primaryButtonClasses }}"
>{{ $primaryButtonLabel }}</a>
@endif
```

**Secondary button:**
```blade
@if(content('__SLUG__', 'toggle_secondary_button', '1'))
@php $secondaryButtonLabel = content('__SLUG__', 'secondary_button', 'Learn More'); @endphp
@php $secondaryButtonClasses = content('__SLUG__', 'secondary_button_classes', 'px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 transition-colors'); @endphp
<a href="{{ content('__SLUG__', 'secondary_button_url', '#') }}"
   @if(content('__SLUG__', 'secondary_button_new_tab', '')) target="_blank" rel="noopener noreferrer" @endif
   class="{{ $secondaryButtonClasses }}"
>{{ $secondaryButtonLabel }}</a>
@endif
```

**Media (image with toggle):**
```blade
@php $imageWrapperClasses = content('__SLUG__', 'image_wrapper_classes', 'rounded-card overflow-hidden aspect-video'); @endphp
@php $imageClasses = content('__SLUG__', 'image_classes', 'w-full h-full object-cover'); @endphp
@if(content('__SLUG__', 'toggle_image', '1'))
<div class="{{ $imageWrapperClasses }}">
    @php $heroImage = content('__SLUG__', 'image', ''); @endphp
    @if($heroImage)
        <img src="{{ $heroImage }}" alt="{{ content('__SLUG__', 'image_alt', '') }}" class="{{ $imageClasses }}">
    @else
        <span class="text-zinc-400 dark:text-zinc-500 text-sm">Image Placeholder</span>
    @endif
</div>
@endif
```

The editor auto-promotes a `toggle_X` field to the group header switch when every other field in the group has a key containing `X` or ending with `_new_tab`.

### Grid rows

- Use the `grid_` prefix for the grid field key: `grid_features`, `grid_items`, etc.
- The inline `content()` default must be a valid single-line JSON array (double quotes only)
- Item keys are inferred from the keys of the first item; new items use those same keys

```php
@php $toggleFeatures = content('__SLUG__', 'toggle_features', '1'); @endphp
@php
    $featuresJson = content('__SLUG__', 'grid_features', '[{"icon":"bolt","title":"Fast","desc":"Speed."}]');
    $features = json_decode($featuresJson, true) ?: [];
@endphp
@php $featuresGridClasses = content('__SLUG__', 'features_grid_classes', 'grid md:grid-cols-3 gap-8'); @endphp
@php $featureCardClasses = content('__SLUG__', 'feature_card_classes', 'p-6 rounded-card border border-zinc-200 dark:border-zinc-700'); @endphp
@if($toggleFeatures)
<div class="{{ $featuresGridClasses }}">
    @foreach ($features as $feature)
        <div class="{{ $featureCardClasses }}">
            {{-- ... render $feature fields ... --}}
        </div>
    @endforeach
</div>
@endif
```

### Heroicons in grid rows

Icons stored as `"bolt"` (outline) or `"bolt:solid"` (solid). Always parse with:

```php
[$iconName, $iconVariant] = array_pad(explode(':', $item['icon'] ?? 'bolt', 2), 2, 'outline');
```

Render with `<x-heroicon name="{{ $iconName }}" variant="{{ $iconVariant }}" class="size-8" />`.

`<x-heroicon>` is **public side only** — it is NOT available via `<flux:icon>` on the public layout (Flux only works on the dashboard side). Use `<x-heroicon>` in all design library row files.

### Adding a new row to the design library

1. Create a new `.blade.php` file in the appropriate category folder
2. Add the metadata comment with `@name`, `@description`, and `@sort`
3. Use `content('__SLUG__', 'key', 'default')` with keys that follow the naming conventions above
4. Run `php artisan design-library:index` to register it in the database

No manual DB insert is required — the index command handles it.

### Other notes

- Template files only affect newly inserted rows. Existing page blade files have row code copied inline at insert time — update them separately if needed.
- Hoist any value used inside an `@if` or HTML attribute with `@php $var = content(...)` to control editor sidebar order.

## Key Lessons

### Vite HMR Reloads on Runtime-Written Files

`refresh: true` watches `config/**` by default. Runtime writes to config files trigger full page reload, wiping Alpine state. Fix: add to `watch.ignored` in `vite.config.js`. Already ignored: `config/navigation.php`, `routes/web.php`, `resources/views/pages/⚡*.blade.php`.

### flux:button `:class` vs `x-bind:class`

Flux components process `:class` as a PHP prop (evaluated server-side). Use `x-bind:class` to pass Alpine expressions through to the DOM.

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

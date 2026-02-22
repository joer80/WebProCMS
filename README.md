# GetRows

## Brand & Styling

| What | Where |
|---|---|
| Primary brand colors & font | `resources/css/app.css` — `@theme` block |
| Business name | `APP_NAME` in `.env` |
| Business contact info (phone, email, address, hours) | `.env` → `BUSINESS_PHONE`, `BUSINESS_EMAIL`, etc. (config: `config/business.php`) |

## View Structure

| Location | Purpose | Route type |
|---|---|---|
| `resources/views/pages/` | Livewire full-page components | `Route::livewire()` |
| `resources/views/` (root) | Static Blade views — top-level pages | `Route::view()` |
| `resources/views/{section}/` | Static Blade views — detail pages under a section | `Route::view()` |

Livewire page components live in `resources/views/pages/` and are registered using `Route::livewire()` with the `pages::` view namespace (e.g. `pages::contact`). Plain Blade views live at the root of `resources/views/` and use `Route::view()`.

### Detail pages

Detail pages (e.g. a deep-dive page for a single service) live in a subdirectory named after their parent section and use a `Route::view()` with a dotted view path:

```
resources/views/services/instant-query-editor.blade.php
```

```php
// routes/web.php
Route::view('services/instant-query-editor', 'services.instant-query-editor')
    ->name('services.instant-query-editor');
```

The route name mirrors the view path using dot notation (`services.instant-query-editor`), which makes it easy to identify the file from the route name and vice versa. Each detail page uses the `<x-layouts::public>` layout component directly, the same as any other static page.

When a section index page (e.g. `services.blade.php`) links to a detail page, the link is conditional — the service data carries a `'route'` key that is either a named route string or `null`. This keeps the index page template clean and makes it trivial to add or remove detail pages later without touching the loop structure.

## Adding Environment Variables

1. Add the var to `.env`: `MY_VAR=value`
2. Add it to `.env.example`: `MY_VAR=`
3. Add it to an existing config file (e.g. `config/business.php`) or create a new `config/my-file.php` that reads it with `env('MY_VAR')`
4. Use it in code via `config('my-file.key')` — never call `env()` directly outside of config files, as it returns `null` when config is cached in production

## Components vs Partials

| Folder | Used with | Use when |
|---|---|---|
| `resources/views/components/` | `<x-component-name />` | The snippet is reusable, accepts props/slots, or wraps other content |
| `resources/views/partials/` | `@include('partials.name')` | The snippet is a fixed layout fragment with no props (e.g. `<head>`) |

**Use `components/`** for anything that behaves like a UI element: it accepts `@props`, can receive slots, and may appear in multiple places (e.g. `app-logo`, `auth-header`, `action-message`).

**Use `partials/`** for static `@include` fragments that belong to a specific layout and don't need to be configurable (e.g. `head`, `settings-heading`).

## Layouts

| Layout | Used for |
|---|---|
| `resources/views/layouts/public.blade.php` | Public-facing pages (nav + footer) |
| `resources/views/layouts/app.blade.php` | Authenticated app pages |
| `resources/views/layouts/auth.blade.php` | Auth pages (login, register, etc.) |

## Claude AI Instructions (`CLAUDE.md`)

`CLAUDE.md` contains project-specific instructions for Claude Code (the AI coding assistant). It is automatically loaded at the start of every Claude session, so the AI always follows the project's conventions without needing to be reminded.

### What it covers

| Section | Purpose |
|---|---|
| **Stack & versions** | Pinned versions of Laravel, Livewire, Flux UI, Pest, Tailwind, etc. |
| **Skills** | Domain-specific agent skills that auto-activate (e.g. `livewire-development`, `tailwindcss-development`) |
| **Conventions** | Naming, structure, code style, and what not to do (no inline validation, no `DB::`, etc.) |
| **PHP rules** | Constructor promotion, explicit return types, PHPDoc, enum casing |
| **Testing** | Every change must be tested; use Pest feature tests; run with `php artisan test --compact` |
| **Formatting** | Run `vendor/bin/pint --dirty` after any PHP change |
| **Laravel Boost** | MCP tools available: `search-docs`, `tinker`, `database-query`, `browser-logs`, `get-absolute-url` |

When contributing or working with Claude Code on this project, do not edit `CLAUDE.md` without understanding the impact — it directly shapes how the AI writes code across every session.

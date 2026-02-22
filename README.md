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
| `resources/views/` (root) | Static Blade views | `Route::view()` |

Livewire page components live in `resources/views/pages/` and are registered using `Route::livewire()` with the `pages::` view namespace (e.g. `pages::contact`). Plain Blade views live at the root of `resources/views/` and use `Route::view()`.

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

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

## Layouts

| Layout | Used for |
|---|---|
| `resources/views/layouts/public.blade.php` | Public-facing pages (nav + footer) |
| `resources/views/layouts/app.blade.php` | Authenticated app pages |
| `resources/views/layouts/auth.blade.php` | Auth pages (login, register, etc.) |

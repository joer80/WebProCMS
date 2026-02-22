# GetRows

## Setting Up a New Project

### Step 1 — Install Laravel Herd

[Laravel Herd](https://herd.laravel.com) is the recommended local development environment.

New Laravel applications use **SQLite** by default. If you need MySQL, PostgreSQL, or Redis, use [DBngin](https://dbngin.com) to manage those services locally, or upgrade to Herd Pro.

Make sure the **latest version of Node.js** is installed before continuing.

### Step 2 — Create a New Site in Herd

When creating the site, select the **Volt** and **Boost** options.

The Livewire starter kit comes with Livewire, Tailwind CSS, and the Flux UI component library pre-configured.

Name the project in kebab-case to match the desired local domain — e.g. a project named `GetRows` will be accessible at `https://getrows.test`.

### Step 3 — Open the Project in VS Code

Install the **[Claude Code extension](https://marketplace.visualstudio.com/items?itemName=anthropic.claude-code)** for VS Code if you haven't already. It provides inline AI assistance powered by Claude.

### Step 4 — Install Laravel Boost

Run the following Artisan command to install the Boost guidelines and skills:

```bash
php artisan boost:install --guidelines --skills
```

When prompted, select:

- **Laravel Fortify** — headless authentication backend
- **Claude** — AI provider integration
- **Gemini** — additional AI provider

This installs the `CLAUDE.md` guidelines file and domain-specific skills that Claude Code uses to follow project conventions automatically.

---

## Common Commands

### Development

| Command | Description |
|---|---|
| `composer run dev` | Start the dev server (Vite + queue + logs) |
| `npm run dev` | Start Vite asset watcher only |
| `npm run build` | Build assets for production |
| `php artisan tinker` | Interactive PHP REPL with app context |
| `php artisan pail` | Tail application logs in the terminal |

### Database

| Command | Description |
|---|---|
| `php artisan migrate` | Run pending migrations |
| `php artisan migrate:fresh --seed` | Drop all tables, re-migrate, and seed |
| `php artisan db:seed` | Run database seeders |
| `php artisan migrate:rollback` | Roll back the last batch of migrations |

### Code Generation

| Command | Description |
|---|---|
| `php artisan make:model Foo -mfs` | Model + migration + factory + seeder |
| `php artisan make:migration create_foo_table` | Standalone migration |
| `php artisan make:request StoreFooRequest` | Form request class |
| `php artisan make:class App/Services/FooService` | Generic PHP class |
| `php artisan make:test FooTest --pest` | Pest feature test |
| `php artisan make:test FooTest --pest --unit` | Pest unit test |
| `php artisan make:command FooCommand` | Console command |
| `php artisan route:list` | List all registered routes |

### Livewire

| Command | Description |
|---|---|
| `php artisan make:livewire FooComponent` | New Livewire component (class + view) |
| `php artisan make:livewire foo.bar` | Nested component in `foo/` subdirectory |
| `php artisan livewire:publish --config` | Publish Livewire config file |

### Testing & Code Quality

| Command | Description |
|---|---|
| `php artisan test --compact` | Run all tests (compact output) |
| `php artisan test --compact --filter=FooTest` | Run a specific test or group |
| `vendor/bin/pint --dirty` | Fix code style on changed files only |
| `vendor/bin/pint` | Fix code style across all PHP files |

### Cache & Config

| Command | Description |
|---|---|
| `php artisan config:clear` | Clear the config cache |
| `php artisan cache:clear` | Clear the application cache |
| `php artisan route:clear` | Clear the route cache |
| `php artisan view:clear` | Clear the compiled view cache |
| `php artisan optimize:clear` | Clear all caches at once |

---

## Working with the SQLite Database

By default, Laravel uses SQLite and stores the database at:

```
database/database.sqlite
```

### Viewing the Database

**Option 1 — TablePlus (recommended)**
[TablePlus](https://tableplus.com) is a native Mac GUI for databases. To connect:
1. Open TablePlus → New Connection → SQLite
2. Set the database path to the absolute path of `database/database.sqlite` in your project
3. Click Connect

**Option 2 — DB Browser for SQLite**
[DB Browser for SQLite](https://sqlitebrowser.org) is a free, open-source GUI. Open the app and use **File → Open Database** to select `database/database.sqlite`.

**Option 3 — VS Code Extension**
Install the [SQLite Viewer](https://marketplace.visualstudio.com/items?itemName=qwtel.sqlite-viewer) extension, then click any `.sqlite` file in the VS Code explorer to browse it inline.

**Option 4 — Command Line**
```bash
sqlite3 database/database.sqlite
```
Common SQLite shell commands:
```sql
.tables              -- list all tables
.schema users        -- show table structure
SELECT * FROM users LIMIT 10;
.quit
```

**Option 5 — Laravel Tinker**
```bash
php artisan tinker
```
```php
User::all();
User::where('email', 'test@example.com')->first();
DB::table('users')->count();
```

### Switching to MySQL or PostgreSQL

1. Install and start the database service via [DBngin](https://dbngin.com) or Herd Pro
2. Update `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=getrows
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Run `php artisan migrate:fresh --seed` to build the schema in the new database

---

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

## Flux UI Components

This project uses [Flux UI Free](https://fluxui.dev) — the official Livewire component library. Below is a reference of what's included in the free edition vs what requires a Pro license.

### Free Components (`livewire/flux`)

| Component | Description |
|---|---|
| `avatar` | User avatar display |
| `badge` | Status/label badges |
| `brand` | Brand/logo display |
| `breadcrumbs` | Navigation breadcrumbs |
| `button` | Buttons with variants |
| `callout` | Highlighted callout blocks |
| `card` | Content card container |
| `checkbox` | Checkbox input |
| `dropdown` | Dropdown menus |
| `field` | Form field wrapper (label + input + error) |
| `heading` | Section headings |
| `icon` | Heroicon icons |
| `input` | Text input |
| `modal` | Dialog modals |
| `navbar` | Navigation bar |
| `otp-input` | One-time password input |
| `pagination` | Page navigation |
| `profile` | User profile display |
| `radio` | Radio button input |
| `select` | Select/dropdown input |
| `separator` | Visual divider |
| `skeleton` | Loading skeleton placeholder |
| `switch` | Toggle switch |
| `table` | Data table |
| `text` | Body text |
| `textarea` | Multi-line text input |
| `tooltip` | Hover tooltip |

### Pro-Only Components (`livewire/flux-pro`)

These components are not available in this project without upgrading to Flux Pro.

| Component | Description |
|---|---|
| `accordion` | Collapsible accordion sections |
| `autocomplete` | Searchable autocomplete input |
| `calendar` | Full calendar display |
| `chart` | Data charts |
| `command` | Command palette |
| `composer` | Rich content composer |
| `context` | Right-click context menu |
| `date-picker` | Date picker input |
| `editor` | Rich text editor |
| `file-upload` | File upload input |
| `kanban` | Kanban board |
| `pillbox` | Multi-value pill input |
| `popover` | Popover/floating panel |
| `slider` | Range slider input |
| `tabs` | Tabbed navigation |
| `time-picker` | Time picker input |

> **Note:** Notable Pro-only surprises: `tabs`, `accordion`, and `popover` all require Pro despite being common UI primitives.

## Claude AI Instructions (`CLAUDE.md`)

`CLAUDE.md` contains project-specific instructions for Claude Code (the AI coding assistant). It is automatically loaded at the start of every Claude session, so the AI always follows the project's conventions without needing to be reminded.

### What it covers

| Section | Purpose |
|---|---|
| **Stack & versions** | Pinned versions of Laravel, Livewire, Flux UI, Pest, Tailwind, etc. |
| **Skills** | Domain-specific agent skills that auto-activate (see table below) |
| **Conventions** | Naming, structure, code style, and what not to do (no inline validation, no `DB::`, etc.) |
| **PHP rules** | Constructor promotion, explicit return types, PHPDoc, enum casing |
| **Testing** | Every change must be tested; use Pest feature tests; run with `php artisan test --compact` |
| **Formatting** | Run `vendor/bin/pint --dirty` after any PHP change |
| **Laravel Boost** | MCP tools available: `search-docs`, `tinker`, `database-query`, `browser-logs`, `get-absolute-url` |

### Skills

| Skill | Activates when... |
|---|---|
| `fluxui-development` | Creating buttons, forms, modals, inputs, dropdowns, checkboxes, or any UI components; working with `flux:` components; replacing HTML with Flux |
| `livewire-development` | Creating or modifying Livewire components; working with `wire:model`, `wire:click`, `wire:loading`, or other `wire:` directives; adding real-time updates or reactivity |
| `pest-testing` | Writing tests, creating unit or feature tests, adding assertions, testing Livewire components, debugging test failures, or working with datasets/mocking |
| `tailwindcss-development` | Adding or changing styles; working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders |
| `developing-with-fortify` | Implementing authentication features: login, registration, password reset, email verification, 2FA/TOTP, profile updates, or auth guards |

When contributing or working with Claude Code on this project, do not edit `CLAUDE.md` without understanding the impact — it directly shapes how the AI writes code across every session.

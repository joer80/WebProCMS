# WebProCMS

[![License: Elastic-2.0](https://img.shields.io/badge/License-Elastic%202.0-blue.svg)](LICENSE)

## Setting Up the Project Locally

### Step 1 — Install Laravel Herd

[Laravel Herd](https://herd.laravel.com) is the recommended local development environment.

This project uses **SQLite** by default. If you need MySQL, PostgreSQL, or Redis, use [DBngin](https://dbngin.com) to manage those services locally, or upgrade to Herd Pro.

Make sure the **latest version of Node.js** is installed before continuing.

### Step 2 — Clone the Repository

Use **[GitHub Desktop](https://desktop.github.com)** to clone the `WebProCMS` repository into your Herd sites folder (e.g. `~/Herd/`). Herd will automatically detect it and make it available at `https://webprocms.test`.

### Step 3 — Open the Project in VS Code

Install the **[Claude Code extension](https://marketplace.visualstudio.com/items?itemName=anthropic.claude-code)** for VS Code if you haven't already. It provides inline AI assistance powered by Claude.

### Step 4 — Install Dependencies & Set Up the Environment

Run the following commands from the project root:

```bash
# Install PHP dependencies
composer install

# Copy the environment file and generate an app key
cp .env.example .env
php artisan key:generate

# Create the SQLite database file (required before first migrate)
touch database/database.sqlite

# Run database migrations
php artisan migrate

# Optionally seed the database with sample data
php artisan db:seed

# Install Node dependencies and build assets for the first time
npm install
npm run build

# Link the storage directory
php artisan storage:link
```

### Default Login Credentials

After running `php artisan db:seed`, two accounts are available:

| Account | Email | Password | Role |
|---|---|---|---|
| Admin | Set via `BUSINESS_ADMIN_EMAIL` in `.env` (defaults to `root@localhost`) | `Admin` | Super |
| Test User | `test@{your-domain}` (e.g. `test@webprocms.test`) | `password` | Standard |

Both accounts require a password change on first login.

---

### Step 5 — Start the Development Environment

```bash
composer run dev
```

This single command starts everything you need for development:

| Process | What it does |
|---|---|
| `php artisan serve` | PHP dev server (Herd already handles this — it runs but is redundant) |
| `php artisan queue:listen` | **Optional** — only needed if you have custom queued jobs; CMS Update and Seed Demo Data run as detached background processes and do not require a queue worker |
| `php artisan pail` | Streams application logs to the terminal |
| `npm run dev` | Vite asset watcher with hot-reload |

> `composer run dev` starts all four processes together including the queue listener. If you don't need the queue, you can run the other processes individually instead.

> **Laravel Boost (`php artisan boost:install --guidelines --skills`):** This command is only needed when setting up a brand-new project. Since this repository already includes the `CLAUDE.md` guidelines and skills files, you do **not** need to run it when cloning.

---

## Production Setup

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
npm run build
php artisan optimize
```

> **After updating `.env`, routes, or config files in production**, clear and rebuild the cache:
> ```bash
> php artisan optimize:clear
> php artisan optimize
> ```

### Queue Worker (Optional)

A queue worker is **not required** for core CMS functionality. The CMS Update and Seed Demo Data features run as detached background processes (`exec`) and work without a queue worker.

If you have custom queued jobs in your application, Forge does **not** start a queue worker automatically. To add one:

1. In Forge, go to your **site → Processes** tab
2. Click **New Background Process**
3. Select the **Queue Worker** tab (use **Custom** instead if running Laravel Horizon)
4. Set **Connection** to `database` and fill in your queue name (leave blank for default)
5. Click **Create** — Forge will manage it with Supervisor and keep it running

Also confirm your production `.env` has `QUEUE_CONNECTION=database` (or whichever driver you're using).

### CMS Updates

Updates can be triggered directly from **Dashboard → Tools → CMS Update**. The update card shows the current installed version, checks for available updates against a configurable releases API, and lets you apply them with a single click.

There are no auto-deploy webhooks — updates only happen when the site owner clicks **Update Now**. This gives clients full control over when updates are applied to their live site, preventing unexpected changes during business hours.

#### How it works

1. Click **Check for Updates** — makes an HTTP request to `CMS_RELEASES_API_URL` and compares the returned version against the local `VERSION` file
2. If a newer version is available, **Update Now** appears
3. Clicking it runs `UpdateCmsJob` as a detached background process (no queue worker required), which:
   - `git fetch origin <CMS_GIT_BRANCH>` + `git merge --ff-only` (safe — fails cleanly if histories have diverged)
   - `composer install --no-dev --optimize-autoloader`
   - `php artisan migrate --force`
   - `npm run build`
   - `php artisan optimize` (config, route, view, and event cache — production only)
   - `php artisan responsecache:clear` (production only)
4. The card polls every 3 seconds while the job runs and shows the full command log on completion or failure

#### Configuration

Add these to your production `.env`:

```env
CMS_RELEASES_API_URL=https://api.github.com/repos/your-org/webprocms/releases/latest
CMS_GIT_BRANCH=main
```

The releases API must return JSON with a `version` key (e.g. `"1.0.1"`) and optional `notes` key. The GitHub Releases API format is also supported — the `v` prefix on `tag_name` is stripped automatically.

> The update runs as a detached background process — no queue worker required. Node.js and npm must be installed on the server for the `npm run build` step.

#### Releasing a new version

To make an update available to clients:

1. Push your fixes to `main`
2. Update the `VERSION` file in the repo to the new version (e.g. `1.0.1`) and commit + push
3. Go to your GitHub repo → **Releases** → **Draft a new release**
4. Click **Choose a tag** → type `v1.0.1` (matching the version you just set) → **Create new tag**
5. Add a title and release notes
6. Click **Publish release**

The client's **Check for Updates** button hits the GitHub releases API, strips the `v` prefix from the tag, and compares it against the version on their server. If the tag is higher, **Update Now** appears.

> **Important:** Always update the `VERSION` file in the repo to match the release tag before pushing and tagging. New installs clone whatever is in the repo — if `VERSION` still says `1.0.0` but your latest release is `v1.0.5`, a fresh install will immediately show an update available and prompt the client to run through the full update pipeline on a site they just set up.

#### If the update fails due to a merge conflict

The update uses `git merge --ff-only`, which refuses to proceed if your local branch has diverged from the upstream (e.g. you edited a CMS core file directly on the server). The site is left untouched — no broken files.

The error log in the dashboard will show the exact commands to run. SSH into the server and choose one:

```bash
cd /path/to/site

# Option A — merge and resolve conflicts manually
git merge origin/main

# Option B — discard local changes and force to upstream (destructive)
git reset --hard origin/main
```

After resolving, click **Update Now** again.

To avoid this situation: don't edit CMS core files directly on the server. Make changes locally, commit to the client's fork, then update.

---

### CSS Builds & the Page Editor

#### Editor preview (JIT)

The editor preview uses the **Tailwind Play CDN** (`@tailwindcss/browser@4`) instead of the compiled `public.css`. This means any Tailwind class you type into a classes field appears instantly in the preview — including classes that have never been used on the site before. No build step is required to see styling changes while editing.

The CDN is injected only into the editor preview iframe; the live public site always uses the compiled bundle.

#### Live site (compiled CSS on save)

When you save a page, the system automatically:

1. Writes the new class value back into the relevant blade file so Tailwind's scanner can detect it
2. Dispatches a `RebuildAssets` job that runs `npm run build:public`

`npm run build:public` only recompiles the public CSS/JS bundle (skipping the dashboard and editor bundles), making it significantly faster than a full `npm run build`.

**This requires Node.js and npm to be installed on the production server.** Without them, the `RebuildAssets` job will fail silently and the new CSS classes won't appear until a manual deploy.

To verify Node.js is available on your Forge server:
```bash
node -v
npm -v
```

If not installed, add it via your server's provisioning or run:
```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt-get install -y nodejs
```

**Locally**, `REBUILD_ASSETS_LOCALLY` defaults to `true` so the on-save rebuild works out of the box — **no `composer run dev` or queue worker required**. The `RebuildAssets` job runs after the save response is sent (non-blocking via `defer()`), so the save feels instant and the build (~1s) happens in the background.

You can disable this in **Dashboard → Advanced Settings → Asset Rebuilding** if you prefer to use `composer run dev` instead.

The job auto-detects npm for **nvm** and **Laravel Herd** installs — no extra configuration needed. If auto-detection ever fails, you can override by setting `NPM_PATH` in `.env` to the full path returned by `which npm`.

---

### Deploying with Laravel Forge

Forge's default deployment script does **not** include a response cache clear step. Since this project uses `spatie/laravel-responsecache` to cache full HTML pages, a new deployment will produce new Vite asset fingerprints (e.g. `app-CClrwE2e.css`) while the cached HTML responses still reference the old filenames — causing 404 errors for CSS and JS.

Add `php artisan responsecache:clear` to your Forge deploy script **before** `$ACTIVATE_RELEASE()`:

```bash
$CREATE_RELEASE()

cd $FORGE_RELEASE_DIRECTORY

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader
$FORGE_PHP artisan optimize
$FORGE_PHP artisan storage:link
$FORGE_PHP artisan migrate --force

npm ci || npm install
npm run build

$FORGE_PHP artisan responsecache:clear

$ACTIVATE_RELEASE()

$RESTART_QUEUES()
```

Without this step, visitors will see broken styles after every deployment until their cached page response expires.

### Deploying with RunCloud

RunCloud supports deploying directly from a GitHub repository. Since WebProCMS is a public repo, no deploy key is required — leave the field blank.

#### Initial setup

1. In RunCloud, create a new **Web Application** and go to **Git Repository**
2. Enter the repository URL: `https://github.com/joer80/WebProCMS.git`
3. Branch: `main`
4. Leave the **Deploy Key** field blank (not needed for public repos)
5. Set the **Public Path** to `/public`
6. Hit **Deploy** — RunCloud will download the repository files
7. Go to your web app → **Deployment** and paste the following into the deployment script box:

```bash
# Install PHP dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Build frontend assets
npm ci || npm install
npm run build
```

8. Hit **Deploy** again — this installs dependencies and builds the frontend assets
9. Visit your domain — the installer will appear and walk you through the rest

The installer handles: SSL check, `.env` configuration, database setup, migrations, and redirecting you to the dashboard.

#### Queue worker (optional)

A queue worker is **not required** for core CMS functionality. If you have custom queued jobs, add one via **web app → Supervisor**:

- **Command:** `php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --timeout=300`
- **Auto-restart:** enabled

### Production Nginx Configuration

Add the following block inside your site's `server {}` block, **above** the PHP catch-all. This caches Vite-built assets in the browser for 6 months — safe because Vite generates content-hashed filenames that change whenever the file changes.

```nginx
# Long-lived cache for Vite-built assets (content-hashed filenames)
location ~* ^/build/ {
    expires 6M;
    add_header Cache-Control "public, max-age=15552000, immutable";
    access_log off;
}
```

---

## Common Commands

### Development

| Command | Description |
|---|---|
| `composer run dev` | Start the dev server (Vite + optional queue listener + logs) |
| `npm run dev` | Start Vite asset watcher only |
| `npm run build` | Build all asset bundles (app, public, editor) |
| `npm run build:public` | Build the public bundle only — faster; used by the page editor on save |
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
| `php artisan make:view foo` | Static Blade view (e.g. `about.blade.php`) — use with `Route::view()` |
| `php artisan make:view foo.bar` | Nested static Blade view (e.g. `services/instant-query-editor.blade.php`) |
| `php artisan make:class App/Services/FooService` | Generic PHP class |
| `php artisan make:test FooTest --pest` | Pest feature test |
| `php artisan make:test FooTest --pest --unit` | Pest unit test |
| `php artisan make:command FooCommand` | Console command |
| `php artisan route:list` | List all registered routes |

### Livewire

| Command | Description |
|---|---|
| `php artisan make:livewire FooComponent` | **Preferred** — creates a Volt single-file component by default in this project (configured in `vendor/livewire/livewire/config/livewire.php`) |
| `php artisan make:livewire --sfc --emoji=true FooComponent` | Explicit Volt single-file component — use this if working in a project where SFC is not the default |
| `php artisan make:livewire --mfc --emoji=true FooComponent` | Two-file component — separate PHP class (`app/Livewire/FooComponent.php`) + blade view (`resources/views/livewire/foo-component.blade.php`) — great for complicated components with lots of logic |
| `php artisan make:livewire --class FooComponent` | Class-only component — template lives inside `render()` as a heredoc string (avoid: no blade syntax highlighting) |
| `php artisan make:livewire pages.foo FooComponent` | Nested component in a subdirectory (works with any flag above) |
| `php artisan livewire:publish --config` | Publish Livewire config file |

**Which format to use:**

- **Volt (`--sfc`)** — the default choice for this project. Everything in one `.blade.php` file with full blade syntax highlighting. All existing page components use this format.
- **Two-file (`--mfc`)** — worth considering when a component has complex PHP logic that benefits from a dedicated class file (better IDE support for refactoring, interfaces, traits). The blade view stays a proper `.blade.php` file with full highlighting.
- **Class-only (`--class`)** — avoid. The template is a PHP string inside `render()`, so there is no blade syntax highlighting or tag completion in VS Code. See `app/Livewire/Blog2.php` for a reference example of what this looks like.

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
   DB_DATABASE=webprocms
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Run `php artisan migrate:fresh --seed` to build the schema in the new database

---

## Website Type (Starting Point)

The public site's navigation, homepage content, and footer links are controlled by a single environment variable:

```env
WEBSITE_TYPE=saas
```

Set this in `.env` before handing the project off to a client. After changing it, run `php artisan config:clear` (or `php artisan optimize:clear` in production).

### Available types

| Value | Navigation |
|---|---|
| `saas` | Features, Pricing, Blog, About + Login/Register or Dashboard |
| `service` | Services, Locations, Blog, Contact Us |
| `ecommerce` | Products, About Us, Contact Us + Login/Register or Dashboard |
| `law` | Practice Areas, About Us, Contact Us |
| `nonprofit` | About, Blog, Donate, Volunteer |
| `healthcare` | Patients, Employers, Locations, Careers |
| `custom` | About, Blog, Contact |

Each type also controls the homepage hero headline, subheadline, and CTA buttons, as well as the footer Company link column.

### How it works

- `WEBSITE_TYPE` is read by `config/features.php` via `env('WEBSITE_TYPE', 'saas')`
- `config/navigation.php` maps each type to its nav items and footer links
- `resources/views/layouts/public.blade.php` loops over the config to render the header and footer
- `resources/views/home.blade.php` uses a `match()` to render the correct hero and intro content per type

All page routes (features, pricing, products, practice-areas, donate, volunteer, patients, employers, careers, locations, blog, contact, about, services) are always registered. The type setting determines which ones appear in the navigation.

---

## Brand & Styling

| What | Where |
|---|---|
| Primary brand colors & font | `resources/css/app.css` — `@theme` block |
| Business name | `APP_NAME` in `.env` |
| Business contact info (phone, email, address, hours) | `.env` → `BUSINESS_PHONE`, `BUSINESS_EMAIL`, etc. (config: `config/business.php`) |

## Route Files

Two route files — never cross-contaminate them:

| File | Purpose |
|---|---|
| `routes/web.php` | **Client zone** — public-facing page routes only. Written at runtime when pages are created/cloned. Will differ between client installs. |
| `routes/cms.php` | **CMS core** — all dashboard, settings, and design-editor routes. Never modified per-client. Delivered cleanly via `git pull`. |

**Rule:** Any route you add for a dashboard feature, admin tool, or CMS-level functionality goes in `routes/cms.php`. Public site pages (contact, about, services, etc.) go in `routes/web.php`. This separation is what allows clients to receive CMS updates without merge conflicts.

`routes/settings.php` (user profile, password, 2FA) is required from `routes/cms.php` and is also CMS core.

---

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
resources/views/services/content-editor.blade.php
```

```php
// routes/web.php
Route::view('services/content-editor', 'services.content-editor')
    ->name('services.content-editor');
```

The route name mirrors the view path using dot notation (`services.content-editor`), which makes it easy to identify the file from the route name and vice versa. Each detail page uses the `<x-layouts::public>` layout component directly, the same as any other static page.

When a section index page (e.g. `services.blade.php`) links to a detail page, the link is conditional — the service data carries a `'route'` key that is either a named route string or `null`. This keeps the index page template clean and makes it trivial to add or remove detail pages later without touching the loop structure.

### When to use Livewire vs Blade

**Use a static Blade view** (`php artisan make:view`) when the page is read-only and the data doesn't change based on user input — marketing pages, about, terms, etc.

**Use a Volt single-file component** (`php artisan make:livewire --sfc --emoji=true`) the moment you need the server to react to something the user does without a full page reload:

| Use case | Examples |
|---|---|
| **Reactive filtering/search** | Search bars, sortable tables, faceted filters (price, category, status) |
| **State-toggling actions** | Like/bookmark/follow buttons, inline editing, voting |
| **Multi-step flows** | Wizards, onboarding, checkout, branching surveys |
| **Real-time / polling** | Order status, notification feeds, live dashboards |
| **Pagination without reload** | Load more, infinite scroll, in-place pagination |
| **File uploads** | Upload with progress, preview, and server-side validation — no JS needed |
| **Complex form state** | Dependent dropdowns, conditional fields, draft autosave |
| **Contact/enquiry forms** | Any form that submits and shows feedback without a page change |

## Adding Environment Variables

1. Add the var to `.env`: `MY_VAR=value`
2. Add it to `.env.example`: `MY_VAR=`
3. Add it to an existing config file (e.g. `config/business.php`) or create a new `config/my-file.php` that reads it with `env('MY_VAR')`
4. Use it in code via `config('my-file.key')` — never call `env()` directly outside of config files, as it returns `null` when config is cached in production

> **Note:** `.env` changes take effect immediately on the next request during local development (no command needed). If the config cache is active (e.g. in production after running `php artisan config:cache`), you must run `php artisan config:cache` again — or `php artisan config:clear` — for changes to be picked up.

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

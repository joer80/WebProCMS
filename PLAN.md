# Plan: Frontend Demo Website Type Switcher

## Context

WebProCMS supports 7 website types (saas, service, ecommerce, law, nonprofit, healthcare, custom), each with distinct navigation, footer, and homepage content. Currently the active type is fixed via `WEBSITE_TYPE` in `.env`. The user wants a demo mode where front-end visitors can rotate between types to see how the site looks for each industry. Client deployments will have this disabled.

## Approach

Session-based runtime config override via middleware, with per-type response cache keying in demo mode. The switcher UI is a floating panel at bottom-right (Alpine.js toggle).

---

## Implementation Steps

### 1. `config/features.php` — add `demo_mode` flag
```php
'demo_mode' => env('DEMO_MODE', false),
```

### 2. `.env` and `.env.example` — add env var
```
# Set to true to show the front-end website type switcher (demo sites only)
DEMO_MODE=true
```
(`.env.example` default: `DEMO_MODE=false`)

### 3. Create `app/Http/Middleware/SetDemoWebsiteType.php`
- Checks `config('features.demo_mode')`
- If true, reads `session('demo_website_type')`
- Validates it against allowed types
- Calls `config(['features.website_type' => $type])` to override at runtime

```php
// Allowed types constant
private const TYPES = ['saas', 'service', 'ecommerce', 'law', 'nonprofit', 'healthcare', 'custom'];

public function handle(Request $request, Closure $next): Response
{
    if (config('features.demo_mode')) {
        $type = session('demo_website_type');
        if ($type && in_array($type, self::TYPES, true)) {
            config(['features.website_type' => $type]);
        }
    }
    return $next($request);
}
```

### 4. Register middleware in `bootstrap/app.php`
Prepend to web group so it runs before `CacheResponse` route middleware:
```php
$middleware->prependToGroup('web', \App\Http\Middleware\SetDemoWebsiteType::class);
```

### 5. Update `app/Http/CacheProfiles/GuestOnlyCacheProfile.php`
Include the active website type in the cache name suffix when demo mode is on, so each type gets its own cached copy:

```php
public function useCacheNameSuffix(Request $request): string
{
    if (config('features.demo_mode')) {
        return 'demo-' . config('features.website_type', 'saas');
    }
    return $request->user() ? 'auth' : '';
}
```

This enables caching-per-type in demo mode (7 cached versions per page) rather than disabling cache entirely.

### 6. Add switch route in `routes/web.php`
Outside the `CacheResponse` group, near the top:
```php
Route::get('demo/switch/{type}', function (string $type): \Illuminate\Http\RedirectResponse {
    $allowed = ['saas', 'service', 'ecommerce', 'law', 'nonprofit', 'healthcare', 'custom'];
    if (config('features.demo_mode') && in_array($type, $allowed, true)) {
        session(['demo_website_type' => $type]);
    }
    return redirect()->route('home');
})->name('demo.switch');
```
Always redirects to `/` (home) — the page most visibly affected by a type change.

### 7. Update `resources/views/layouts/public.blade.php`
Add a floating switcher panel at the **bottom-right** of the page, shown only when `config('features.demo_mode')` is true. Uses Alpine.js for expand/collapse toggle.

Place just before `@stack('scripts')` at the end of `<body>`:

```blade
@if (config('features.demo_mode'))
    @php
        $demoTypes = [
            'saas'       => 'SaaS',
            'service'    => 'Service',
            'ecommerce'  => 'eCommerce',
            'law'        => 'Law',
            'nonprofit'  => 'Nonprofit',
            'healthcare' => 'Healthcare',
            'custom'     => 'Custom',
        ];
        $activeType = config('features.website_type', 'saas');
    @endphp

    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2"
         x-data="{ open: false }">

        {{-- Expanded panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-white dark:bg-[#161615] rounded-lg shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-3 flex flex-col gap-1.5 min-w-36">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#706f6c] dark:text-[#A1A09A] px-1 mb-1">Website Type</p>
            @foreach ($demoTypes as $slug => $label)
                <a href="{{ route('demo.switch', $slug) }}"
                   class="px-3 py-1.5 rounded text-sm transition-colors
                          {{ $slug === $activeType
                              ? 'bg-primary text-primary-foreground font-medium'
                              : 'text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f3] dark:hover:bg-[#1D1D1B]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Toggle button --}}
        <button @click="open = !open"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-full shadow-lg text-sm font-medium hover:bg-primary-hover transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
            Demo
        </button>
    </div>
@endif
```

### 8. Write Pest feature test `tests/Feature/DemoModeSwitcherTest.php`
Test cases:
- When `DEMO_MODE=false`, switcher bar does not appear on homepage
- When `DEMO_MODE=true`, switcher bar appears on homepage
- `GET /demo/switch/law` stores `demo_website_type=law` in session and redirects to `/`
- `GET /demo/switch/invalid` does NOT change session, redirects to `/`
- When session has `demo_website_type=law`, homepage shows law-type hero heading
- When `DEMO_MODE=false`, `/demo/switch/law` does not change the session type

### 9. Run Pint
`vendor/bin/pint --dirty --format agent` on all modified PHP files.

---

## Files Modified
- `config/features.php`
- `.env`
- `.env.example`
- `app/Http/Middleware/SetDemoWebsiteType.php` ← **new**
- `bootstrap/app.php`
- `app/Http/CacheProfiles/GuestOnlyCacheProfile.php`
- `routes/web.php`
- `resources/views/layouts/public.blade.php`
- `tests/Feature/DemoModeSwitcherTest.php` ← **new**

## Key Constraints
- No changes to homepage or other page Volt components — the middleware + config override handles all type-switching transparently
- Response caching is preserved (per-type cache keys in demo mode)
- When `DEMO_MODE=false`, zero changes to behaviour (the middleware no-ops, cache profile unchanged, switcher hidden)
- The switch route always redirects to `/` so visitors land on the type-aware homepage

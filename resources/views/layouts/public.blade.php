@props(['title' => null, 'description' => null, 'noindex' => false, 'ogImage' => null])
@php
    use App\Enums\SnippetPlacement;
    use App\Models\Snippet;

    $navConfig = config('navigation', []);
    $showAuthLinks = $navConfig['show_auth_links'] ?? false;
    $showAccountInFooter = $navConfig['show_account_in_footer'] ?? true;
    $allMenus = collect($navConfig['menus'] ?? []);
    $navMenu = $allMenus->firstWhere('slug', 'main-navigation');
    $navItems = array_filter($navMenu['items'] ?? [], fn ($item) => $item['active'] ?? true);
    $footerSlugs = $navConfig['footer_slugs'] ?? [];
    $footerMenus = collect($footerSlugs)->map(fn ($slug) => $allMenus->firstWhere('slug', $slug))->filter()->values()->all();

    $layoutConfig = config('layout', []);
    $layoutBodyClasses = $layoutConfig['body_classes'] ?? '';
    $layoutPhpTop = $layoutConfig['php_top'] ?? '';
    $layoutActiveHeader = $layoutConfig['active_header'] ?? null;
    $layoutActiveFooter = $layoutConfig['active_footer'] ?? null;

    if ($layoutPhpTop) {
        try {
            eval($layoutPhpTop);
        } catch (\Throwable) {
        }
    }

    $currentPath = trim(request()->path(), '/');
    $pageSnippets = cache()->remember('snippets:'.$currentPath, 300, fn () => Snippet::forPage($currentPath)->get());

    foreach ($pageSnippets->filter(fn ($s) => $s->placement === SnippetPlacement::PhpTop) as $snippet) {
        try {
            eval($snippet->content);
        } catch (\Throwable) {
        }
    }
@endphp
@foreach ($pageSnippets->filter(fn ($s) => $s->placement === SnippetPlacement::Head) as $snippet)
    @push('head'){!! $snippet->content !!}@endpush
@endforeach
@foreach ($pageSnippets->filter(fn ($s) => $s->placement === SnippetPlacement::BodyEnd) as $snippet)
    @push('scripts'){!! $snippet->content !!}@endpush
@endforeach
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['cssBundle' => 'resources/css/public.css', 'noindex' => $noindex, 'ogImage' => $ogImage])
    </head>
    <body class="{{ $layoutBodyClasses ?: 'bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex min-h-screen flex-col antialiased' }}">
        @if($layoutActiveHeader && file_exists(resource_path('views/layouts/partials/header.blade.php')))
            @include('layouts.partials.header')
        @else
            {{-- Built-in fallback header (used when no design library header is active) --}}
            <header class="w-full border-b border-[#e3e3e0] dark:border-[#3E3E3A] text-sm mb-10" x-data="{ open: false }">
                <div class="max-w-6xl mx-auto px-6">
                <nav class="flex items-center justify-between gap-4 h-14">
                    <a href="{{ route('home') }}">
                        <img src="{{ config('branding.logo_url', asset('images/logo.svg')) }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
                    </a>

                    {{-- Desktop nav --}}
                    <div class="hidden sm:flex items-center gap-4">
                        @foreach ($navItems as $item)
                            <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}" @if (!empty($item['new_window'])) target="_blank" rel="noopener noreferrer" @endif class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        @if ($showAuthLinks && Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>

                    {{-- Mobile hamburger button --}}
                    <button @click="open = !open" class="sm:hidden p-2 -mr-2 text-[#1b1b18] dark:text-[#EDEDEC]" aria-label="Toggle menu">
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </nav>

                {{-- Mobile menu --}}
                <div x-show="open" x-transition class="sm:hidden mt-3 flex flex-col border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-3">
                    @foreach ($navItems as $item)
                        <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}" @if (!empty($item['new_window'])) target="_blank" rel="noopener noreferrer" @endif class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">{{ $item['label'] }}</a>
                    @endforeach

                    @if ($showAuthLinks && Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
                </div>
            </header>
        @endif

        <main class="w-full flex-1">
            {{ $slot }}
        </main>

        @if($layoutActiveFooter && file_exists(resource_path('views/layouts/partials/footer.blade.php')))
            @include('layouts.partials.footer')
        @else
            {{-- Built-in fallback footer (used when no design library footer is active) --}}
            <footer class="w-full border-t border-[#e3e3e0] dark:border-[#3E3E3A] mt-16 pt-10 pb-8 px-6">
                <div class="max-w-6xl mx-auto">
                <div class="flex flex-col gap-8 sm:flex-row sm:justify-between">
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('home') }}">
                            <img src="{{ config('branding.logo_url', asset('images/logo.svg')) }}" alt="{{ config('app.name') }}" class="h-7 w-auto" />
                        </a>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] max-w-xs">
                            Build, manage, and publish — without limits.
                        </p>
                    </div>
                    <div class="flex gap-12">
                        @foreach ($footerMenus as $footerMenu)
                            @php $activeFooterItems = array_filter($footerMenu['items'] ?? [], fn ($item) => $item['active'] ?? true); @endphp
                            @if (!empty($activeFooterItems))
                                <div class="flex flex-col gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">{{ $footerMenu['label'] }}</p>
                                    <nav class="flex flex-col gap-2">
                                        @foreach ($activeFooterItems as $item)
                                            <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}" @if (!empty($item['new_window'])) target="_blank" rel="noopener noreferrer" @endif class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">{{ $item['label'] }}</a>
                                        @endforeach
                                    </nav>
                                </div>
                            @endif
                        @endforeach
                        @if ($showAccountInFooter)
                            <div class="flex flex-col gap-3">
                                <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">Account</p>
                                <nav class="flex flex-col gap-2">
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Dashboard</a>
                                    @else
                                        <a href="{{ route('login') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Log in</a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Register</a>
                                        @endif
                                    @endauth
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-10 border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6">
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                </div>
            </footer>
        @endif

        @auth
            @if(auth()->user()->isAtLeast(\App\Enums\Role::Manager))
                @php
                    $routeName = Route::currentRouteName();
                    $pageFile = null;
                    if ($routeName) {
                        // Check flat path first (e.g. pages/⚡about.blade.php)
                        $flatFile = "pages/⚡{$routeName}.blade.php";
                        if (file_exists(resource_path("views/{$flatFile}"))) {
                            $pageFile = $flatFile;
                        } else {
                            // Check subdirectory path (e.g. blog.index → pages/blog/⚡index.blade.php)
                            $parts = explode('.', $routeName);
                            $last = array_pop($parts);
                            if ($parts) {
                                $subFile = 'pages/' . implode('/', $parts) . '/⚡' . $last . '.blade.php';
                                if (file_exists(resource_path("views/{$subFile}"))) {
                                    $pageFile = $subFile;
                                }
                            }
                        }
                    }
                    $editorUrl = $pageFile
                        ? route('dashboard.design-library.editor') . '?file=' . urlencode($pageFile)
                        : null;

                    $editPostUrl = null;
                    if ($routeName === 'blog.show') {
                        $slug = request()->route('slug');
                        if ($slug) {
                            $post = \App\Models\Post::query()->where('slug', $slug)->first();
                            if ($post) {
                                $editPostUrl = route('dashboard.blog.edit', $post);
                            }
                        }
                    }
                @endphp
                @php $isAboveManager = auth()->user()->isAtLeast(\App\Enums\Role::Admin); @endphp
                @if($editPostUrl || ($editorUrl && $isAboveManager))
                    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-2">
                        @if($editPostUrl)
                            <a href="{{ $editPostUrl }}"
                               class="flex items-center gap-2 rounded-full bg-zinc-900 dark:bg-white px-4 py-2.5 text-sm font-semibold text-white dark:text-zinc-900 shadow-lg hover:bg-zinc-700 dark:hover:bg-zinc-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Post
                            </a>
                        @endif
                        @if($editorUrl && $isAboveManager)
                            <a href="{{ $editorUrl }}"
                               class="flex items-center gap-2 rounded-full bg-zinc-900 dark:bg-white px-4 py-2.5 text-sm font-semibold text-white dark:text-zinc-900 shadow-lg hover:bg-zinc-700 dark:hover:bg-zinc-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                Edit Page
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        @endauth
        @stack('scripts')
        @vite('resources/js/public.js')
        <script>
            if (window.self !== window.top) {
                window.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        window.parent.postMessage({ type: 'editor-save-page' }, window.location.origin);
                    }
                });
            }
        </script>
    </body>
</html>

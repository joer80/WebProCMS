@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex p-6 lg:p-8 min-h-screen flex-col antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
        <header class="w-full max-w-4xl mx-auto text-sm mb-10" x-data="{ open: false }">
            <nav class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
                </a>

                {{-- Desktop nav --}}
                <div class="hidden sm:flex items-center gap-4">
                    <a href="{{ route('about') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        About
                    </a>
                    <a href="{{ route('services') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Services
                    </a>
                    <a href="{{ route('locations') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Locations
                    </a>
                    <a href="{{ route('contact') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Contact
                    </a>
                    @if (Route::has('login'))
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
                <a href="{{ route('about') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">About</a>
                <a href="{{ route('services') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Services</a>
                <a href="{{ route('locations') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Locations</a>
                <a href="{{ route('contact') }}" class="px-2 py-2.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Contact</a>
                @if (Route::has('login'))
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
        </header>

        <main class="w-full max-w-4xl mx-auto flex-1">
            {{ $slot }}
        </main>

        <footer class="w-full max-w-4xl mx-auto border-t border-[#e3e3e0] dark:border-[#3E3E3A] mt-16 pt-10 pb-8">
            <div class="flex flex-col gap-8 sm:flex-row sm:justify-between">
                <div class="flex flex-col gap-3">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-7 w-auto" />
                    </a>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] max-w-xs">
                        Query your data in seconds.
                    </p>
                </div>
                <div class="flex gap-12">
                    <div class="flex flex-col gap-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#706f6c] dark:text-[#A1A09A]">Company</p>
                        <nav class="flex flex-col gap-2">
                            <a href="{{ route('about') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">About</a>
                            <a href="{{ route('services') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Services</a>
                            <a href="{{ route('locations') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Locations</a>
                            <a href="{{ route('contact') }}" class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#706f6c] dark:hover:text-[#A1A09A] transition-colors">Contact</a>
                        </nav>
                    </div>
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
                </div>
            </div>
            <div class="mt-10 border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6">
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>

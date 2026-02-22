@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex p-6 lg:p-8 min-h-screen flex-col antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
        <header class="w-full max-w-4xl mx-auto text-sm mb-10">
            <nav class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
                </a>
                <div class="flex items-center gap-4">
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
            </nav>
        </header>

        <main class="w-full max-w-4xl mx-auto flex-1">
            {{ $slot }}
        </main>

        <footer class="w-full max-w-4xl mx-auto border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6 pb-4 mt-16 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </footer>

        @fluxScripts
    </body>
</html>

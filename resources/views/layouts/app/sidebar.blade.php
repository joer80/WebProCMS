<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @if (auth()->user()->isAtLeast(\App\Enums\Role::Manager))
            <link rel="preload" href="{{ Vite::asset('resources/css/editor.css') }}" as="style">
        @endif
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                @if (auth()->user()->isAtLeast(\App\Enums\Role::Manager))
                    <ui-disclosure-group exclusive>
                    <flux:sidebar.group expandable icon="document-duplicate" heading="{{ __('Content') }}" :expanded="true" class="grid">
                        <flux:sidebar.item icon="document" :href="route('dashboard.pages')" :current="request()->routeIs('dashboard.pages')" wire:navigate>
                            {{ __('Pages') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" :href="route('dashboard.blog.index')" :current="request()->routeIs('dashboard.blog.*')" wire:navigate>
                            {{ __('Blog') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="photo" :href="route('dashboard.media-library.index')" :current="request()->routeIs('dashboard.media-library.*')" wire:navigate>
                            {{ __('Media Library') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="map-pin" :href="route('dashboard.locations.index')" :current="request()->routeIs('dashboard.locations.*')" wire:navigate>
                            {{ __('Locations') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                    <flux:sidebar.group expandable icon="wrench-screwdriver" heading="{{ __('Develop') }}" :expanded="request()->routeIs('dashboard.forms.*', 'dashboard.menus', 'dashboard.redirects', 'dashboard.users', 'dashboard.shortcodes.*', 'dashboard.snippets.*', 'dashboard.design-library.*', 'dashboard.tools')" class="grid">
                        <flux:sidebar.item icon="document-check" :href="route('dashboard.forms.index')" :current="request()->routeIs('dashboard.forms.*')" wire:navigate>
                            {{ __('Forms') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="bars-3" :href="route('dashboard.menus')" :current="request()->routeIs('dashboard.menus')" wire:navigate>
                            {{ __('Menus') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="arrow-right-circle" :href="route('dashboard.redirects')" :current="request()->routeIs('dashboard.redirects')" wire:navigate>
                            {{ __('Redirects') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('dashboard.users')" :current="request()->routeIs('dashboard.users')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="code-bracket" :href="route('dashboard.shortcodes.index')" :current="request()->routeIs('dashboard.shortcodes.*')" wire:navigate>
                            {{ __('Shortcodes') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="code-bracket-square" :href="route('dashboard.snippets.index')" :current="request()->routeIs('dashboard.snippets.*')" wire:navigate>
                            {{ __('Snippets') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="squares-2x2" :href="route('dashboard.design-library.index')" :current="request()->routeIs('dashboard.design-library.*')" wire:navigate>
                            {{ __('Design Library') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="wrench-screwdriver" :href="route('dashboard.tools')" :current="request()->routeIs('dashboard.tools')" wire:navigate>
                            {{ __('Tools') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                    <flux:sidebar.group expandable icon="cog-6-tooth" heading="{{ __('Settings') }}" :expanded="request()->routeIs('dashboard.settings.*')" class="grid">
                        <flux:sidebar.item :href="route('dashboard.settings.general')" :current="request()->routeIs('dashboard.settings.general')" wire:navigate>
                            {{ __('General') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('dashboard.settings.branding')" :current="request()->routeIs('dashboard.settings.branding')" wire:navigate>
                            {{ __('Branding') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('dashboard.settings.design')" :current="request()->routeIs('dashboard.settings.design')" wire:navigate>
                            {{ __('Design') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('dashboard.settings.advanced')" :current="request()->routeIs('dashboard.settings.advanced')" wire:navigate>
                            {{ __('Advanced') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                    </ui-disclosure-group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="globe-alt" :href="route('home')" wire:navigate>
                    {{ __('View Website') }}
                </flux:sidebar.item>

            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        {{-- Toast notifications --}}
        <div
            wire:ignore
            x-data="{ show: false, message: '', _timer: null }"
            @notify.window="message = $event.detail.message; show = true; clearTimeout(_timer); _timer = setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="fixed bottom-4 right-4 z-50 flex items-center gap-2.5 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100 text-sm font-medium px-4 py-2.5 rounded-lg shadow-lg ring-1 ring-zinc-900/10 dark:ring-zinc-700"
            x-cloak
        >
            <flux:icon name="check-circle" variant="mini" class="text-green-500 dark:text-green-400 shrink-0" />
            <span x-text="message"></span>
        </div>

@stack('scripts')
        @fluxScripts
    </body>
</html>

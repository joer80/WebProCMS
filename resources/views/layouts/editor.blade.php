<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['cssBundle' => 'resources/css/editor.css'])
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
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

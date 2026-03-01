<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Branding')] class extends Component {
    public string $logoUrl = '';

    /** @var array<string, string> */
    public array $themeColors = [];

    public function mount(): void
    {
        $this->logoUrl = config('branding.logo_url', '');
        $this->loadThemeColors();
    }

    public function saveLogo(): void
    {
        $this->validate([
            'logoUrl' => ['nullable', 'url'],
        ]);

        $path = config_path('branding.php');
        $e = fn (string $v): string => str_replace("'", "\\'", $v);

        file_put_contents($path, implode("\n", [
            '<?php',
            '',
            'return [',
            '',
            "    'logo_url' => '{$e($this->logoUrl)}',",
            '',
            '];',
            '',
        ]));

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        config(['branding.logo_url' => $this->logoUrl]);

        $this->dispatch('notify', message: 'Logo saved.');
    }

    public function saveThemeColors(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $cssPath = resource_path('css/app.css');
        $css = file_get_contents($cssPath);

        foreach ($this->themeColors as $name => $value) {
            if (! preg_match('/^#[0-9a-fA-F]{3,8}$/', $value)) {
                continue;
            }

            $css = preg_replace(
                '/(--color-' . preg_quote($name, '/') . '\s*:\s*)(#[0-9a-fA-F]{3,8})/',
                '${1}' . $value,
                $css
            );
        }

        file_put_contents($cssPath, $css);

        $this->dispatch('notify', message: 'Theme colors saved. Rebuild assets to apply.');
    }

    protected function loadThemeColors(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        preg_match('/@theme\s*\{([^}]+)\}/s', $css, $themeMatch);
        $themeBlock = $themeMatch[1] ?? '';

        preg_match_all('/--color-([a-z][a-z0-9-]+)\s*:\s*(#[0-9a-fA-F]{3,8})/', $themeBlock, $matches, PREG_SET_ORDER);

        $standardColors = ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
        $standardShades = ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'];

        foreach ($matches as $match) {
            $name = $match[1];
            $value = $match[2];

            $isStandardScale = false;

            foreach ($standardColors as $color) {
                foreach ($standardShades as $shade) {
                    if ($name === "{$color}-{$shade}") {
                        $isStandardScale = true;
                        break 2;
                    }
                }
            }

            if (! $isStandardScale) {
                $this->themeColors[$name] = $value;
            }
        }
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Branding</flux:heading>
            <flux:text class="mt-1">Logo and theme colors for your site.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Logo</flux:heading>
                        <flux:text class="mt-1">The logo displayed in the site header and sidebar.</flux:text>
                        <div class="mt-4 space-y-4">
                            @if ($logoUrl)
                                <div class="p-3 rounded-md bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 inline-block">
                                    <img src="{{ $logoUrl }}" alt="Logo preview" class="h-10 w-auto" />
                                </div>
                            @endif
                            <flux:input wire:model="logoUrl" label="Logo URL" placeholder="https://domain.com/storage/logos/logo.svg" description="Copy the URL from your Media Library." />
                        </div>
                    </div>
                    <flux:button wire:click="saveLogo" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 {{ ! app()->isLocal() ? 'opacity-50' : '' }}">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Theme Colors</flux:heading>
                        <flux:text class="mt-1">Custom colors defined in <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">resources/css/app.css</code>. Changes must be committed to git to take effect in production.</flux:text>
                        @if (! app()->isLocal())
                            <flux:text class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                                ⚠ Theme color editing is only available in your local environment. Make changes locally and commit to git.
                            </flux:text>
                        @endif
                        <div class="mt-4 space-y-3 {{ ! app()->isLocal() ? 'pointer-events-none select-none' : '' }}">
                            @forelse ($themeColors as $name => $value)
                                <div class="grid grid-cols-[2.5rem_1fr_7rem] items-center gap-3">
                                    <input
                                        type="color"
                                        wire:model="themeColors.{{ $name }}"
                                        class="h-8 w-10 rounded cursor-pointer border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 p-0.5"
                                    >
                                    <div>
                                        <div class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $name }}</div>
                                        <div class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">--color-{{ $name }}</div>
                                    </div>
                                    <input
                                        type="text"
                                        wire:model="themeColors.{{ $name }}"
                                        placeholder="#000000"
                                        class="font-mono text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-2.5 py-1.5 w-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    >
                                </div>
                            @empty
                                <flux:text class="text-sm text-zinc-400">No custom hex colors found in <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">@theme</code>.</flux:text>
                            @endforelse
                        </div>
                    </div>
                    <flux:button wire:click="saveThemeColors" variant="outline" class="shrink-0" :disabled="! app()->isLocal()">Save</flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>

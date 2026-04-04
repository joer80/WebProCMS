<?php

use App\Models\Setting;
use App\Services\BrandingStyleService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Branding')] class extends Component {
    public string $logoUrl = '';

    public string $darkLogoUrl = '';

    public bool $whiteLabelEnabled = false;

    public bool $showMediaPicker = false;

    public bool $showDarkMediaPicker = false;

    /** @var array<string, string> */
    public array $themeColors = [];

    public function mount(): void
    {
        $this->logoUrl = (string) Setting::get('branding.logo_url', '');
        $this->darkLogoUrl = (string) Setting::get('branding.dark_logo_url', '');
        $this->whiteLabelEnabled = (bool) Setting::get('branding.white_label', false);
        $this->loadThemeColors();
    }

    public function updatedWhiteLabelEnabled(): void
    {
        Setting::set('branding.white_label', $this->whiteLabelEnabled);
        $this->dispatch('notify', message: $this->whiteLabelEnabled ? 'White label enabled.' : 'White label disabled.');
    }

    public function openMediaPicker(): void
    {
        $this->showMediaPicker = true;
    }

    public function removeLogo(): void
    {
        $this->logoUrl = '';
        $this->writeBrandingConfig();
        $this->dispatch('notify', message: 'Logo removed.');
    }

    public function openDarkMediaPicker(): void
    {
        $this->showDarkMediaPicker = true;
    }

    public function removeDarkLogo(): void
    {
        $this->darkLogoUrl = '';
        $this->writeBrandingConfig();
        $this->dispatch('notify', message: 'Dark logo removed.');
    }

    #[On('media-image-picked')]
    public function handleMediaImagePicked(string $key, string $path, string $alt = ''): void
    {
        if ($key === 'branding-logo') {
            $this->logoUrl = Storage::url($path);
            $this->showMediaPicker = false;
            $this->writeBrandingConfig();
            $this->dispatch('notify', message: 'Logo saved.');
        } elseif ($key === 'branding-logo-dark') {
            $this->darkLogoUrl = Storage::url($path);
            $this->showDarkMediaPicker = false;
            $this->writeBrandingConfig();
            $this->dispatch('notify', message: 'Dark logo saved.');
        }
    }

    public function saveThemeColors(): void
    {
        Setting::set('branding.colors', $this->themeColors);
        app(BrandingStyleService::class)->bust();
        $this->dispatch('notify', message: 'Theme colors saved.');
    }

    protected function loadThemeColors(): void
    {
        $service = app(BrandingStyleService::class);
        $this->themeColors = (array) Setting::get('branding.colors', $service->defaultColors());
    }

    protected function writeBrandingConfig(): void
    {
        Setting::set('branding.logo_url', $this->logoUrl);
        Setting::set('branding.dark_logo_url', $this->darkLogoUrl);
    }
}; ?>

<div
    x-data="{
        openPicker: null,
        twColors: {{ \App\Support\TailwindColors::toJson() }},
        togglePicker(name) { this.openPicker = this.openPicker === name ? null : name; },
        pickColor(name, hex) { $wire.set('themeColors.' + name, hex); this.openPicker = null; },
    }"
    @click.window="openPicker = null"
>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Branding</flux:heading>
            <flux:text class="mt-1">Logo and theme colors for your site.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading>Logo</flux:heading>
                <flux:text class="mt-1">The logo displayed in the site header and sidebar.</flux:text>

                <div class="mt-4 space-y-4">
                    @if ($logoUrl)
                        <div class="p-3 rounded-md bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 w-fit">
                            <img src="{{ $logoUrl }}" alt="Logo preview" class="h-10 w-auto" />
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <flux:button variant="outline" icon="photo" wire:click="openMediaPicker">
                            {{ $logoUrl ? 'Change logo' : 'Pick from Media Library' }}
                        </flux:button>
                        @if ($logoUrl)
                            <flux:button variant="ghost" icon="trash" wire:click="removeLogo" wire:confirm="Remove the logo? The default CMS logo will be used instead.">
                                Remove
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading>Dark Background Logo</flux:heading>
                <flux:text class="mt-1">An alternate logo for use on dark backgrounds — dark footers, dark sections, and dark mode. Falls back to the main logo if not set.</flux:text>

                <div class="mt-4 space-y-4">
                    @if ($darkLogoUrl)
                        <div class="p-3 rounded-md bg-zinc-800 border border-zinc-700 w-fit">
                            <img src="{{ $darkLogoUrl }}" alt="Dark logo preview" class="h-10 w-auto" />
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <flux:button variant="outline" icon="photo" wire:click="openDarkMediaPicker">
                            {{ $darkLogoUrl ? 'Change dark logo' : 'Pick from Media Library' }}
                        </flux:button>
                        @if ($darkLogoUrl)
                            <flux:button variant="ghost" icon="trash" wire:click="removeDarkLogo" wire:confirm="Remove the dark logo?">
                                Remove
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <flux:heading>White Label</flux:heading>
                        <flux:text class="mt-1">Use your uploaded logo in the admin sign-in page and sidebar instead of the WebProCMS logo.</flux:text>
                        @if (! $logoUrl)
                            <flux:text class="mt-2 text-xs text-amber-600 dark:text-amber-400">⚠ Upload a logo above for this to take effect.</flux:text>
                        @endif
                    </div>
                    <flux:switch wire:model.live="whiteLabelEnabled" />
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Theme Colors</flux:heading>
                        <flux:text class="mt-1">Custom theme colors for your site.</flux:text>
                        <div class="mt-4 space-y-3">
                            @forelse ($themeColors as $name => $value)
                                <div class="grid grid-cols-[2.5rem_1fr_7rem_2rem] items-center gap-3">
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
                                    {{-- Tailwind color palette picker --}}
                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click.stop="togglePicker('{{ $name }}')"
                                            title="Pick a Tailwind color"
                                            x-bind:class="openPicker === '{{ $name }}' ? 'border-primary text-primary' : 'border-zinc-300 dark:border-zinc-600 text-zinc-400 hover:border-primary hover:text-primary'"
                                            class="size-8 flex items-center justify-center rounded-lg border transition-colors"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM2 12.75v-3a1.75 1.75 0 0 1 1.75-1.75h12.5A1.75 1.75 0 0 1 18 9.75v3a1.75 1.75 0 0 1-1.75 1.75h-1.6l.666 2.337a.75.75 0 1 1-1.44.41L13.5 15H13v.75a.75.75 0 0 1-1.5 0V15h-.5l-.376 2.247a.75.75 0 1 1-1.48-.247L9.5 15H9v.75a.75.75 0 0 1-1.5 0V15H6.5l-.376 2.247a.75.75 0 1 1-1.48-.247L5 15H3.75A1.75 1.75 0 0 1 2 13.25v-.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        {{-- Color picker popover --}}
                                        <div
                                            x-show="openPicker === '{{ $name }}'"
                                            x-cloak
                                            @click.stop
                                            class="absolute right-0 top-full mt-1 z-50 w-72 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-xl p-3 overflow-y-auto max-h-80"
                                        >
                                            <div class="text-xs font-medium text-zinc-400 dark:text-zinc-500 mb-2">Tailwind colors — click to apply</div>
                                            <template x-for="[colorName, shades] in Object.entries(twColors)" :key="colorName">
                                                <div class="flex items-center gap-1 mb-0.5">
                                                    <span class="text-xs text-zinc-400 dark:text-zinc-500 w-14 shrink-0 capitalize" x-text="colorName"></span>
                                                    <template x-for="[shade, hex] in Object.entries(shades)" :key="shade">
                                                        <button
                                                            type="button"
                                                            :style="'background-color:' + hex"
                                                            :title="colorName + '-' + shade + ' · ' + hex"
                                                            @click="pickColor('{{ $name }}', hex)"
                                                            class="size-4 rounded-sm cursor-pointer transition-transform hover:scale-125 hover:ring-2 hover:ring-offset-1 hover:ring-zinc-500"
                                                        ></button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <flux:text class="text-sm text-zinc-400">No custom hex colors found in <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">@theme</code>.</flux:text>
                            @endforelse
                        </div>
                    </div>
                    <flux:button wire:click="saveThemeColors" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>
        </div>
    </flux:main>

    {{-- Media Library Picker Modal --}}
    <flux:modal wire:model="showMediaPicker" name="branding-logo-picker" class="p-0!" style="max-width: 75vw; width: 75vw;">
        @if ($showMediaPicker)
            <livewire:pages::dashboard.media-library.picker
                field-key="branding-logo"
                default-category-slug="logos"
                :key="'branding-logo-picker'"
            />
        @endif
    </flux:modal>

    {{-- Dark Logo Media Library Picker Modal --}}
    <flux:modal wire:model="showDarkMediaPicker" name="branding-dark-logo-picker" class="p-0!" style="max-width: 75vw; width: 75vw;">
        @if ($showDarkMediaPicker)
            <livewire:pages::dashboard.media-library.picker
                field-key="branding-logo-dark"
                default-category-slug="logos"
                :key="'branding-dark-logo-picker'"
            />
        @endif
    </flux:modal>
</div>

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

    public bool $whiteLabelEnabled = false;

    public bool $showMediaPicker = false;

    /** @var array<string, string> */
    public array $themeColors = [];

    public function mount(): void
    {
        $this->logoUrl = (string) Setting::get('branding.logo_url', '');
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

    #[On('media-image-picked')]
    public function handleMediaImagePicked(string $key, string $path, string $alt = ''): void
    {
        if ($key !== 'branding-logo') {
            return;
        }

        $this->logoUrl = Storage::url($path);
        $this->showMediaPicker = false;
        $this->writeBrandingConfig();
        $this->dispatch('notify', message: 'Logo saved.');
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
</div>

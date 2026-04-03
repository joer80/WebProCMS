<?php

use App\Models\Setting;
use App\Services\BrandingStyleService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Design')] class extends Component {
    public string $bodyFont = 'instrument-sans';

    public string $headingFont = 'instrument-sans';

    public string $sectionSpacing = 'medium';

    public string $containerWidth = 'medium';

    public function mount(): void
    {
        $this->loadTypography();
    }

    public function saveTypography(): void
    {
        $this->validate([
            'bodyFont'       => ['required', 'string', 'in:instrument-sans,inter,system'],
            'headingFont'    => ['required', 'string', 'in:instrument-sans,inter,system'],
            'sectionSpacing' => ['required', 'string', 'in:small,medium,large'],
            'containerWidth' => ['required', 'string', 'in:small,medium,large'],
        ]);

        $this->writeBrandingConfig();
        app(BrandingStyleService::class)->bust();

        $this->dispatch('notify', message: 'Design saved.');
    }

    protected function loadTypography(): void
    {
        $this->bodyFont = (string) Setting::get('branding.body_font', 'instrument-sans');
        $this->headingFont = (string) Setting::get('branding.heading_font', 'instrument-sans');
        $this->sectionSpacing = (string) Setting::get('branding.section_spacing', 'medium');
        $this->containerWidth = (string) Setting::get('branding.container_width', 'medium');
    }

    protected function writeBrandingConfig(): void
    {
        Setting::set('branding.logo_url', Setting::get('branding.logo_url', ''));
        Setting::set('branding.body_font', $this->bodyFont);
        Setting::set('branding.heading_font', $this->headingFont);
        Setting::set('branding.section_spacing', $this->sectionSpacing);
        Setting::set('branding.container_width', $this->containerWidth);
    }

}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Design</flux:heading>
            <flux:text class="mt-1">Typography, spacing, and layout defaults for your site.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Typography &amp; Spacing</flux:heading>
                        <flux:text class="mt-1">Font families and section spacing.</flux:text>
                        <div class="mt-4 space-y-4">
                            <flux:select wire:model="bodyFont" label="Body Font" description="Font used for all body text (--font-sans).">
                                <option value="instrument-sans">Instrument Sans</option>
                                <option value="inter">Inter</option>
                                <option value="system">System Font</option>
                            </flux:select>
                            <flux:select wire:model="headingFont" label="Heading Font" description="Font used with the font-heading class on headings (--font-heading).">
                                <option value="instrument-sans">Instrument Sans</option>
                                <option value="inter">Inter</option>
                                <option value="system">System Font</option>
                            </flux:select>
                            <div>
                                <flux:label>Section Spacing</flux:label>
                                <flux:text class="mt-1 text-xs text-zinc-500">Vertical padding for page sections. Controls py-section (standard), py-section-banner (compact CTAs), and py-section-hero (heroes).</flux:text>
                                <div class="mt-2 flex gap-2">
                                    @foreach (['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('sectionSpacing', '{{ $value }}')"
                                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors {{ $sectionSpacing === $value ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600 hover:border-zinc-400' }}"
                                        >{{ $label }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <flux:label>Container Width</flux:label>
                                <flux:text class="mt-1 text-xs text-zinc-500">Max width of page containers and the mega menu panel. Small = 56rem (896px), Medium = 72rem (1152px), Large = 80rem (1280px).</flux:text>
                                <div class="mt-2 flex gap-2">
                                    @foreach (['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'] as $value => $label)
                                        <button
                                            type="button"
                                            wire:click="$set('containerWidth', '{{ $value }}')"
                                            class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors {{ $containerWidth === $value ? 'bg-primary text-white border-primary' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600 hover:border-zinc-400' }}"
                                        >{{ $label }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <flux:button wire:click="saveTypography" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>

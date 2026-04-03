<?php

use App\Models\Setting;
use App\Services\BrandingStyleService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Typography')] class extends Component {
    public string $bodyFont = 'instrument-sans';

    public string $headingFont = 'instrument-sans';

    public function mount(): void
    {
        $this->loadTypography();
    }

    public function saveTypography(): void
    {
        $this->validate([
            'bodyFont'    => ['required', 'string', 'in:instrument-sans,inter,system'],
            'headingFont' => ['required', 'string', 'in:instrument-sans,inter,system'],
        ]);

        $this->writeBrandingConfig();
        app(BrandingStyleService::class)->bust();

        $this->dispatch('notify', message: 'Typography saved.');
    }

    protected function loadTypography(): void
    {
        $this->bodyFont = (string) Setting::get('branding.body_font', 'instrument-sans');
        $this->headingFont = (string) Setting::get('branding.heading_font', 'instrument-sans');
    }

    protected function writeBrandingConfig(): void
    {
        Setting::set('branding.logo_url', Setting::get('branding.logo_url', ''));
        Setting::set('branding.body_font', $this->bodyFont);
        Setting::set('branding.heading_font', $this->headingFont);
    }

}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Typography</flux:heading>
            <flux:text class="mt-1">Font family defaults for your site.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Font Families</flux:heading>
                        <flux:text class="mt-1">Font families used across the site.</flux:text>
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
                        </div>
                    </div>
                    <flux:button wire:click="saveTypography" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>

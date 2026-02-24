<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Settings')] class extends Component {
    public string $locationsMode = 'single';

    public function mount(): void
    {
        $this->locationsMode = Setting::get('locations_mode', 'single');
    }

    public function saveLocationsMode(): void
    {
        $this->validate(['locationsMode' => ['required', 'in:single,multiple']]);

        Setting::set('locations_mode', $this->locationsMode);

        $this->dispatch('notify', message: 'Settings saved.');
    }
}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">Settings</flux:heading>
            <flux:text class="mt-1">Configure your website preferences.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Locations</flux:heading>
                        <flux:text class="mt-1">Choose whether your site has a single location or multiple locations.</flux:text>
                        <flux:radio.group wire:model="locationsMode" class="mt-4">
                            <flux:radio value="single" label="Single location" description="Your site has one primary location." />
                            <flux:radio value="multiple" label="Multiple locations" description="Your site has several locations to display." />
                        </flux:radio.group>
                    </div>
                    <flux:button wire:click="saveLocationsMode" variant="outline" class="shrink-0">
                        Save
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:main>
</div>

<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


new #[Layout('layouts.app')] #[Title('General Settings')] class extends Component {
    public string $businessUrl = '';

    public string $businessPhone = '';

    public string $businessEmail = '';

    public string $businessAddressStreet = '';

    public string $businessAddressCityStateZip = '';

    public string $businessHours = '';

    public string $seoSchemaType = 'Organization';

    public string $seoSchemaLogo = '';

    public string $seoSchemaDescription = '';

    public string $seoAddressCity = '';

    public string $seoAddressRegion = '';

    public string $seoAddressPostalCode = '';

    public string $seoAddressCountry = 'US';

    public string $seoOgDefaultImage = '';

    public string $seoTwitterHandle = '';

    public function mount(): void
    {
        $this->businessUrl = (string) Setting::get('business.url', '');
        $this->businessPhone = (string) Setting::get('business.phone', '');
        $this->businessEmail = (string) Setting::get('business.email', '');
        $this->businessAddressStreet = (string) Setting::get('business.address_street', '');
        $this->businessAddressCityStateZip = (string) Setting::get('business.address_city_state_zip', '');
        $this->businessHours = (string) Setting::get('business.hours', '');

        $seoSchema = Setting::get('seo.schema', []);
        $this->seoSchemaType = (string) ($seoSchema['type'] ?? 'Organization');
        $this->seoSchemaLogo = (string) ($seoSchema['logo'] ?? '');
        $this->seoSchemaDescription = (string) ($seoSchema['description'] ?? '');
        $this->seoAddressCity = (string) ($seoSchema['address']['city'] ?? '');
        $this->seoAddressRegion = (string) ($seoSchema['address']['region'] ?? '');
        $this->seoAddressPostalCode = (string) ($seoSchema['address']['postal_code'] ?? '');
        $this->seoAddressCountry = (string) ($seoSchema['address']['country'] ?? 'US');
        $this->seoOgDefaultImage = (string) Setting::get('seo.og.default_image', '');
        $this->seoTwitterHandle = (string) Setting::get('seo.twitter.handle', '');
    }

    public function saveBusinessInfo(): void
    {
        $this->validate([
            'businessUrl' => ['nullable', 'url'],
            'businessPhone' => ['nullable', 'string', 'max:255'],
            'businessEmail' => ['nullable', 'email', 'max:255'],
            'businessAddressStreet' => ['nullable', 'string', 'max:255'],
            'businessAddressCityStateZip' => ['nullable', 'string', 'max:255'],
            'businessHours' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('business.url', $this->businessUrl);
        Setting::set('business.phone', $this->businessPhone);
        Setting::set('business.email', $this->businessEmail);
        Setting::set('business.address_street', $this->businessAddressStreet);
        Setting::set('business.address_city_state_zip', $this->businessAddressCityStateZip);
        Setting::set('business.hours', $this->businessHours);

        $this->dispatch('notify', message: 'Business info saved.');
    }

    public function saveSeoSettings(): void
    {
        $this->validate([
            'seoSchemaType' => ['required', 'in:Organization,LocalBusiness'],
            'seoSchemaLogo' => ['nullable', 'url'],
            'seoSchemaDescription' => ['nullable', 'string', 'max:500'],
            'seoAddressCity' => ['nullable', 'string', 'max:255'],
            'seoAddressRegion' => ['nullable', 'string', 'max:100'],
            'seoAddressPostalCode' => ['nullable', 'string', 'max:20'],
            'seoAddressCountry' => ['nullable', 'string', 'size:2'],
            'seoOgDefaultImage' => ['nullable', 'url'],
            'seoTwitterHandle' => ['nullable', 'string', 'max:100'],
        ]);

        Setting::set('seo.schema', [
            'type' => $this->seoSchemaType,
            'logo' => $this->seoSchemaLogo,
            'description' => $this->seoSchemaDescription,
            'address' => [
                'city' => $this->seoAddressCity,
                'region' => $this->seoAddressRegion,
                'postal_code' => $this->seoAddressPostalCode,
                'country' => $this->seoAddressCountry,
            ],
        ]);
        Setting::set('seo.og.default_image', $this->seoOgDefaultImage);
        Setting::set('seo.twitter.handle', $this->seoTwitterHandle);

        $this->dispatch('notify', message: 'SEO settings saved.');
    }


}; ?>

<div>
    <flux:main>
        <div class="mb-8">
            <flux:heading size="xl">General Settings</flux:heading>
            <flux:text class="mt-1">Business information and SEO configuration.</flux:text>
        </div>

        <div class="max-w-2xl space-y-4">
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Business Information</flux:heading>
                        <flux:text class="mt-1">Contact details shown on your public site and in structured data.</flux:text>
                        <div class="mt-4 space-y-4">
                            <flux:input wire:model="businessUrl" label="Website URL" placeholder="https://domain.com" />
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="businessPhone" label="Phone" placeholder="+1 (512) 555-0100" />
                                <flux:input wire:model="businessEmail" label="Email" placeholder="sales@domain.com" />
                            </div>
                            <flux:input wire:model="businessAddressStreet" label="Street address" placeholder="100 Congress Ave, Suite 200" />
                            <flux:input wire:model="businessAddressCityStateZip" label="City, state, zip" placeholder="Austin, TX 78701" />
                            <flux:input wire:model="businessHours" label="Business hours" placeholder="Monday – Friday, 9am – 5pm CT" />
                        </div>
                    </div>
                    <flux:button wire:click="saveBusinessInfo" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>SEO</flux:heading>
                        <flux:text class="mt-1">Schema.org structured data, Open Graph defaults, and social metadata.</flux:text>
                        <div class="mt-4 space-y-4">
                            <flux:select wire:model="seoSchemaType" label="Schema type">
                                <flux:select.option value="Organization">Organization</flux:select.option>
                                <flux:select.option value="LocalBusiness">LocalBusiness</flux:select.option>
                            </flux:select>
                            <flux:textarea wire:model="seoSchemaDescription" label="Schema description" rows="3" placeholder="Business Name helps you manage your web content efficiently." />
                            <flux:input wire:model="seoSchemaLogo" label="Logo URL" placeholder="https://domain.com/logo.png" />
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="seoAddressCity" label="City" placeholder="Austin" />
                                <flux:input wire:model="seoAddressRegion" label="State / Region" placeholder="TX" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input wire:model="seoAddressPostalCode" label="Postal code" placeholder="78701" />
                                <flux:input wire:model="seoAddressCountry" label="Country code" placeholder="US" />
                            </div>
                            <flux:input wire:model="seoOgDefaultImage" label="Default OG image URL" placeholder="https://domain.com/og-image.jpg" />
                            <flux:input wire:model="seoTwitterHandle" label="Twitter / X handle" placeholder="@businessname" />
                        </div>
                    </div>
                    <flux:button wire:click="saveSeoSettings" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

        </div>
    </flux:main>
</div>

<?php

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('General Settings')] class extends Component {
    public string $locationsMode = 'single';

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
        $this->locationsMode = Setting::get('locations_mode', 'single');

        $this->businessUrl = config('business.url');
        $this->businessPhone = config('business.phone');
        $this->businessEmail = config('business.email');
        $this->businessAddressStreet = config('business.address_street');
        $this->businessAddressCityStateZip = config('business.address_city_state_zip');
        $this->businessHours = config('business.hours');

        $this->seoSchemaType = config('seo.schema.type');
        $this->seoSchemaLogo = config('seo.schema.logo');
        $this->seoSchemaDescription = config('seo.schema.description');
        $this->seoAddressCity = config('seo.schema.address.city');
        $this->seoAddressRegion = config('seo.schema.address.region');
        $this->seoAddressPostalCode = config('seo.schema.address.postal_code');
        $this->seoAddressCountry = config('seo.schema.address.country');
        $this->seoOgDefaultImage = config('seo.og.default_image');
        $this->seoTwitterHandle = config('seo.twitter.handle');
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

        $path = config_path('business.php');
        file_put_contents($path, $this->buildBusinessConfig());

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        config([
            'business.url' => $this->businessUrl,
            'business.phone' => $this->businessPhone,
            'business.email' => $this->businessEmail,
            'business.address_street' => $this->businessAddressStreet,
            'business.address_city_state_zip' => $this->businessAddressCityStateZip,
            'business.hours' => $this->businessHours,
        ]);

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

        $path = config_path('seo.php');
        file_put_contents($path, $this->buildSeoConfig());

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        config([
            'seo.schema.type' => $this->seoSchemaType,
            'seo.schema.logo' => $this->seoSchemaLogo,
            'seo.schema.description' => $this->seoSchemaDescription,
            'seo.schema.address.city' => $this->seoAddressCity,
            'seo.schema.address.region' => $this->seoAddressRegion,
            'seo.schema.address.postal_code' => $this->seoAddressPostalCode,
            'seo.schema.address.country' => $this->seoAddressCountry,
            'seo.og.default_image' => $this->seoOgDefaultImage,
            'seo.twitter.handle' => $this->seoTwitterHandle,
        ]);

        $this->dispatch('notify', message: 'SEO settings saved.');
    }

    public function saveLocationsMode(): void
    {
        $this->validate(['locationsMode' => ['required', 'in:single,multiple']]);

        Setting::set('locations_mode', $this->locationsMode);

        $this->dispatch('notify', message: 'Settings saved.');
    }

    protected function buildBusinessConfig(): string
    {
        $e = fn (string $v): string => str_replace("'", "\\'", $v);

        return implode("\n", [
            '<?php',
            '',
            'return [',
            '',
            "    'url' => '{$e($this->businessUrl)}',",
            "    'phone' => '{$e($this->businessPhone)}',",
            "    'email' => '{$e($this->businessEmail)}',",
            "    'admin_email' => env('BUSINESS_ADMIN_EMAIL', ''),",
            "    'address_street' => '{$e($this->businessAddressStreet)}',",
            "    'address_city_state_zip' => '{$e($this->businessAddressCityStateZip)}',",
            "    'hours' => '{$e($this->businessHours)}',",
            '',
            '];',
            '',
        ]);
    }

    protected function buildSeoConfig(): string
    {
        $e = fn (string $v): string => str_replace("'", "\\'", $v);

        return implode("\n", [
            '<?php',
            '',
            'return [',
            '',
            "    'schema' => [",
            "        'type' => '{$e($this->seoSchemaType)}',",
            "        'name' => env('APP_NAME', ''),",
            "        'url' => env('APP_URL', ''),",
            "        'logo' => '{$e($this->seoSchemaLogo)}',",
            "        'description' => '{$e($this->seoSchemaDescription)}',",
            "        'phone' => config('business.phone', ''),",
            "        'email' => config('business.email', ''),",
            "        'address' => [",
            "            'street' => config('business.address_street', ''),",
            "            'city' => '{$e($this->seoAddressCity)}',",
            "            'region' => '{$e($this->seoAddressRegion)}',",
            "            'postal_code' => '{$e($this->seoAddressPostalCode)}',",
            "            'country' => '{$e($this->seoAddressCountry)}',",
            '        ],',
            "        'hours' => config('business.hours', ''),",
            '    ],',
            '',
            "    'og' => [",
            "        'default_image' => '{$e($this->seoOgDefaultImage)}',",
            '    ],',
            '',
            "    'twitter' => [",
            "        'handle' => '{$e($this->seoTwitterHandle)}',",
            '    ],',
            '',
            '];',
            '',
        ]);
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

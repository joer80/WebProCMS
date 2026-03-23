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

    /** @var list<string> */
    public array $activeLanguageCodes = ['en'];

    /** @var list<array{code: string, label: string, flag: string}> */
    public array $customLanguages = [];

    public string $newLanguageCode = '';

    public string $newLanguageLabel = '';

    public string $newLanguageFlag = '';

    public string $languageSwitcherTheme = 'light';

    /** @return list<array{code: string, label: string, flag: string}> */
    public static function predefinedLanguages(): array
    {
        return [
            ['code' => 'en', 'label' => 'English', 'flag' => '🇺🇸'],
            ['code' => 'es', 'label' => 'Spanish (Mexican)', 'flag' => '🇲🇽'],
            ['code' => 'es-ES', 'label' => 'Spanish (Spain)', 'flag' => '🇪🇸'],
            ['code' => 'fr', 'label' => 'French', 'flag' => '🇫🇷'],
            ['code' => 'de', 'label' => 'German', 'flag' => '🇩🇪'],
            ['code' => 'pt', 'label' => 'Portuguese', 'flag' => '🇵🇹'],
            ['code' => 'it', 'label' => 'Italian', 'flag' => '🇮🇹'],
            ['code' => 'ja', 'label' => 'Japanese', 'flag' => '🇯🇵'],
            ['code' => 'zh', 'label' => 'Chinese', 'flag' => '🇨🇳'],
            ['code' => 'ar', 'label' => 'Arabic', 'flag' => '🇸🇦'],
        ];
    }

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

        $saved = Setting::get('site.languages', [['code' => 'en', 'label' => 'English', 'flag' => '🇺🇸']]);
        $predefinedCodes = array_column(self::predefinedLanguages(), 'code');
        $this->activeLanguageCodes = array_values(array_column($saved, 'code'));
        $this->customLanguages = array_values(array_filter($saved, fn ($l) => ! in_array($l['code'], $predefinedCodes, true)));
        $this->languageSwitcherTheme = (string) Setting::get('site.language_switcher_theme', 'light');
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

    public function saveLanguages(): void
    {
        $this->validate([
            'activeLanguageCodes' => ['required', 'array'],
            'activeLanguageCodes.*' => ['required', 'string', 'max:10'],
        ]);

        // Always ensure English is first
        if (! in_array('en', $this->activeLanguageCodes, true)) {
            array_unshift($this->activeLanguageCodes, 'en');
        }

        $allLanguages = self::predefinedLanguages();

        // Append custom languages
        foreach ($this->customLanguages as $custom) {
            $allLanguages[] = $custom;
        }

        $allByCode = array_column($allLanguages, null, 'code');

        $toSave = array_values(array_filter(
            array_map(fn ($code) => $allByCode[$code] ?? null, $this->activeLanguageCodes),
            fn ($l) => $l !== null
        ));

        Setting::set('site.languages', $toSave);
        Setting::set('site.language_switcher_theme', in_array($this->languageSwitcherTheme, ['light', 'dark'], true) ? $this->languageSwitcherTheme : 'light');

        $this->dispatch('notify', message: 'Languages saved.');
    }

    public function addCustomLanguage(): void
    {
        $this->validate([
            'newLanguageCode' => ['required', 'string', 'max:10', 'alpha'],
            'newLanguageLabel' => ['required', 'string', 'max:100'],
            'newLanguageFlag' => ['nullable', 'string', 'max:10'],
        ]);

        $code = strtolower(trim($this->newLanguageCode));
        $label = trim($this->newLanguageLabel);
        $flag = trim($this->newLanguageFlag) ?: '🌐';

        // Prevent duplicate codes
        $allCodes = array_merge(
            array_column(self::predefinedLanguages(), 'code'),
            array_column($this->customLanguages, 'code'),
        );

        if (in_array($code, $allCodes, true)) {
            $this->addError('newLanguageCode', 'A language with this code already exists.');

            return;
        }

        $this->customLanguages[] = ['code' => $code, 'label' => $label, 'flag' => $flag];
        $this->activeLanguageCodes[] = $code;

        $this->newLanguageCode = '';
        $this->newLanguageLabel = '';
        $this->newLanguageFlag = '';
    }

    public function removeCustomLanguage(string $code): void
    {
        $this->customLanguages = array_values(array_filter($this->customLanguages, fn ($l) => $l['code'] !== $code));
        $this->activeLanguageCodes = array_values(array_filter($this->activeLanguageCodes, fn ($c) => $c !== $code));
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
                @php
                    $predefined = $this::predefinedLanguages();
                    $predefinedCodes = array_column($predefined, 'code');
                    $allAvailable = array_merge($predefined, $customLanguages);
                @endphp
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <flux:heading>Languages</flux:heading>
                        <flux:text class="mt-1">Enable multiple languages to show a language picker on your public site. Content can be translated per-language in the page editor.</flux:text>
                        <div class="mt-4 space-y-2">
                            @foreach ($allAvailable as $lang)
                                <div class="flex items-center gap-3">
                                    <flux:checkbox
                                        wire:model="activeLanguageCodes"
                                        value="{{ $lang['code'] }}"
                                        :disabled="$lang['code'] === 'en'"
                                        id="lang-{{ $lang['code'] }}"
                                    />
                                    <label for="lang-{{ $lang['code'] }}" class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 {{ $lang['code'] === 'en' ? 'opacity-60' : 'cursor-pointer' }}">
                                        <span class="text-base">{{ $lang['flag'] }}</span>
                                        <span>{{ $lang['label'] }}</span>
                                        <span class="text-xs font-mono text-zinc-400">({{ $lang['code'] }})</span>
                                        @if (! in_array($lang['code'], $predefinedCodes, true))
                                            <button
                                                type="button"
                                                wire:click="removeCustomLanguage('{{ $lang['code'] }}')"
                                                class="ml-1 text-red-400 hover:text-red-600 transition-colors"
                                                title="Remove custom language"
                                            >
                                                <flux:icon name="x-mark" class="size-3" />
                                            </button>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 border-t border-zinc-200 dark:border-zinc-700 pt-4" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                                <flux:icon name="chevron-right" class="size-3 transition-transform duration-150" x-bind:class="open ? 'rotate-90' : ''" />
                                Add custom language
                            </button>
                            <div x-show="open" x-collapse class="mt-3">
                                <div class="flex items-end gap-2 flex-wrap">
                                    <flux:input wire:model="newLanguageCode" label="Code" placeholder="ko" class="w-20" />
                                    <flux:input wire:model="newLanguageLabel" label="Label" placeholder="Korean" class="w-36" />
                                    <flux:input wire:model="newLanguageFlag" label="Flag emoji" placeholder="🇰🇷" class="w-24" />
                                    <flux:button wire:click="addCustomLanguage" variant="outline" size="sm" class="mb-0.5">Add</flux:button>
                                </div>
                                @error('newLanguageCode') <flux:error>{{ $message }}</flux:error> @enderror
                            </div>
                        </div>
                        @if (count($activeLanguageCodes) > 1)
                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Switcher style</p>
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="languageSwitcherTheme" value="light" class="accent-primary" />
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Light</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-zinc-200 shadow text-xs font-semibold text-zinc-800 pointer-events-none">🇺🇸 EN</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="languageSwitcherTheme" value="dark" class="accent-primary" />
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Dark</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-700 shadow text-xs font-semibold text-zinc-100 pointer-events-none">🇺🇸 EN</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                                <flux:icon name="information-circle" class="size-4 shrink-0" />
                                <span>Language picker will appear on your public site. Content can be translated in the page editor.</span>
                            </div>
                        @endif
                    </div>
                    <flux:button wire:click="saveLanguages" variant="outline" class="shrink-0">Save</flux:button>
                </div>
            </div>

        </div>
    </flux:main>
</div>

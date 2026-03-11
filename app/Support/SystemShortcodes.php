<?php

namespace App\Support;

use App\Models\Setting;

class SystemShortcodes
{
    /**
     * All built-in shortcodes sourced from application config.
     * DB shortcodes with the same tag take priority over these at render time.
     *
     * @return array<string, array{label: string, value: string}>
     */
    public static function all(): array
    {
        $street = (string) Setting::get('business.address_street', '');
        $cityStateZip = (string) Setting::get('business.address_city_state_zip', '');

        return [
            'site_name' => [
                'label' => 'Site Name',
                'value' => (string) config('app.name', ''),
            ],
            'business_phone' => [
                'label' => 'Phone',
                'value' => (string) Setting::get('business.phone', ''),
            ],
            'business_email' => [
                'label' => 'Email',
                'value' => (string) Setting::get('business.email', ''),
            ],
            'business_url' => [
                'label' => 'Website URL',
                'value' => (string) Setting::get('business.url', ''),
            ],
            'business_hours' => [
                'label' => 'Business Hours',
                'value' => (string) Setting::get('business.hours', ''),
            ],
            'business_address_street' => [
                'label' => 'Address Street',
                'value' => $street,
            ],
            'business_address_city_state_zip' => [
                'label' => 'Address City / State / ZIP',
                'value' => $cityStateZip,
            ],
            'business_address' => [
                'label' => 'Full Address',
                'value' => implode(', ', array_filter([$street, $cityStateZip])),
            ],
        ];
    }

    /**
     * Resolve a tag to its current config value. Returns null for unknown tags.
     */
    public static function resolve(string $tag): ?string
    {
        return self::all()[$tag]['value'] ?? null;
    }
}

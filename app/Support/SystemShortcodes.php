<?php

namespace App\Support;

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
        $street = (string) config('business.address_street', '');
        $cityStateZip = (string) config('business.address_city_state_zip', '');

        return [
            'site_name' => [
                'label' => 'Site Name',
                'value' => (string) config('app.name', ''),
            ],
            'business_phone' => [
                'label' => 'Phone',
                'value' => (string) config('business.phone', ''),
            ],
            'business_email' => [
                'label' => 'Email',
                'value' => (string) config('business.email', ''),
            ],
            'business_url' => [
                'label' => 'Website URL',
                'value' => (string) config('business.url', ''),
            ],
            'business_hours' => [
                'label' => 'Business Hours',
                'value' => (string) config('business.hours', ''),
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

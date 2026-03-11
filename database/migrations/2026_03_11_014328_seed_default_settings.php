<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            ['key' => 'navigation.menus', 'value' => '[]', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'navigation.footer_slugs', 'value' => '[]', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'navigation.show_auth_links', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'navigation.show_account_in_footer', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.logo_url', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.body_font', 'value' => 'instrument-sans', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.heading_font', 'value' => 'instrument-sans', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.section_spacing', 'value' => 'medium', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.alt_rows_enabled', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'branding.alt_rows_start', 'value' => 'even', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.url', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.phone', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.email', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.address_street', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.address_city_state_zip', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'business.hours', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'seo.schema', 'value' => '{"type":"Organization","logo":"","description":"","address":{"city":"","region":"","postal_code":"","country":"US"}}', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'seo.og.default_image', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'seo.twitter.handle', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'layout.active_header', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'layout.active_footer', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'layout.body_classes', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'layout.php_top', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'navigation.menus',
            'navigation.footer_slugs',
            'navigation.show_auth_links',
            'navigation.show_account_in_footer',
            'branding.logo_url',
            'branding.body_font',
            'branding.heading_font',
            'branding.section_spacing',
            'branding.alt_rows_enabled',
            'branding.alt_rows_start',
            'business.url',
            'business.phone',
            'business.email',
            'business.address_street',
            'business.address_city_state_zip',
            'business.hours',
            'seo.schema',
            'seo.og.default_image',
            'seo.twitter.handle',
            'layout.active_header',
            'layout.active_footer',
            'layout.body_classes',
            'layout.php_top',
        ])->delete();
    }
};

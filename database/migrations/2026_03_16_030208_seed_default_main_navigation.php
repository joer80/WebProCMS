<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultMenu = [
            [
                'slug' => 'main-navigation',
                'label' => 'Main Navigation',
                'items' => [
                    ['label' => 'Home', 'route' => 'home', 'active' => true],
                    ['label' => 'Contact', 'route' => 'contact', 'active' => true],
                ],
            ],
        ];

        // Only set if no menus exist yet (safe for existing installs that already have menus configured).
        $current = json_decode(DB::table('settings')->where('key', 'navigation.menus')->value('value') ?? '[]', true);

        if (empty($current)) {
            DB::table('settings')
                ->where('key', 'navigation.menus')
                ->update(['value' => json_encode($defaultMenu), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Only remove if it still matches the default (no user changes).
        $current = json_decode(DB::table('settings')->where('key', 'navigation.menus')->value('value') ?? '[]', true);
        $slugs = array_column($current, 'slug');

        if ($slugs === ['main-navigation']) {
            DB::table('settings')
                ->where('key', 'navigation.menus')
                ->update(['value' => '[]', 'updated_at' => now()]);
        }
    }
};

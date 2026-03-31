<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_pages', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('website_category');
        });

        // Migrate existing single-category values into JSON arrays
        DB::table('design_pages')->get()->each(function ($row) {
            DB::table('design_pages')
                ->where('id', $row->id)
                ->update(['categories' => json_encode(array_filter([$row->website_category]))]);
        });

        Schema::table('design_pages', function (Blueprint $table) {
            $table->dropColumn('website_category');
        });
    }

    public function down(): void
    {
        Schema::table('design_pages', function (Blueprint $table) {
            $table->string('website_category')->nullable()->after('categories');
        });

        // Restore first category value back into the single column
        DB::table('design_pages')->get()->each(function ($row) {
            $categories = json_decode($row->categories, true) ?? [];
            DB::table('design_pages')
                ->where('id', $row->id)
                ->update(['website_category' => $categories[0] ?? null]);
        });

        Schema::table('design_pages', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};

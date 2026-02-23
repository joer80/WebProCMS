<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('meta_title')->nullable()->after('layout');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->boolean('is_noindex')->default(false)->after('meta_description');
            $table->string('og_title')->nullable()->after('is_noindex');
            $table->string('og_description', 320)->nullable()->after('og_title');
            $table->string('og_image', 2048)->nullable()->after('og_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn(['meta_title', 'meta_description', 'is_noindex', 'og_title', 'og_description', 'og_image']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->json('gallery_images')->nullable()->after('cta_buttons');
            $table->unsignedTinyInteger('gallery_columns')->default(4)->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn(['gallery_images', 'gallery_columns']);
        });
    }
};

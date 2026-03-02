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
        Schema::table('content_overrides', function (Blueprint $table) {
            $table->string('page_slug')->nullable()->after('row_slug')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_overrides', function (Blueprint $table) {
            $table->dropIndex(['page_slug']);
            $table->dropColumn('page_slug');
        });
    }
};

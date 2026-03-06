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
        Schema::table('design_pages', function (Blueprint $table) {
            $table->json('row_names')->nullable()->after('sort_order');
            $table->text('blade_code')->nullable()->change();
            $table->text('php_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_pages', function (Blueprint $table) {
            $table->dropColumn('row_names');
        });
    }
};

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
        Schema::table('content_type_definitions', function (Blueprint $table): void {
            $table->boolean('show_dashboard_button')->default(false)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_type_definitions', function (Blueprint $table): void {
            $table->dropColumn('show_dashboard_button');
        });
    }
};

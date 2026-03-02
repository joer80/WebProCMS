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
        Schema::table('design_rows', function (Blueprint $table): void {
            $table->json('schema_fields')->nullable()->after('source_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_rows', function (Blueprint $table): void {
            $table->dropColumn('schema_fields');
        });
    }
};

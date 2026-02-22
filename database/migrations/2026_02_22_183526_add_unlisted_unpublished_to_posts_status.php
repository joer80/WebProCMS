<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published', 'unlisted', 'unpublished') NOT NULL DEFAULT 'draft'");
        } else {
            Schema::table('posts', function (Blueprint $table): void {
                $table->enum('status', ['draft', 'published', 'unlisted', 'unpublished'])->default('draft')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
        } else {
            Schema::table('posts', function (Blueprint $table): void {
                $table->enum('status', ['draft', 'published'])->default('draft')->change();
            });
        }
    }
};

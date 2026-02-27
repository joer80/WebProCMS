<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_overrides', function (Blueprint $table): void {
            $table->id();
            $table->string('row_slug')->index();
            $table->string('key');
            $table->string('type')->default('text');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['row_slug', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_overrides');
    }
};

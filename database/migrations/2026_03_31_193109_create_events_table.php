<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('parent_event_id')->nullable()->index();
            $table->foreign('parent_event_id')->references('id')->on('events')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->string('layout')->default('image-top');
            $table->json('cta_buttons')->nullable();
            $table->json('gallery_images')->nullable();
            $table->integer('gallery_columns')->default(4);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_noindex')->default(false);
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->string('timezone')->nullable();
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('website_url')->nullable();
            $table->string('cost')->nullable();
            $table->boolean('is_repeating')->default(false);
            $table->string('repeat_frequency')->nullable();
            $table->tinyInteger('repeat_interval')->default(1);
            $table->date('repeat_ends_at')->nullable();
            $table->json('repeat_days')->nullable();
            $table->boolean('is_seeded')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

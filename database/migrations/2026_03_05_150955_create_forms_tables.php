<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('notification_email')->nullable();
            $table->boolean('save_submissions')->default(true);
            $table->json('fields');
            $table->boolean('is_seeded')->default(false);
            $table->timestamps();
        });

        Schema::create('form_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->json('data');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        DB::table('forms')->insert([
            'name' => 'Contact Form',
            'notification_email' => null,
            'save_submissions' => true,
            'fields' => json_encode([
                'first_name' => ['enabled' => true, 'required' => true, 'label' => 'First Name'],
                'last_name' => ['enabled' => true, 'required' => true, 'label' => 'Last Name'],
                'email' => ['enabled' => true, 'required' => true, 'label' => 'Email'],
                'phone' => ['enabled' => false, 'required' => false, 'label' => 'Phone Number'],
                'inquiry' => ['enabled' => true, 'required' => true, 'label' => 'Your Inquiry'],
            ]),
            'is_seeded' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('forms');
    }
};

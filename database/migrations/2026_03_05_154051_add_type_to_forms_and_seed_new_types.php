<?php

use App\Enums\FormType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->string('type')->default('contact')->after('name');
        });

        // Update existing seeded Contact Form with field_type on each field
        DB::table('forms')
            ->where('is_seeded', true)
            ->where('name', 'Contact Form')
            ->update([
                'type' => FormType::Contact->value,
                'fields' => json_encode(FormType::Contact->defaultFields()),
            ]);

        // Seed Employment Application form
        DB::table('forms')->insert([
            'name' => 'Employment Application',
            'type' => FormType::JobApplication->value,
            'notification_email' => null,
            'save_submissions' => true,
            'fields' => json_encode(FormType::JobApplication->defaultFields()),
            'is_seeded' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Photo Contest form
        DB::table('forms')->insert([
            'name' => 'Photo Contest',
            'type' => FormType::PhotoContest->value,
            'notification_email' => null,
            'save_submissions' => true,
            'fields' => json_encode(FormType::PhotoContest->defaultFields()),
            'is_seeded' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('forms')->where('is_seeded', true)->whereIn('name', ['Employment Application', 'Photo Contest'])->delete();

        Schema::table('forms', function (Blueprint $table): void {
            $table->dropColumn('type');
        });
    }
};

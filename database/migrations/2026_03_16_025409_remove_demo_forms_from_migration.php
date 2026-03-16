<?php

use App\Enums\FormType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove Employment Application and Photo Contest — now seeded by SeedDemoDataJob only.
        DB::table('forms')
            ->where('is_seeded', true)
            ->whereIn('type', [FormType::JobApplication->value, FormType::PhotoContest->value])
            ->delete();
    }

    public function down(): void
    {
        DB::table('forms')->insertOrIgnore([
            [
                'name' => 'Employment Application',
                'type' => FormType::JobApplication->value,
                'notification_email' => null,
                'save_submissions' => true,
                'fields' => json_encode(FormType::JobApplication->defaultFields()),
                'is_seeded' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Photo Contest',
                'type' => FormType::PhotoContest->value,
                'notification_email' => null,
                'save_submissions' => true,
                'fields' => json_encode(FormType::PhotoContest->defaultFields()),
                'is_seeded' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};

<?php

namespace App\Models;

use App\Enums\FormType;
use App\Enums\SpamProtection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\ResponseCache\Facades\ResponseCache;

class Form extends Model
{
    /** @use HasFactory<\Database\Factories\FormFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'notification_email',
        'save_submissions',
        'spam_protection',
        'fields',
        'is_seeded',
    ];

    protected function casts(): array
    {
        return [
            'type' => FormType::class,
            'spam_protection' => SpamProtection::class,
            'save_submissions' => 'boolean',
            'is_seeded' => 'boolean',
            'fields' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            ResponseCache::clear();
        });

        static::deleted(function (): void {
            ResponseCache::clear();
        });
    }

    /** @return array<string, array{enabled: bool, required: bool, label: string, field_type: string}> */
    public static function defaultFields(): array
    {
        return FormType::Contact->defaultFields();
    }

    /** @return HasMany<FormSubmission, $this> */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}

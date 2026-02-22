<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shortcode extends Model
{
    /** @use HasFactory<\Database\Factories\ShortcodeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'tag',
        'type',
        'content',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'single_text' => 'Single Text/HTML',
            'rich_text' => 'Rich Text/HTML',
            'php_code' => 'PHP Code',
            default => $this->type,
        };
    }
}

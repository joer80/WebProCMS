<?php

namespace App\Models;

use App\Enums\ContentType;
use Illuminate\Database\Eloquent\Model;

class ContentOverride extends Model
{
    protected $fillable = [
        'row_slug',
        'key',
        'type',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContentType::class,
        ];
    }
}

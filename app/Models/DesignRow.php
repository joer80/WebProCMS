<?php

namespace App\Models;

use App\Enums\RowCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignRow extends Model
{
    /** @use HasFactory<\Database\Factories\DesignRowFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'blade_code',
        'php_code',
        'source_file',
        'schema_fields',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category' => RowCategory::class,
            'sort_order' => 'integer',
            'schema_fields' => 'array',
        ];
    }
}

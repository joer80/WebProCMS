<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignPage extends Model
{
    /** @use HasFactory<\Database\Factories\DesignPageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'categories',
        'description',
        'blade_code',
        'php_code',
        'source_file',
        'sort_order',
        'row_names',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'sort_order' => 'integer',
            'row_names' => 'array',
        ];
    }
}

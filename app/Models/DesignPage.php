<?php

namespace App\Models;

use App\Enums\PageCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignPage extends Model
{
    /** @use HasFactory<\Database\Factories\DesignPageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'website_category',
        'description',
        'blade_code',
        'php_code',
        'source_file',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'website_category' => PageCategory::class,
            'sort_order' => 'integer',
        ];
    }
}

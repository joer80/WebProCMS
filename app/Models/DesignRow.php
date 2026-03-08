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
        'source_file',
        'schema_fields',
        'sort_order',
    ];

    /**
     * Read blade code from the source file on disk, stripping the frontmatter comment.
     */
    public function bladeCodeFromFile(): string
    {
        $path = resource_path('design-library/'.$this->source_file);
        $code = file_get_contents($path);

        return preg_replace('/^\{\{--.*?--\}\}\s*/s', '', $code) ?? $code;
    }

    /**
     * Read the @php block content from the source file on disk.
     */
    public function phpCodeFromFile(): string
    {
        $path = resource_path('design-library/'.$this->source_file);
        $code = file_get_contents($path);

        if (preg_match('/\{\{--\s*@php\s*(.*?)\s*--\}\}\s*$/s', $code, $match)) {
            return trim($match[1]);
        }

        return '';
    }

    protected function casts(): array
    {
        return [
            'category' => RowCategory::class,
            'sort_order' => 'integer',
            'schema_fields' => 'array',
        ];
    }
}

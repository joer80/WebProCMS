<?php

namespace App\Jobs;

use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Support\DesignLibraryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IndexDesignLibraryJob implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(DesignLibraryService $service): void
    {
        $rowsBase = resource_path('design-library/rows');
        $pagesBase = resource_path('design-library/pages');

        $this->indexDirectory($service, $rowsBase, 'row');
        $this->indexDirectory($service, $pagesBase, 'page');
    }

    private function indexDirectory(DesignLibraryService $service, string $baseDir, string $type): void
    {
        if (! is_dir($baseDir)) {
            return;
        }

        $grouped = $service->scanDirectory($baseDir);
        $seenSourceFiles = [];

        foreach ($grouped as $category => $files) {
            foreach ($files as $fullPath) {
                $data = $service->parseTemplateFile($fullPath);
                $seenSourceFiles[] = $data['source_file'];

                $attributes = array_merge(
                    ['category' => $category],
                    $type === 'page' ? ['website_category' => $category] : ['category' => $category]
                );

                if ($type === 'row') {
                    DesignRow::updateOrCreate(
                        ['source_file' => $data['source_file']],
                        [
                            'name' => $data['name'],
                            'category' => $category,
                            'description' => $data['description'],
                            'blade_code' => $data['blade_code'],
                            'php_code' => $data['php_code'] ?: null,
                            'sort_order' => $data['sort_order'],
                            'schema_fields' => $data['schema_fields'],
                        ]
                    );
                } else {
                    DesignPage::updateOrCreate(
                        ['source_file' => $data['source_file']],
                        [
                            'name' => $data['name'],
                            'website_category' => $category,
                            'description' => $data['description'],
                            'row_names' => $data['row_names'] ?: null,
                            'blade_code' => null,
                            'php_code' => null,
                            'sort_order' => $data['sort_order'],
                        ]
                    );
                }
            }
        }

        // Remove DB records whose source files no longer exist on disk
        if ($type === 'row') {
            DesignRow::query()
                ->whereNotIn('source_file', $seenSourceFiles)
                ->where('source_file', 'like', 'rows/%')
                ->delete();
        } else {
            DesignPage::query()
                ->whereNotIn('source_file', $seenSourceFiles)
                ->where('source_file', 'like', 'pages/%')
                ->delete();
        }
    }
}

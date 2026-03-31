<?php

namespace App\Jobs;

use App\Models\MediaItem;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToCloudJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public int $tries = 1;

    public function handle(): void
    {
        Setting::set('storage.migration_status', 'running');
        Setting::set('storage.migration_log', '');

        $items = MediaItem::all();
        $total = $items->count();
        $done = 0;
        $errors = [];

        Setting::set('storage.migration_total', $total);
        Setting::set('storage.migration_progress', 0);

        foreach ($items as $item) {
            try {
                if (! Storage::disk('public')->exists($item->path)) {
                    $done++;
                    Setting::set('storage.migration_progress', $done);

                    continue;
                }

                if (! Storage::disk('media')->exists($item->path)) {
                    $stream = Storage::disk('public')->readStream($item->path);

                    Storage::disk('media')->writeStream($item->path, $stream, ['visibility' => 'public']);

                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
            } catch (\Throwable $e) {
                $errors[] = $item->path.': '.$e->getMessage();
            }

            $done++;
            Setting::set('storage.migration_progress', $done);
        }

        if ($errors) {
            Setting::set('storage.migration_status', 'error');
            Setting::set('storage.migration_log', implode("\n", $errors));
        } else {
            Setting::set('storage.migration_status', 'done');
        }
    }
}

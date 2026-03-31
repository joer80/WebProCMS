<?php

namespace App\Jobs;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRepeatingEventChildrenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Event $event) {}

    public function handle(): void
    {
        // Delete existing children
        Event::query()->where('parent_event_id', $this->event->id)->delete();

        $event = $this->event;
        $frequency = $event->repeat_frequency;
        $interval = max(1, (int) $event->repeat_interval);
        $limit = now()->addYears(2);
        $endsAt = $event->repeat_ends_at ? Carbon::parse($event->repeat_ends_at) : null;
        $cutoff = $endsAt && $endsAt->lt($limit) ? $endsAt : $limit;

        $duration = $event->end_date ? $event->start_date->diffInSeconds($event->end_date) : null;

        // Day map for weekly
        $dayMap = ['sun' => 0, 'mon' => 1, 'tue' => 2, 'wed' => 3, 'thu' => 4, 'fri' => 5, 'sat' => 6];
        $repeatDays = $event->repeat_days ?? [];

        $current = $event->start_date->copy();
        $parentSlug = $event->slug;

        // Advance by one period to start generating children
        $current = $this->advance($current, $frequency, $interval);

        $count = 0;
        while ($current->lte($cutoff) && $count < 500) {
            if ($frequency === 'weekly' && ! empty($repeatDays)) {
                // Generate for each matching day in the current week
                $weekStart = $current->copy()->startOfWeek(Carbon::SUNDAY);
                foreach ($repeatDays as $dayStr) {
                    if (! isset($dayMap[$dayStr])) {
                        continue;
                    }
                    $dayDate = $weekStart->copy()->addDays($dayMap[$dayStr]);
                    if ($dayDate->lte($event->start_date) || $dayDate->gt($cutoff)) {
                        continue;
                    }
                    $this->createChild($event, $dayDate, $parentSlug, $duration);
                    $count++;
                }
            } else {
                if ($current->gt($event->start_date)) {
                    $this->createChild($event, $current, $parentSlug, $duration);
                    $count++;
                }
            }

            $current = $this->advance($current, $frequency, $interval);
        }
    }

    private function advance(Carbon $date, string $frequency, int $interval): Carbon
    {
        return match ($frequency) {
            'daily' => $date->copy()->addDays($interval),
            'weekly' => $date->copy()->addWeeks($interval),
            'monthly' => $date->copy()->addMonths($interval),
            'yearly' => $date->copy()->addYears($interval),
            default => $date->copy()->addWeeks($interval),
        };
    }

    private function createChild(Event $parent, Carbon $startDate, string $parentSlug, ?int $duration): void
    {
        $slug = $parentSlug.'-'.$startDate->format('Y-m-d');
        // Ensure unique slug
        $base = $slug;
        $i = 2;
        while (Event::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        Event::query()->withoutEvents(function () use ($parent, $startDate, $slug, $duration): void {
            Event::create([
                'parent_event_id' => $parent->id,
                'title' => $parent->title,
                'slug' => $slug,
                'excerpt' => $parent->excerpt,
                'content' => $parent->content,
                'status' => $parent->status,
                'published_at' => $parent->published_at,
                'featured_image' => $parent->featured_image,
                'featured_image_alt' => $parent->featured_image_alt,
                'layout' => $parent->layout,
                'cta_buttons' => $parent->cta_buttons,
                'gallery_images' => $parent->gallery_images,
                'gallery_columns' => $parent->gallery_columns,
                'start_date' => $startDate,
                'end_date' => $duration ? $startDate->copy()->addSeconds($duration) : null,
                'is_all_day' => $parent->is_all_day,
                'timezone' => $parent->timezone,
                'venue_name' => $parent->venue_name,
                'venue_address' => $parent->venue_address,
                'website_url' => $parent->website_url,
                'cost' => $parent->cost,
                'is_repeating' => false,
                'is_seeded' => false,
                'meta_title' => $parent->meta_title,
                'meta_description' => $parent->meta_description,
                'is_noindex' => $parent->is_noindex,
                'og_title' => $parent->og_title,
                'og_description' => $parent->og_description,
                'og_image' => $parent->og_image,
            ]);
        });
    }
}

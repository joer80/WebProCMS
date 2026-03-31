<?php

namespace App\Jobs;

use App\Enums\FormType;
use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Models\Event;
use App\Models\Form;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Support\ContentTypePageGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedDemoDataJob
{
    /**
     * @param  list<string>  $categories  Which categories to seed. Empty = all.
     */
    public function __construct(public array $categories = []) {}

    public function handle(): void
    {
        if ($this->wants('blog') && ! Post::query()->where('is_seeded', true)->exists()) {
            $exitCode = Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--no-interaction' => true, '--force' => true]);

            if ($exitCode !== 0) {
                throw new \RuntimeException('DatabaseSeeder failed. Check storage/logs/laravel.log for details.');
            }
        }

        if ($this->wants('events')) {
            $this->seedEvents();
        }

        if ($this->wants('locations')) {
            $this->seedLocations();
        }

        if ($this->wants('content_types')) {
            $this->seedContentTypes();
        }

        if ($this->wants('forms')) {
            $this->seedDemoForms();
        }

        if ($this->wants('navigation')) {
            $this->seedDemoNavigation();
        }

        Setting::set('seeding_status', 'complete');
    }

    private function wants(string $category): bool
    {
        return empty($this->categories) || in_array($category, $this->categories);
    }

    public function failed(\Throwable $exception): void
    {
        Setting::set('seeding_status', 'failed');
    }

    private function seedContentTypes(): void
    {
        $types = [
            [
                'name' => 'Minutes',
                'slug' => 'minutes',
                'singular' => 'Minute',
                'icon' => 'document',
                'fields' => [
                    ['label' => 'Meeting Date', 'name' => 'meeting_date', 'type' => 'date', 'options' => '', 'required' => true],
                    ['label' => 'Meeting Notes', 'name' => 'meeting_notes', 'type' => 'richtext_tiptap', 'options' => '', 'required' => false],
                ],
                'sort_order' => 0,
                'show_dashboard_button' => true,
                'is_seeded' => true,
            ],
        ];

        $generator = app(ContentTypePageGenerator::class);

        foreach ($types as $typeData) {
            $typeDef = ContentTypeDefinition::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );

            if (! $typeDef->is_seeded) {
                $typeDef->update(['is_seeded' => true]);
            }

            if (! $generator->hasPages($typeDef->slug)) {
                $generator->generate($typeDef);
            }
        }

        $this->seedContentItems();
    }

    private function seedContentItems(): void
    {
        $items = [
            [
                'type_slug' => 'minutes',
                'data' => [
                    'meeting_date' => '2026-03-10',
                    'meeting_notes' => '<p>Reviewed budget allocations for the upcoming quarter. Approved new vendor contracts and set next meeting for March 24th.</p>',
                ],
                'status' => 'published',
                'published_at' => now(),
            ],
        ];

        foreach ($items as $item) {
            if (! ContentItem::query()->where('type_slug', $item['type_slug'])->exists()) {
                ContentItem::create($item);
            }
        }
    }

    private function seedDemoForms(): void
    {
        $forms = [
            [
                'name' => 'Employment Application',
                'type' => FormType::JobApplication,
            ],
            [
                'name' => 'Photo Contest',
                'type' => FormType::PhotoContest,
            ],
        ];

        foreach ($forms as $formData) {
            Form::firstOrCreate(
                ['type' => $formData['type']->value, 'is_seeded' => true],
                [
                    'name' => $formData['name'],
                    'notification_email' => null,
                    'save_submissions' => true,
                    'fields' => $formData['type']->defaultFields(),
                    'is_seeded' => true,
                ]
            );
        }
    }

    private function seedDemoNavigation(): void
    {
        $menus = Setting::get('navigation.menus', []);
        $seededRoutes = Setting::get('navigation.seeded_routes', []);

        $demoItems = [
            ['label' => 'About', 'route' => 'about', 'active' => true],
            ['label' => 'Blog', 'route' => 'blog.index', 'active' => true],
        ];

        $menus = array_map(function (array $menu) use ($demoItems, &$seededRoutes): array {
            if ($menu['slug'] !== 'main-navigation') {
                return $menu;
            }

            $existingRoutes = array_column($menu['items'], 'route');

            foreach ($demoItems as $item) {
                if (! in_array($item['route'], $existingRoutes)) {
                    // Insert before the last item (Contact) so order is Home, About, Blog, Contact.
                    array_splice($menu['items'], -1, 0, [$item]);

                    if (! in_array($item['route'], $seededRoutes)) {
                        $seededRoutes[] = $item['route'];
                    }
                }
            }

            return $menu;
        }, $menus);

        Setting::set('navigation.menus', $menus);
        Setting::set('navigation.seeded_routes', $seededRoutes);
    }

    private function seedEvents(): void
    {
        $events = [
            [
                'title' => 'Annual Community Gala',
                'excerpt' => 'Join us for an elegant evening celebrating our community\'s achievements with dinner, awards, and live entertainment.',
                'content' => '<p>Our Annual Community Gala is the highlight of the year — an elegant evening bringing together neighbors, local leaders, and community supporters for dinner, awards, and live entertainment. Formal attire requested.</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->addDays(14)->setTime(18, 0),
                'end_date' => now()->addDays(14)->setTime(22, 0),
                'is_all_day' => false,
                'venue_name' => 'The Grand Ballroom',
                'venue_address' => '500 Main Street, Downtown',
                'cost' => '$75 per person',
                'is_seeded' => true,
            ],
            [
                'title' => 'Spring Farmers Market',
                'excerpt' => 'Shop fresh local produce, artisan goods, and handmade crafts every Saturday morning through the spring season.',
                'content' => '<p>The Spring Farmers Market returns every Saturday morning from April through June. Find fresh local produce, artisan breads, handmade crafts, and live music in the town square. Free admission — bring a tote bag!</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->addDays(3)->setTime(8, 0),
                'end_date' => now()->addDays(3)->setTime(13, 0),
                'is_all_day' => false,
                'venue_name' => 'Town Square',
                'venue_address' => '1 Central Plaza',
                'cost' => 'Free',
                'is_repeating' => true,
                'repeat_frequency' => 'weekly',
                'repeat_interval' => 1,
                'repeat_days' => ['sat'],
                'repeat_ends_at' => now()->addMonths(3)->format('Y-m-d'),
                'is_seeded' => true,
            ],
            [
                'title' => 'Volunteer Orientation',
                'excerpt' => 'New to volunteering with us? Attend this orientation to learn how you can make a difference in your community.',
                'content' => '<p>Welcome to our volunteer family! This one-hour orientation covers our programs, volunteer opportunities, scheduling, and how to get started. Light refreshments provided. No RSVP required.</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->addDays(7)->setTime(10, 0),
                'end_date' => now()->addDays(7)->setTime(11, 0),
                'is_all_day' => false,
                'venue_name' => 'Community Center — Room 101',
                'venue_address' => '200 Oak Avenue',
                'cost' => 'Free',
                'is_seeded' => true,
            ],
            [
                'title' => 'Youth Soccer Tournament',
                'excerpt' => 'Cheer on your favorite young athletes at our annual youth soccer tournament featuring teams from across the region.',
                'content' => '<p>The annual Youth Soccer Tournament brings together teams from across the region for a weekend of exciting competition. Open to spectators of all ages — concessions available on-site. Come support our local athletes!</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(22),
                'is_all_day' => true,
                'venue_name' => 'Riverside Sports Complex',
                'venue_address' => '900 River Road',
                'cost' => 'Free admission',
                'is_seeded' => true,
            ],
            [
                'title' => 'Winter Holiday Parade',
                'excerpt' => 'A beloved community tradition — floats, marching bands, and a visit from Santa himself down Main Street.',
                'content' => '<p>Don\'t miss the annual Winter Holiday Parade! Floats, marching bands, local school groups, and a special appearance by Santa Claus will make their way down Main Street. Arrive early for a great viewing spot. Hot cocoa stands will line the route.</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->addDays(45)->setTime(11, 0),
                'end_date' => now()->addDays(45)->setTime(13, 0),
                'is_all_day' => false,
                'venue_name' => 'Main Street',
                'venue_address' => 'Starting at First & Main',
                'cost' => 'Free',
                'is_seeded' => true,
            ],
            [
                'title' => 'Board of Directors Meeting — March',
                'excerpt' => 'Monthly open board meeting. Community members are welcome to attend and observe.',
                'content' => '<p>The monthly Board of Directors meeting is open to all community members. Agenda items will be posted on the website 48 hours in advance. Public comment period is available at the end of the meeting.</p>',
                'status' => 'published',
                'published_at' => now(),
                'start_date' => now()->subDays(10)->setTime(9, 0),
                'end_date' => now()->subDays(10)->setTime(11, 0),
                'is_all_day' => false,
                'venue_name' => 'City Hall — Council Chambers',
                'venue_address' => '1 Government Drive',
                'cost' => 'Free',
                'is_seeded' => true,
            ],
        ];

        $imagePaths = $this->downloadEventImages(count($events));

        foreach (array_values($events) as $index => $eventData) {
            if (! Event::query()->where('title', $eventData['title'])->where('is_seeded', true)->exists()) {
                $eventData['featured_image'] = $imagePaths[$index] ?? null;
                Event::create($eventData);
            }
        }
    }

    /** @return array<int, string> */
    private function downloadEventImages(int $count): array
    {
        Storage::disk('public')->makeDirectory('events');

        $paths = [];

        // Start at picsum ID 50 to get different images than the blog posts (which use IDs 1–20)
        for ($i = 0; $i < $count; $i++) {
            $response = Http::get('https://picsum.photos/id/'.($i + 50).'/1200/630');

            if ($response->successful()) {
                $path = 'events/'.Str::random(40).'.jpg';
                Storage::disk('public')->put($path, $response->body());
                $paths[] = $path;
            }
        }

        return $paths;
    }

    private function seedLocations(): void
    {
        $locations = [
            [
                'name' => 'GetRows Austin',
                'address' => '100 Congress Avenue',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '(512) 555-0101',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows San Antonio',
                'address' => '200 E Market Street',
                'city' => 'San Antonio',
                'state' => 'TX',
                'zip' => '78205',
                'phone' => '(210) 555-0202',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Memphis',
                'address' => '350 Beale Street',
                'city' => 'Memphis',
                'state' => 'TN',
                'zip' => '38103',
                'phone' => '(901) 555-0303',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Tulsa',
                'address' => '75 W 5th Street',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74103',
                'phone' => '(918) 555-0404',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Shreveport',
                'address' => '500 Texas Street',
                'city' => 'Shreveport',
                'state' => 'LA',
                'zip' => '71101',
                'phone' => '(318) 555-0505',
                'is_seeded' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location);
        }
    }
}

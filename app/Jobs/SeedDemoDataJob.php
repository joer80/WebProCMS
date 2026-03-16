<?php

namespace App\Jobs;

use App\Enums\FormType;
use App\Models\ContentItem;
use App\Models\ContentTypeDefinition;
use App\Models\Form;
use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use App\Support\ContentTypePageGenerator;
use Illuminate\Support\Facades\Artisan;

class SeedDemoDataJob
{
    public function handle(): void
    {
        if (! Post::query()->where('is_seeded', true)->exists()) {
            $exitCode = Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--no-interaction' => true, '--force' => true]);

            if ($exitCode !== 0) {
                throw new \RuntimeException('DatabaseSeeder failed. Check storage/logs/laravel.log for details.');
            }
        }

        $this->seedLocations();
        $this->seedContentTypes();
        $this->seedDemoForms();
        $this->seedDemoNavigation();

        Setting::set('seeding_status', 'complete');
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

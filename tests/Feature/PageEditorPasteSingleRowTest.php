<?php

use App\Enums\Role;
use App\Models\ContentOverride;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-paste-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:hero-abc123 --}}
<section>Hero content</section>
{{-- ROW:end:hero-abc123 --}}
</div>
BLADE);
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }
});

it('pastes a single row and appends it to the page rows', function (): void {
    $row = [
        'slug' => 'hero:abc123',
        'name' => 'Hero',
        'blade' => '<section slug="hero:abc123">Hero</section>',
        'shared' => false,
        'hidden' => false,
    ];

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('pasteSingleRow', $row)
        ->assertDispatched('notify');

    $rows = $component->get('rows');
    expect($rows)->toHaveCount(2);
    expect($rows[1]['name'])->toBe('Hero');
    expect($rows[1]['slug'])->not->toBe('hero:abc123');
    expect($rows[1]['shared'])->toBeFalse();
});

it('clones content overrides to the new slug when pasting a single row', function (): void {
    ContentOverride::create([
        'row_slug' => 'hero:abc123',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Custom Headline',
    ]);

    $row = [
        'slug' => 'hero:abc123',
        'name' => 'Hero',
        'blade' => '<section slug="hero:abc123">Hero</section>',
        'shared' => false,
        'hidden' => false,
    ];

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('pasteSingleRow', $row);

    $rows = $component->get('rows');
    $newSlug = $rows[1]['slug'];

    // Original override still exists, and a clone was created for the new slug
    expect(ContentOverride::query()->where('row_slug', 'hero:abc123')->where('key', 'headline')->exists())->toBeTrue();
    expect(ContentOverride::query()->where('row_slug', $newSlug)->where('key', 'headline')->where('value', 'Custom Headline')->exists())->toBeTrue();
});

it('forces shared to false when pasting a shared row', function (): void {
    $row = [
        'slug' => 'hero:abc123',
        'name' => 'Shared Hero',
        'blade' => '<section slug="hero:abc123">Hero</section>',
        'shared' => true,
        'hidden' => false,
    ];

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('pasteSingleRow', $row);

    $rows = $component->get('rows');
    expect($rows[1]['shared'])->toBeFalse();
});

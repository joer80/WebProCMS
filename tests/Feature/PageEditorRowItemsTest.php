<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-items-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    $rowSlug = 'hero-test:abc123';

    file_put_contents($this->tempFullPath, <<<BLADE
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:{$rowSlug} --}}
<x-dl.section slug="{$rowSlug}" default-section-classes="py-10" default-container-classes="max-w-6xl mx-auto">
{{-- @dl-item:heading:headline:Heading --}}
<x-dl.heading slug="{$rowSlug}" prefix="headline" default="Heading" default-tag="h2" default-classes="text-4xl font-bold" />
{{-- /@dl-item --}}
{{-- @dl-item:subheadline:subheadline1:Subheadline --}}
<x-dl.subheadline slug="{$rowSlug}" prefix="subheadline1" default="Subheadline" default-classes="text-lg" />
{{-- /@dl-item --}}
{{-- @dl-item:button:button1:Button --}}
<x-dl.button slug="{$rowSlug}" prefix="button1" default="Click me" default-url="#" default-classes="px-6 py-3 bg-primary text-white" />
{{-- /@dl-item --}}
</x-dl.section>
{{-- ROW:end:{$rowSlug} --}}
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

it('reorders items within a row moving first to last', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('reorderItems', 0, 2);

    $blade = $component->get('rows')[0]['blade'];

    $headingPos = strpos($blade, '@dl-item:heading');
    $buttonPos = strpos($blade, '@dl-item:button');

    expect($buttonPos)->toBeLessThan($headingPos);
});

it('reorders items within a row moving last to first', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('reorderItems', 2, 0);

    $blade = $component->get('rows')[0]['blade'];

    $headingPos = strpos($blade, '@dl-item:heading');
    $buttonPos = strpos($blade, '@dl-item:button');

    expect($buttonPos)->toBeLessThan($headingPos);
});

it('does nothing when reordering an item to the same position', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath]);

    $bladeBefore = $component->get('rows')[0]['blade'];

    $component->call('openContentEditor', 0)->call('reorderItems', 1, 1);

    $bladeAfter = $component->get('rows')[0]['blade'];

    expect($bladeAfter)->toBe($bladeBefore);
});

it('inserts a new item above an existing item', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('openItemPickerAbove', 0) // insert above first item (heading)
        ->call('addItemToRow', 'subheadline');

    $blade = $component->get('rows')[0]['blade'];

    $newSubPos = strpos($blade, '@dl-item:subheadline:subheadline:');
    $headingPos = strpos($blade, '@dl-item:heading');

    expect($newSubPos)->toBeLessThan($headingPos);
});

it('inserts a new item below an existing item', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->call('openContentEditor', 0)
        ->call('openItemPickerBelow', 0) // insert below first item (heading)
        ->call('addItemToRow', 'subheadline');

    $blade = $component->get('rows')[0]['blade'];

    $headingPos = strpos($blade, '@dl-item:heading');
    $newSubPos = strpos($blade, '@dl-item:subheadline:subheadline:');
    $origSubPos = strpos($blade, '@dl-item:subheadline:subheadline1');

    expect($newSubPos)->toBeGreaterThan($headingPos);
    expect($newSubPos)->toBeLessThan($origSubPos);
});

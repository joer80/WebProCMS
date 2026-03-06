<?php

use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->tempRelativePath = 'pages/test-bundle-'.uniqid().'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php
use Livewire\Component;

new class extends Component {}; ?>

{{-- ROW:start:hero-aaa111 --}}
<section>Hero content</section>
{{-- ROW:end:hero-aaa111 --}}
BLADE);
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    $previewDir = resource_path('views/pages/_editor-previews');

    foreach (glob($previewDir.'/*.blade.php') ?: [] as $file) {
        unlink($file);
    }
});

it('inserts all rows from a page bundle at the given index', function (): void {
    $user = User::factory()->create();

    $rowA = DesignRow::factory()->create([
        'name' => 'Hero Row',
        'source_file' => 'rows/hero/bundle-hero-'.uniqid().'.blade.php',
        'blade_code' => '<section>Hero</section>',
    ]);

    $rowB = DesignRow::factory()->create([
        'name' => 'Features Row',
        'source_file' => 'rows/features/bundle-features-'.uniqid().'.blade.php',
        'blade_code' => '<section>Features</section>',
    ]);

    $templateA = basename($rowA->source_file, '.blade.php');
    $templateB = basename($rowB->source_file, '.blade.php');

    $bundle = DesignPage::factory()->create([
        'row_names' => [$templateA, $templateB],
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('insertPageBundle', $bundle->id, 0);

    $rows = $component->get('rows');

    expect($rows)->toHaveCount(3);
    expect($rows[0]['name'])->toBe('Hero Row');
    expect($rows[1]['name'])->toBe('Features Row');
    expect($rows[2]['slug'])->toBe('hero-aaa111');
    expect($component->get('isDirty'))->toBeTrue();
});

it('skips unknown row names gracefully', function (): void {
    $user = User::factory()->create();

    $realRow = DesignRow::factory()->create([
        'name' => 'Real Row',
        'source_file' => 'rows/hero/bundle-real-'.uniqid().'.blade.php',
        'blade_code' => '<section>Real</section>',
    ]);

    $bundle = DesignPage::factory()->create([
        'row_names' => [basename($realRow->source_file, '.blade.php'), 'nonexistent-row'],
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('insertPageBundle', $bundle->id, 0);

    $rows = $component->get('rows');

    expect($rows)->toHaveCount(2);
    expect($rows[0]['name'])->toBe('Real Row');
});

it('does nothing when bundle has no row_names', function (): void {
    $user = User::factory()->create();

    $bundle = DesignPage::factory()->create([
        'row_names' => null,
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('insertPageBundle', $bundle->id, 0);

    expect($component->get('rows'))->toHaveCount(1);
});

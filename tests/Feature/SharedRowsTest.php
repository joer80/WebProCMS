<?php

use App\Enums\Role;
use App\Models\ContentOverride;
use App\Models\SharedRow;
use App\Models\User;
use App\Support\VoltFileService;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();
    $this->originalRoutes = file_get_contents(base_path('routes/web.php'));

    $this->tempRelativePath = 'pages/test-shared-'.uniqid().'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php
use Livewire\Component;

new class extends Component {}; ?>

{{-- ROW:start:hero-aaa111 --}}
<section>Hero content</section>
{{-- ROW:end:hero-aaa111 --}}

{{-- ROW:start:simple-cta:XYZ789 --}}
<section>CTA content</section>
{{-- ROW:end:simple-cta:XYZ789 --}}
BLADE);
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    file_put_contents(base_path('routes/web.php'), $this->originalRoutes);

    // Clean up any shared-rows files created during tests.
    foreach (glob(resource_path('views/shared-rows/test-*.blade.php')) ?: [] as $file) {
        unlink($file);
    }

    foreach (glob(resource_path('views/shared-rows/simple-cta-*.blade.php')) ?: [] as $file) {
        unlink($file);
    }

    $previewDir = resource_path('views/pages/_editor-previews');

    foreach (glob($previewDir.'/*.blade.php') ?: [] as $file) {
        unlink($file);
    }
});

it('makeRowShared creates a SharedRow record and shared-rows file', function (): void {
    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('makeRowShared', 1);

    $sharedFilePath = resource_path('views/shared-rows/simple-cta-XYZ789.blade.php');

    expect(SharedRow::where('slug', 'simple-cta:XYZ789')->exists())->toBeTrue()
        ->and(file_exists($sharedFilePath))->toBeTrue()
        ->and($component->get('rows')[1]['shared'])->toBeTrue()
        ->and($component->get('rows')[1]['blade'])->toBe("@include('shared-rows.simple-cta-XYZ789')")
        ->and($component->get('isDirty'))->toBeTrue();
});

it('makeRowShared updates existing ContentOverride records to page_slug null', function (): void {
    ContentOverride::create([
        'row_slug' => 'simple-cta:XYZ789',
        'page_slug' => 'some-page',
        'key' => 'headline',
        'type' => 'text',
        'value' => 'My CTA',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('makeRowShared', 1);

    expect(ContentOverride::where('row_slug', 'simple-cta:XYZ789')->whereNull('page_slug')->exists())->toBeTrue()
        ->and(ContentOverride::where('row_slug', 'simple-cta:XYZ789')->whereNotNull('page_slug')->exists())->toBeFalse();
});

it('insertSharedRow adds the shared row to the page', function (): void {
    SharedRow::create(['slug' => 'simple-cta:XYZ789', 'name' => 'Simple CTA']);
    file_put_contents(resource_path('views/shared-rows/simple-cta-XYZ789.blade.php'), '<section>CTA</section>');

    $component = Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('insertSharedRow', 'simple-cta:XYZ789', 0);

    $rows = $component->get('rows');

    expect($rows[0]['slug'])->toBe('simple-cta:XYZ789')
        ->and($rows[0]['shared'])->toBeTrue()
        ->and($rows[0]['blade'])->toBe("@include('shared-rows.simple-cta-XYZ789')")
        ->and($component->get('isDirty'))->toBeTrue();
});

it('removeRow does not delete ContentOverride records for shared rows', function (): void {
    SharedRow::create(['slug' => 'simple-cta:XYZ789', 'name' => 'Simple CTA']);
    file_put_contents(resource_path('views/shared-rows/simple-cta-XYZ789.blade.php'), '<section>CTA</section>');

    ContentOverride::create([
        'row_slug' => 'simple-cta:XYZ789',
        'page_slug' => null,
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Shared CTA Headline',
    ]);

    // Rewrite the temp file with a shared row marker so the editor loads it as shared.
    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php
use Livewire\Component;

new class extends Component {}; ?>

{{-- ROW:start:simple-cta:XYZ789:shared=1 --}}
@include('shared-rows.simple-cta-XYZ789')
{{-- ROW:end:simple-cta:XYZ789 --}}
BLADE);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('removeRow', 0);

    expect(ContentOverride::where('row_slug', 'simple-cta:XYZ789')->exists())->toBeTrue();
});

it('removeRow deletes ContentOverride records for non-shared rows', function (): void {
    ContentOverride::create([
        'row_slug' => 'hero-aaa111',
        'page_slug' => null,
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Hero Headline',
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('removeRow', 0);

    expect(ContentOverride::where('row_slug', 'hero-aaa111')->exists())->toBeFalse();
});

it('deletePage does not delete ContentOverride records for shared rows', function (): void {
    SharedRow::create(['slug' => 'simple-cta:XYZ789', 'name' => 'Simple CTA']);
    file_put_contents(resource_path('views/shared-rows/simple-cta-XYZ789.blade.php'), '<section>CTA</section>');

    ContentOverride::create([
        'row_slug' => 'simple-cta:XYZ789',
        'page_slug' => null,
        'key' => 'headline',
        'type' => 'text',
        'value' => 'Shared CTA',
    ]);

    // Rewrite with shared row marker and a real page slug so deletePage can target it.
    $pageSlug = 'test-delete-'.uniqid();
    $pageRelativePath = 'pages/⚡'.$pageSlug.'.blade.php';
    $pageFullPath = resource_path('views/'.$pageRelativePath);

    file_put_contents($pageFullPath, <<<'BLADE'
<?php
use Livewire\Component;

new class extends Component {}; ?>

{{-- ROW:start:simple-cta:XYZ789:shared=1 --}}
@include('shared-rows.simple-cta-XYZ789')
{{-- ROW:end:simple-cta:XYZ789 --}}
BLADE);

    (new VoltFileService)->deletePage($pageRelativePath);

    expect(ContentOverride::where('row_slug', 'simple-cta:XYZ789')->exists())->toBeTrue();
});

it('clonePage preserves shared row slugs without remapping', function (): void {
    SharedRow::create(['slug' => 'simple-cta:XYZ789', 'name' => 'Simple CTA']);
    file_put_contents(resource_path('views/shared-rows/simple-cta-XYZ789.blade.php'), '<section>CTA</section>');

    $sourceRelativePath = 'pages/⚡test-clone-source-'.uniqid().'.blade.php';
    $sourceFullPath = resource_path('views/'.$sourceRelativePath);

    file_put_contents($sourceFullPath, <<<'BLADE'
<?php
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Source')] class extends Component {}; ?>

<div>
{{-- ROW:start:simple-cta:XYZ789:shared=1 --}}
@include('shared-rows.simple-cta-XYZ789')
{{-- ROW:end:simple-cta:XYZ789 --}}
</div>
BLADE);

    $newSlug = 'test-clone-dest-'.uniqid();
    $newRelativePath = 'pages/⚡'.$newSlug.'.blade.php';
    $newFullPath = resource_path('views/'.$newRelativePath);

    (new VoltFileService)->clonePage($newSlug, 'Clone Dest', $sourceRelativePath);

    $contents = file_get_contents($newFullPath);

    expect($contents)->toContain('simple-cta:XYZ789');

    unlink($sourceFullPath);

    if (file_exists($newFullPath)) {
        unlink($newFullPath);
    }
});

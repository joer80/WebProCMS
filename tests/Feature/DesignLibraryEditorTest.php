<?php

use App\Enums\Role;
use App\Models\DesignRow;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->tempRelativePath = 'pages/test-editor-'.uniqid().'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);

    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php
use Livewire\Component;

new class extends Component {}; ?>

{{-- ROW:start:hero-aaa111 --}}
<section>Hero content</section>
{{-- ROW:end:hero-aaa111 --}}

{{-- ROW:start:cta-bbb222 --}}
<section>CTA content</section>
{{-- ROW:end:cta-bbb222 --}}
BLADE);
});

afterEach(function (): void {
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    if (isset($this->libraryTempPath) && file_exists($this->libraryTempPath)) {
        unlink($this->libraryTempPath);
    }

    $previewDir = resource_path('views/pages/_editor-previews');

    foreach (glob($previewDir.'/*.blade.php') ?: [] as $file) {
        unlink($file);
    }
});

it('redirects unauthenticated users from the editor', function (): void {
    $this->get(route('dashboard.design-library.editor'))->assertRedirect(route('login'));
});

it('redirects standard users from the editor', function (): void {
    $user = User::factory()->withRole(Role::Standard)->create();

    $this->actingAs($user)
        ->get(route('dashboard.design-library.editor'))
        ->assertRedirect(route('dashboard'));
});

it('allows manager users to view the editor', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.design-library.editor'))
        ->assertOk();
});

it('loads and parses a volt file into rows', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath);

    expect($component->get('rows'))->toHaveCount(2);
    expect($component->get('rows')[0]['slug'])->toBe('hero-aaa111');
    expect($component->get('rows')[1]['slug'])->toBe('cta-bbb222');
});

it('wraps legacy content without row markers in a legacy row', function (): void {
    $legacyRelativePath = 'pages/test-legacy-'.uniqid().'.blade.php';
    $legacyFullPath = resource_path('views/'.$legacyRelativePath);

    file_put_contents($legacyFullPath, "<?php\nnew class extends \\Livewire\\Component {}; ?>\n\n<div>Legacy content here</div>");

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $legacyRelativePath);

    expect($component->get('rows'))->toHaveCount(1);
    expect($component->get('rows')[0]['name'])->toBe('Existing Content');

    unlink($legacyFullPath);
});

it('can move a row up', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowUp', 1);

    expect($component->get('rows')[0]['slug'])->toBe('cta-bbb222');
    expect($component->get('rows')[1]['slug'])->toBe('hero-aaa111');
    expect($component->get('isDirty'))->toBeTrue();
});

it('does not move a row up when already at the top', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowUp', 0);

    expect($component->get('rows')[0]['slug'])->toBe('hero-aaa111');
});

it('can move a row down', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowDown', 0);

    expect($component->get('rows')[0]['slug'])->toBe('cta-bbb222');
    expect($component->get('rows')[1]['slug'])->toBe('hero-aaa111');
    expect($component->get('isDirty'))->toBeTrue();
});

it('does not move a row down when already at the bottom', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowDown', 1);

    expect($component->get('rows')[1]['slug'])->toBe('cta-bbb222');
});

it('can remove a row', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('removeRow', 0);

    expect($component->get('rows'))->toHaveCount(1);
    expect($component->get('rows')[0]['slug'])->toBe('cta-bbb222');
    expect($component->get('isDirty'))->toBeTrue();
});

it('can insert a row from the library at a given index', function (): void {
    $user = User::factory()->create();
    $designRow = DesignRow::factory()->create([
        'name' => 'Library Row',
        'blade_code' => '<section>From Library</section>',
    ]);

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('insertRow', $designRow->id, 0);

    expect($component->get('rows'))->toHaveCount(3);
    expect($component->get('rows')[0]['name'])->toBe('Library Row');
    expect($component->get('isDirty'))->toBeTrue();
});

it('saves file content to disk', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowDown', 0)
        ->call('saveFile');

    $saved = file_get_contents($this->tempFullPath);
    expect($saved)->toContain('ROW:start:cta-bbb222');
    expect($saved)->toContain('ROW:start:hero-aaa111');
    expect(strpos($saved, 'cta-bbb222'))->toBeLessThan(strpos($saved, 'hero-aaa111'));
});

it('marks file as clean after saving', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowDown', 0)
        ->call('saveFile');

    expect($component->get('isDirty'))->toBeFalse();
});

it('syncs the library when the insert drawer is opened', function (): void {
    $user = User::factory()->create();

    $sourceFile = 'rows/hero/test-drawer-'.uniqid().'.blade.php';
    $this->libraryTempPath = resource_path('design-library/'.$sourceFile);
    file_put_contents($this->libraryTempPath, "{{--\n@name Drawer Sync Row\n--}}\n<section>Drawer Test</section>");

    expect(DesignRow::where('source_file', $sourceFile)->exists())->toBeFalse();

    Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('openLibraryDrawer', 0);

    expect(DesignRow::where('source_file', $sourceFile)->exists())->toBeTrue();
});

it('discards changes by reloading from disk', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test('pages::dashboard.pages.editor')
        ->call('loadFile', $this->tempRelativePath)
        ->call('moveRowDown', 0);

    expect($component->get('isDirty'))->toBeTrue();

    $component->call('discardChanges');

    expect($component->get('isDirty'))->toBeFalse();
    expect($component->get('rows')[0]['slug'])->toBe('hero-aaa111');
});

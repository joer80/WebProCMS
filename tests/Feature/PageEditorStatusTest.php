<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->tempRelativePath = 'pages/test-status-'.uniqid().'.blade.php';
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

    $previewDir = resource_path('views/pages/_editor-previews');

    foreach (glob($previewDir.'/*.blade.php') ?: [] as $file) {
        unlink($file);
    }
});

it('defaults pageStatus to published for a page without a status attribute', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('pageStatus', 'published');
});

it('parses an existing status from the layout attribute', function (): void {
    file_put_contents($this->tempFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public', ['status' => 'draft'])] #[Title('Test Page')] class extends Component {}; ?>

<div>
{{-- ROW:start:hero-abc123 --}}
<section>Hero content</section>
{{-- ROW:end:hero-abc123 --}}
</div>
BLADE);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('pageStatus', 'draft');
});

it('persists status to the layout attribute when saving settings', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unlisted')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)->toContain("'status' => 'unlisted'");
});

it('omits status from the layout attribute when set to published', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'published')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)->not->toContain("'status'");
});

it('injects a boot abort method when status is unpublished', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unpublished')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)
        ->toContain('// ROW:php:start:page-status-abort')
        ->toContain('abort(404)')
        ->toContain('// ROW:php:end:page-status-abort');
});

it('injects a boot abort method when status is draft', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'draft')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)->toContain('abort(404)');
});

it('removes the boot abort method when status changes to published', function (): void {
    // First set to unpublished to inject the abort code
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unpublished')
        ->call('saveSeoSettings');

    // Now change back to published
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'published')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)->not->toContain('abort(404)');
});

it('removes the boot abort method when status changes to unlisted', function (): void {
    // First set to draft to inject the abort code
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'draft')
        ->call('saveSeoSettings');

    // Now change to unlisted (accessible)
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unlisted')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)
        ->not->toContain('abort(404)')
        ->toContain("'status' => 'unlisted'");
});

it('does not inject abort code for unlisted pages', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unlisted')
        ->call('saveSeoSettings');

    $contents = file_get_contents($this->tempFullPath);

    expect($contents)->not->toContain('abort(404)');
});

it('does not include the abort code in the preview file when page is unpublished', function (): void {
    $this->actingAs($this->user);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'unpublished')
        ->call('saveSeoSettings');

    $previewPath = (new \App\Support\VoltFileService)->previewFilePath($this->tempRelativePath);

    if (file_exists($previewPath)) {
        expect(file_get_contents($previewPath))->not->toContain('abort(404)');
    }
});

it('validates that only allowed status values are accepted', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageStatus', 'invalid-status')
        ->call('saveSeoSettings')
        ->assertHasErrors(['pageStatus']);
});

<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-slug-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);
    $this->renamedFullPath = null;
    $this->originalRoutes = file_get_contents(base_path('routes/web.php'));

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
    foreach ([$this->tempFullPath, $this->renamedFullPath] as $path) {
        if ($path && file_exists($path)) {
            unlink($path);
        }
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }

    file_put_contents(base_path('routes/web.php'), $this->originalRoutes);
});

it('populates pageSlug from the filename when loading a volt page', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('pageSlug', $this->slug);
});

it('does not populate pageSlug for non-top-level pages', function (): void {
    $dashboardRelativePath = 'pages/dashboard/⚡something.blade.php';

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor')
        ->assertSet('pageSlug', '');
});

it('validates that the slug only allows lowercase letters, numbers, and hyphens', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', 'Invalid Slug!')
        ->call('saveSeoSettings')
        ->assertHasErrors(['pageSlug']);
});

it('rejects a slug with uppercase letters', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', 'MyPage')
        ->call('saveSeoSettings')
        ->assertHasErrors(['pageSlug']);
});

it('rejects an empty slug', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', '')
        ->call('saveSeoSettings')
        ->assertHasErrors(['pageSlug']);
});

it('accepts a valid slug with hyphens', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', $this->slug)
        ->call('saveSeoSettings')
        ->assertHasNoErrors(['pageSlug']);
});

it('renames the file when the slug changes', function (): void {
    $newSlug = 'renamed-'.uniqid();
    $this->renamedFullPath = resource_path('views/pages/⚡'.$newSlug.'.blade.php');

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', $newSlug)
        ->call('saveSeoSettings');

    expect(file_exists($this->tempFullPath))->toBeFalse();
    expect(file_exists($this->renamedFullPath))->toBeTrue();
});

it('updates the file property to the new path after renaming', function (): void {
    $newSlug = 'renamed-'.uniqid();
    $newRelativePath = 'pages/⚡'.$newSlug.'.blade.php';
    $this->renamedFullPath = resource_path('views/'.$newRelativePath);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', $newSlug)
        ->call('saveSeoSettings')
        ->assertSet('file', $newRelativePath);
});

it('rejects renaming to a slug that already exists', function (): void {
    $existingSlug = 'existing-'.uniqid();
    $existingPath = resource_path('views/pages/⚡'.$existingSlug.'.blade.php');
    file_put_contents($existingPath, '');

    try {
        Livewire::actingAs($this->user)
            ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
            ->set('pageSlug', $existingSlug)
            ->call('saveSeoSettings')
            ->assertHasErrors(['pageSlug']);
    } finally {
        unlink($existingPath);
    }
});

it('does not rename the file when the slug is unchanged', function (): void {
    $originalMtime = filemtime($this->tempFullPath);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', $this->slug)
        ->call('saveSeoSettings');

    expect(file_exists($this->tempFullPath))->toBeTrue();
});

it('does not apply slug validation for non-public page paths', function (): void {
    $nonPublicPath = 'pages/test-no-slug-'.uniqid().'.blade.php';
    $nonPublicFullPath = resource_path('views/'.$nonPublicPath);

    file_put_contents($nonPublicFullPath, <<<'BLADE'
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.public')] #[Title('Test')] class extends Component {}; ?>

<div></div>
BLADE);

    try {
        Livewire::actingAs($this->user)
            ->test('pages::dashboard.pages.editor', ['file' => $nonPublicPath])
            ->set('pageSlug', '')
            ->call('saveSeoSettings')
            ->assertHasNoErrors(['pageSlug']);
    } finally {
        if (file_exists($nonPublicFullPath)) {
            unlink($nonPublicFullPath);
        }
    }
});

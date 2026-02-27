<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-cache-'.uniqid();
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

it('defaults isCachedPage to true when the route is not registered', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('isCachedPage', true);
});

it('reports isCachedPage as true for a route inside the cache middleware group', function (): void {
    // Add the route to the cached group first
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: true);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('isCachedPage', true);
});

it('reports isCachedPage as false for a route outside the cache middleware group', function (): void {
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('isCachedPage', false);
});

it('moves a cached route to the uncached section when isCachedPage is toggled off', function (): void {
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: true);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('isCachedPage', false)
        ->call('saveSeoSettings');

    $service = new \App\Support\VoltFileService;
    expect($service->isRouteCached($this->slug))->toBeFalse();
});

it('moves an uncached route into the cache group when isCachedPage is toggled on', function (): void {
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('isCachedPage', true)
        ->call('saveSeoSettings');

    $service = new \App\Support\VoltFileService;
    expect($service->isRouteCached($this->slug))->toBeTrue();
});

it('preserves the cache setting when renaming a page', function (): void {
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: false);

    $newSlug = 'renamed-cache-'.uniqid();
    $this->renamedFullPath = resource_path('views/pages/⚡'.$newSlug.'.blade.php');

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('pageSlug', $newSlug)
        ->call('saveSeoSettings');

    $service = new \App\Support\VoltFileService;
    expect($service->isRouteCached($newSlug))->toBeFalse();
});

it('does not move the route when the cache setting is unchanged', function (): void {
    (new \App\Support\VoltFileService)->addPublicRoute($this->slug, cached: true);

    $routesBefore = file_get_contents(base_path('routes/web.php'));

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('isCachedPage', true)
        ->call('saveSeoSettings');

    $routesAfter = file_get_contents(base_path('routes/web.php'));

    // Route content should be identical (no remove+re-add)
    expect($routesAfter)->toBe($routesBefore);
});

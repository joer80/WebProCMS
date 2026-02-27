<?php

use App\Enums\Role;
use App\Models\User;
use App\Support\VoltFileService;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->withRole(Role::Manager)->create();

    $this->slug = 'test-auth-'.uniqid();
    $this->tempRelativePath = 'pages/⚡'.$this->slug.'.blade.php';
    $this->tempFullPath = resource_path('views/'.$this->tempRelativePath);
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
    if (isset($this->tempFullPath) && file_exists($this->tempFullPath)) {
        unlink($this->tempFullPath);
    }

    foreach (glob(resource_path('views/pages/_editor-previews/*.blade.php')) ?: [] as $file) {
        unlink($file);
    }

    file_put_contents(base_path('routes/web.php'), $this->originalRoutes);
});

it('defaults requiresLogin to false when no auth route is registered', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('requiresLogin', false)
        ->assertSet('requiredRole', '');
});

it('detects requiresLogin as true when the route is in the auth section', function (): void {
    (new VoltFileService)->addAuthRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('requiresLogin', true)
        ->assertSet('requiredRole', '');
});

it('detects the required role when the auth route has a role middleware', function (): void {
    (new VoltFileService)->addAuthRoute($this->slug, cached: false, role: 'manager');

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->assertSet('requiresLogin', true)
        ->assertSet('requiredRole', 'manager');
});

it('moves the route to the auth section when requiresLogin is enabled', function (): void {
    (new VoltFileService)->addPublicRoute($this->slug, cached: true);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->call('saveSeoSettings');

    $service = new VoltFileService;
    expect($service->isAuthRoute($this->slug))->toBeTrue();
});

it('places the auth route in the cached sub-group when isCachedPage is true', function (): void {
    (new VoltFileService)->addPublicRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->set('isCachedPage', true)
        ->call('saveSeoSettings');

    $service = new VoltFileService;
    expect($service->isAuthRoute($this->slug))->toBeTrue();
    expect($service->isAuthRouteCached($this->slug))->toBeTrue();
});

it('places the auth route in the uncached section when isCachedPage is false', function (): void {
    (new VoltFileService)->addPublicRoute($this->slug, cached: true);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->set('isCachedPage', false)
        ->call('saveSeoSettings');

    $service = new VoltFileService;
    expect($service->isAuthRouteCached($this->slug))->toBeFalse();
});

it('includes the role middleware on the route when a required role is set', function (string $role): void {
    (new VoltFileService)->addPublicRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->set('requiredRole', $role)
        ->call('saveSeoSettings');

    $routes = file_get_contents(base_path('routes/web.php'));
    expect($routes)->toContain("->middleware('role:{$role}')");
})->with(['manager', 'admin', 'super']);

it('moves the route back to the public section when requiresLogin is turned off', function (): void {
    (new VoltFileService)->addAuthRoute($this->slug, cached: false);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', false)
        ->call('saveSeoSettings');

    $service = new VoltFileService;
    expect($service->isAuthRoute($this->slug))->toBeFalse();
});

it('does not inject a page-auth PHP block into the page file', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->call('saveSeoSettings');

    expect(file_get_contents($this->tempFullPath))->not->toContain('page-auth');
});

it('rejects an invalid requiredRole value', function (): void {
    (new VoltFileService)->addPublicRoute($this->slug, cached: true);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.pages.editor', ['file' => $this->tempRelativePath])
        ->set('requiresLogin', true)
        ->set('requiredRole', 'standard')
        ->call('saveSeoSettings')
        ->assertHasErrors(['requiredRole']);
});

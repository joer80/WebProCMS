<?php

use App\Enums\Role;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    \Livewire\Features\SupportLazyLoading\SupportLazyLoading::disableWhileTesting();
    $this->user = User::factory()->withRole(Role::Manager)->create();
    $this->originalRoutes = file_get_contents(base_path('routes/web.php'));
});

afterEach(function (): void {
    file_put_contents(base_path('routes/web.php'), $this->originalRoutes);
});

it('renders the redirects page for managers', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->assertSuccessful();
});

it('displays existing redirects from routes/web.php', function (): void {
    $routes = file_get_contents(base_path('routes/web.php'));
    $routes = preg_replace(
        '/^(\/\/ new uncached pages are inserted here)$/m',
        "$1\nRoute::redirect('old-page', '/new-page', 301);",
        $routes,
        1
    );
    file_put_contents(base_path('routes/web.php'), $routes);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->assertSeeText('old-page')
        ->assertSeeText('/new-page');
});

it('can create a new redirect', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openCreateModal')
        ->set('fromPath', 'old-path')
        ->set('toUrl', '/new-path')
        ->set('statusCode', '301')
        ->call('save')
        ->assertHasNoErrors();

    expect(file_get_contents(base_path('routes/web.php')))
        ->toContain("Route::redirect('old-path', '/new-path', 301);");
});

it('validates that fromPath is required', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openCreateModal')
        ->set('fromPath', '')
        ->set('toUrl', '/new-path')
        ->call('save')
        ->assertHasErrors(['fromPath']);
});

it('validates that toUrl is required', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openCreateModal')
        ->set('fromPath', 'old-path')
        ->set('toUrl', '')
        ->call('save')
        ->assertHasErrors(['toUrl']);
});

it('validates that statusCode must be 301 or 302', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openCreateModal')
        ->set('fromPath', 'old-path')
        ->set('toUrl', '/new-path')
        ->set('statusCode', '200')
        ->call('save')
        ->assertHasErrors(['statusCode']);
});

it('can delete a redirect', function (): void {
    $routes = file_get_contents(base_path('routes/web.php'));
    $routes = preg_replace(
        '/^(\/\/ new uncached pages are inserted here)$/m',
        "$1\nRoute::redirect('to-delete', '/destination', 301);",
        $routes,
        1
    );
    file_put_contents(base_path('routes/web.php'), $routes);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('deleteRedirect', 'to-delete')
        ->assertDispatched('notify');

    expect(file_get_contents(base_path('routes/web.php')))
        ->not->toContain("Route::redirect('to-delete'");
});

it('can edit an existing redirect', function (): void {
    $routes = file_get_contents(base_path('routes/web.php'));
    $routes = preg_replace(
        '/^(\/\/ new uncached pages are inserted here)$/m',
        "$1\nRoute::redirect('original-path', '/original-dest', 301);",
        $routes,
        1
    );
    file_put_contents(base_path('routes/web.php'), $routes);

    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openEditModal', 'original-path')
        ->assertSet('fromPath', 'original-path')
        ->assertSet('toUrl', '/original-dest')
        ->assertSet('statusCode', '301')
        ->set('toUrl', '/updated-dest')
        ->set('statusCode', '302')
        ->call('save')
        ->assertHasNoErrors();

    $updatedRoutes = file_get_contents(base_path('routes/web.php'));
    expect($updatedRoutes)->not->toContain("Route::redirect('original-path', '/original-dest', 301);");
    expect($updatedRoutes)->toContain("Route::redirect('original-path', '/updated-dest', 302);");
});

it('dispatches a notify event after creating a redirect', function (): void {
    Livewire::actingAs($this->user)
        ->test('pages::dashboard.redirects')
        ->call('openCreateModal')
        ->set('fromPath', 'notify-test')
        ->set('toUrl', '/dest')
        ->call('save')
        ->assertDispatched('notify');
});

<?php

use App\Enums\Role;
use App\Models\User;
use App\Support\LayoutService;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->layoutConfigPath = config_path('layout.php');
    $this->originalConfig = file_get_contents($this->layoutConfigPath);
});

afterEach(function (): void {
    file_put_contents($this->layoutConfigPath, $this->originalConfig);
});

it('requires authentication', function (): void {
    $this->get(route('dashboard.templates'))
        ->assertRedirect(route('login'));
});

it('loads the templates page for a manager', function (): void {
    $user = User::factory()->withRole(Role::Manager)->create();

    $this->actingAs($user)
        ->get(route('dashboard.templates'))
        ->assertOk();
});

it('saves layout settings', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::dashboard.templates')
        ->set('bodyClasses', 'overflow-x-hidden')
        ->set('phpTop', '// custom php')
        ->call('saveLayoutSettings')
        ->assertHasNoErrors()
        ->assertDispatched('notify', message: 'Layout settings saved.');

    $config = (new LayoutService)->getConfig();

    expect($config['body_classes'])->toBe('overflow-x-hidden')
        ->and($config['php_top'])->toBe('// custom php');
});

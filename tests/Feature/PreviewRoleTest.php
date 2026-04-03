<?php

use App\Enums\Role;
use App\Models\User;

beforeEach(function (): void {
    $this->super = User::factory()->withRole(Role::Super)->create();
    $this->manager = User::factory()->withRole(Role::Manager)->create();
});

test('super user can set a preview role', function (): void {
    $this->actingAs($this->super)
        ->post(route('dashboard.preview-as.store'), ['role' => 'standard'])
        ->assertRedirect()
        ->assertSessionHas('preview_role', 'standard');
});

test('super user can clear the preview role', function (): void {
    $this->actingAs($this->super)
        ->withSession(['preview_role' => 'standard'])
        ->delete(route('dashboard.preview-as.destroy'))
        ->assertRedirect()
        ->assertSessionMissing('preview_role');
});

test('non-super user cannot set a preview role', function (): void {
    $this->actingAs($this->manager)
        ->post(route('dashboard.preview-as.store'), ['role' => 'standard'])
        ->assertForbidden();
});

test('non-super user cannot clear the preview role', function (): void {
    $this->actingAs($this->manager)
        ->delete(route('dashboard.preview-as.destroy'))
        ->assertForbidden();
});

test('effectiveRole returns simulated role for super with session set', function (): void {
    $this->actingAs($this->super);
    session()->put('preview_role', 'manager');

    expect($this->super->effectiveRole())->toBe(Role::Manager);
});

test('effectiveRole returns real role for super with no session', function (): void {
    $this->actingAs($this->super);

    expect($this->super->effectiveRole())->toBe(Role::Super);
});

test('effectiveRole always returns real role for non-super even if session set', function (): void {
    $this->actingAs($this->manager);
    session()->put('preview_role', 'standard');

    expect($this->manager->effectiveRole())->toBe(Role::Manager);
});

test('effectiveRole will not elevate to a role above the real role', function (): void {
    $this->actingAs($this->manager);
    session()->put('preview_role', 'admin');

    // Manager cannot simulate Admin (higher role) — falls back to real role
    expect($this->manager->effectiveRole())->toBe(Role::Manager);
});

test('isPreviewingRole is true when super has an active preview', function (): void {
    $this->actingAs($this->super);
    session()->put('preview_role', 'standard');

    expect($this->super->isPreviewingRole())->toBeTrue();
});

test('isPreviewingRole is false when no preview is active', function (): void {
    $this->actingAs($this->super);

    expect($this->super->isPreviewingRole())->toBeFalse();
});

test('previewIsAtLeast respects the simulated role', function (): void {
    $this->actingAs($this->super);
    session()->put('preview_role', 'standard');

    expect($this->super->previewIsAtLeast(Role::Standard))->toBeTrue()
        ->and($this->super->previewIsAtLeast(Role::Manager))->toBeFalse();
});

test('role-gated dashboard route is blocked when simulating a lower role', function (): void {
    session()->put('preview_role', 'standard');

    $this->actingAs($this->super)
        ->get(route('dashboard.pages'))
        ->assertRedirect(route('dashboard'));
});

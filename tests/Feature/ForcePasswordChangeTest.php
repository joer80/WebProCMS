<?php

use App\Models\User;
use Livewire\Livewire;

it('redirects to password settings when must_change_password is true', function (): void {
    $user = User::factory()->create(['must_change_password' => true]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('user-password.edit'));
});

it('allows access to the password settings page when must_change_password is true', function (): void {
    $user = User::factory()->create(['must_change_password' => true]);

    $this->actingAs($user)
        ->get(route('user-password.edit'))
        ->assertOk();
});

it('does not redirect when must_change_password is false', function (): void {
    $user = User::factory()->create(['must_change_password' => false]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('clears must_change_password after a successful password update', function (): void {
    $user = User::factory()->create([
        'must_change_password' => true,
        'password' => bcrypt('oldpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.password')
        ->set('current_password', 'oldpassword')
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect($user->fresh()->must_change_password)->toBeFalse();
});

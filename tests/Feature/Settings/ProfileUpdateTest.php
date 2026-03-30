<?php

declare(strict_types=1);

use App\Models\User;

test('displays the profile page', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('profile.edit'));

    $response->assertOk();
});

test('updates profile information', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = patch(route('profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com');
});

test('lets a user delete their account', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = delete(route('profile.destroy'), [
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    assertGuest();

    expect($user->fresh())->toBeNull();
});

test('requires the correct password to delete an account', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = from(route('profile.edit'))->delete(route('profile.destroy'), [
        'password' => 'wrong-password',
    ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not()->toBeNull();
});

<?php

declare(strict_types=1);

use App\Models\User;

test('displays the password update page', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('user-password.edit'));

    $response->assertOk();
});

test('updates the password', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = from(route('user-password.edit'))->put(route('user-password.update'), [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('user-password.edit'));

    expect(password_verify('new-password', $user->refresh()->password))->toBeTrue();
});

test('requires the correct password to update the password', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = from(route('user-password.edit'))->put(route('user-password.update'), [
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect(route('user-password.edit'));
});

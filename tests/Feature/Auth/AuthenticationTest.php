<?php

declare(strict_types=1);

use App\Enums\UserStatus;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('renders the login screen', function (): void {
    $response = get(route('login'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('auth/Login'),
        );
});

test('renders the login screen on the root path for guests', function (): void {
    $response = get(route('home'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('auth/Login'),
        );
});

test('redirects authenticated users from the root path to the dashboard', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('home'));

    $response->assertRedirect(route('dashboard'));
});

test('authenticates users with the login screen and stores the last login timestamp', function (): void {
    $user = User::factory()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    assertAuthenticated();

    expect(str($user->id)->isUuid())->toBeTrue()
        ->and($user->fresh()->last_login_at)->not()->toBeNull();

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('does not authenticate inactive users', function (): void {
    $user = User::factory()->create([
        'status' => UserStatus::Inactive,
    ]);

    $response = from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
});

test('does not authenticate users with an invalid password', function (): void {
    $user = User::factory()->create();

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    assertGuest();
});

test('logs users out', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = post(route('logout'));

    assertGuest();
    $response->assertRedirect(route('home'));
});

test('rate limits repeated failed login attempts', function (): void {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});

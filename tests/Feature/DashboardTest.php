<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('redirects guests to the login page', function (): void {
    $response = get(route('dashboard'));

    $response->assertRedirect(route('home'));
});

test('allows authenticated users to visit the dashboard', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $response = get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Dashboard')
            ->where('title', 'Дашборд')
            ->has('metrics', 5),
        );
});

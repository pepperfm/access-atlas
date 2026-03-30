<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature', 'Unit');

uses(RefreshDatabase::class)->in('Feature', 'Unit');

beforeEach(function (): void {
    $this->withoutVite();
});

if (!function_exists('actingAs')) {
    function actingAs(\Illuminate\Contracts\Auth\Authenticatable $user, ?string $driver = null)
    {
        return \Pest\Laravel\actingAs($user, $driver);
    }
}

if (!function_exists('assertAuthenticated')) {
    function assertAuthenticated(?string $guard = null): void
    {
        \Pest\Laravel\assertAuthenticated($guard);
    }
}

if (!function_exists('assertGuest')) {
    function assertGuest(?string $guard = null): void
    {
        \Pest\Laravel\assertGuest($guard);
    }
}

if (!function_exists('from')) {
    function from(string $url)
    {
        return \Pest\Laravel\from($url);
    }
}

if (!function_exists('get')) {
    function get(string $uri, array $headers = [])
    {
        return \Pest\Laravel\get($uri, $headers);
    }
}

if (!function_exists('getJson')) {
    function getJson(string $uri, array $headers = [])
    {
        return \Pest\Laravel\getJson($uri, $headers);
    }
}

if (!function_exists('post')) {
    function post(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\post($uri, $data, $headers);
    }
}

if (!function_exists('postJson')) {
    function postJson(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\postJson($uri, $data, $headers);
    }
}

if (!function_exists('put')) {
    function put(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\put($uri, $data, $headers);
    }
}

if (!function_exists('putJson')) {
    function putJson(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\putJson($uri, $data, $headers);
    }
}

if (!function_exists('patch')) {
    function patch(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\patch($uri, $data, $headers);
    }
}

if (!function_exists('patchJson')) {
    function patchJson(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\patchJson($uri, $data, $headers);
    }
}

if (!function_exists('delete')) {
    function delete(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\delete($uri, $data, $headers);
    }
}

if (!function_exists('deleteJson')) {
    function deleteJson(string $uri, array $data = [], array $headers = [])
    {
        return \Pest\Laravel\deleteJson($uri, $data, $headers);
    }
}

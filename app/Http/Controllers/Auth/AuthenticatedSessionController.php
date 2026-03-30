<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Data\Auth\LoginPageData;
use App\Enums\UserStatus;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Response;

final readonly class AuthenticatedSessionController
{
    public function create(Request $request): Response
    {
        return inertia(
            'auth/Login',
            new LoginPageData(
                status: $request->session()->get('status'),
            )->toArray(),
        );
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->string('email')->value();
        $throttleKey = $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            abort(429);
        }

        if (!auth()->attempt([
            ...$request->validated(),
            'status' => UserStatus::Active->value,
        ], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            $user = User::query()->firstWhere('email', $email);

            if ($user instanceof User && $user->status !== UserStatus::Active) {
            }

            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        user()->update([
            'last_login_at' => now(),
        ]);

        return to_route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}

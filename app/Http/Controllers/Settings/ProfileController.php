<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final readonly class ProfileController
{
    public function edit(): Response
    {
        return inertia('settings/Profile');
    }

    /**
     * Update the user's profile information.
     *
     * @param ProfileUpdateRequest $request
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     *
     * @param ProfileDeleteRequest $request
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}

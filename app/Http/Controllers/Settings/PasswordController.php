<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final readonly class PasswordController
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return inertia('settings/Password');
    }

    /**
     * Update the user's password.
     *
     * @param PasswordUpdateRequest $request
     */
    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return back();
    }
}

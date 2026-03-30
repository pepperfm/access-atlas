<?php

declare(strict_types=1);

namespace App\Http\Controllers\Secrets;

use App\Actions\Secrets\RevealSecretAction;
use App\Models\Secret;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final readonly class RevealSecretController
{
    public function __invoke(Request $request, Secret $secret, RevealSecretAction $revealSecretAction): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('reveal', $secret);

        $revealed = $revealSecretAction->handle(
            actor: $actor,
            secret: $secret,
            reason: $request->string('reason')->value() ?: null,
        );

        return back()->with('revealed_secret', $revealed->toArray());
    }
}

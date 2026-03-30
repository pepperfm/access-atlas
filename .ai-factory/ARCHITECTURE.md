# Architecture: Modular Monolith

## Overview
Access Atlas should start as a modular monolith: one Laravel application, one deployment unit, and one shared database, with strong boundaries between business areas. This fits the project because the MVP has substantial domain complexity around projects, memberships, accesses, secrets, reviews, and offboarding, but it does not yet justify the operational cost of microservices.

This pattern keeps delivery speed high for an early-stage product while forcing domain separation early enough to avoid a generic CRUD tangle. The primary architectural goal is not infrastructure isolation; it is keeping project-first business logic explicit, testable, and easy to evolve.

## Decision Rationale
- **Project type:** internal access and secret registry with governance workflows
- **Tech stack:** Laravel 12, Inertia Vue 3, Nuxt UI, PostgreSQL, `spatie/laravel-data`
- **Key factor:** medium-to-high domain complexity with a small initial codebase and a need for fast iteration

## Modules
- Identity & App Authorization
- Participants
- Projects & Environments
- Resources
- Access Grants
- Secrets & Consumers
- Reviews & Rotation
- Inbox Intake
- Audit & Search
- Dashboard & Offboarding

## Folder Structure
```text
app/
├── Actions/                    # Application use-cases grouped by domain
│   ├── Access/
│   ├── Audit/
│   ├── Inbox/
│   ├── Participants/
│   ├── Projects/
│   ├── Resources/
│   ├── Reviews/
│   └── Secrets/
├── Concerns/                   # Shared traits for UUIDs, enums, and validation rules
├── Data/                       # spatie/laravel-data DTOs for request/response boundaries
│   ├── Access/
│   ├── Audit/
│   ├── Inbox/
│   ├── Offboarding/
│   ├── Participants/
│   ├── Projects/
│   ├── Resources/
│   ├── Reviews/
│   ├── Search/
│   └── Secrets/
├── Enums/                      # Shared enum/value layer for statuses, roles, sensitivity, and policies
├── Http/
│   ├── Controllers/            # Thin delivery layer, organized by domain and operational surfaces
│   ├── Middleware/
│   └── Requests/               # Only when plain FormRequest is still the right boundary
├── Models/                     # Eloquent models with UUID PKs and documented properties
├── Policies/                   # Authorization rules using Spatie permissions + domain state
├── Providers/                  # Cross-cutting Laravel bootstrapping
└── Support/                    # Shared low-level utilities that do not belong to a domain

database/
├── factories/
├── migrations/
└── seeders/

resources/js/
├── components/                 # Reusable UI components, grouped by domain when needed
├── layouts/
├── pages/                      # Inertia pages, grouped by domain
│   ├── Accesses/
│   ├── Audit/
│   ├── Dashboard/
│   ├── Inbox/
│   ├── Offboarding/
│   ├── Participants/
│   ├── Projects/
│   ├── Resources/
│   ├── Reviews/
│   ├── Search/
│   └── Secrets/
├── composables/                # Frontend state and interaction logic
└── types/                      # Shared TS contracts for page props and UI state
```

## Dependency Rules
- ✅ Controllers call Actions and pass typed `laravel-data` objects rather than pushing business logic into HTTP endpoints.
- ✅ Actions may depend on Eloquent models, domain-specific DTOs, policies, and focused support services.
- ✅ Policies may depend on permissions, project membership state, ownership, and access/secret lifecycle state.
- ✅ Frontend pages compose Nuxt UI components and consume typed page props from Inertia.
- ❌ Controllers must not contain review, rotation, offboarding, or reveal business rules.
- ❌ Frontend components must not encode authorization rules that only exist in the browser.
- ❌ Cross-domain writes should not happen through ad hoc model mutations scattered across controllers.
- ❌ Raw secret values must never be used in search indexing, list endpoints, or generic logs.

## Layer Communication
- HTTP layer validates and normalizes input, then hands off to an Action.
- Actions orchestrate domain state transitions and persist through Eloquent models.
- `laravel-data` DTOs define the boundary shape for action input, page props, and API/resource output.
- Policies and permissions gate sensitive operations such as reveal, rotation, revoke, or management.
- Audit recording happens inside the use-case path for every security-relevant action, not as optional UI behavior.

## Key Principles
1. Project-first modeling: resources, accesses, secrets, and operational workflows are entered through project context whenever possible.
2. Metadata-first security: ownership, environment, consumers, status, and lifecycle dates are modeled explicitly; raw secret values stay isolated.
3. Explicit lifecycle: archive, revoke, rotate, verify, and offboard are first-class transitions, not loose status text.
4. Helper-first Laravel style: prefer framework helpers and fluent helper APIs over facade-heavy code where the helper variant exists.
5. UUID everywhere: core models and their relationships use UUID-based identities consistently.
6. Thin transport boundaries: controllers stay small, DTOs stay typed, actions own business behavior.

## Code Examples

### Action With Typed Input
```php
<?php

declare(strict_types=1);

namespace App\Actions\Secrets;

use App\Data\Secrets\StoreSecretData;
use App\Models\Secret;

final readonly class StoreSecretAction
{
    public function handle(StoreSecretData $data): Secret
    {
        return Secret::query()->create([
            'id' => str()->uuid()->toString(),
            'project_id' => $data->projectId,
            'project_environment_id' => $data->projectEnvironmentId,
            'resource_id' => $data->resourceId,
            'owner_participant_id' => $data->ownerParticipantId,
            'name' => $data->name,
            'status' => $data->status,
        ]);
    }
}
```

### Controller As Delivery Layer
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Secrets;

use App\Actions\Secrets\RevealSecretAction;
use App\Data\Secrets\RevealSecretData;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

final class RevealSecretController extends Controller
{
    public function __invoke(
        RevealSecretData $data,
        RevealSecretAction $revealSecret,
    ): RedirectResponse {
        $revealSecret->handle(actor: user(), data: $data);

        return to_route('secrets.show', $data->secretId);
    }
}
```

## Cross-Cutting Conventions
- Put `Model::unguard()` in a provider once the domain models start landing; avoid `$fillable` unless a package forces it.
- Prefer UUID setup through a reusable trait shared across domain models.
- Add model PHPDoc blocks for discoverability of attributes and relationships.
- Keep package-specific authorization logic isolated: Spatie manages app authorization, while project roles and external access levels stay in domain models.
- Use Bun and `bunx` for frontend workflows; treat leftover `npm` references from starter scaffolding as cleanup debt.
- Use Nuxt UI as the default building block for forms, tables, overlays, and navigation in Inertia pages.

## Anti-Patterns
- ❌ Turning `secrets` into a standalone vault module disconnected from projects, resources, and ownership.
- ❌ Mixing Spatie app roles with project membership roles as if they were the same concept.
- ❌ Implementing reveal or rotation flows without mandatory audit events.
- ❌ Letting list pages or search endpoints access decrypted secret values.
- ❌ Building new UI from unstructured divs when Nuxt UI already provides the needed primitive.

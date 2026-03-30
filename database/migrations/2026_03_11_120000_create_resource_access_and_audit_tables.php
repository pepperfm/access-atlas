<?php

declare(strict_types=1);

use App\Enums\AccessGrantStatus;
use App\Enums\ResourceStatus;
use App\Enums\SensitivityLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('provider');
            $table->string('kind');
            $table->string('name');
            $table->string('external_identifier')->nullable();
            $table->foreignUuid('owner_user_id')->constrained('users')->restrictOnDelete();
            $table->string('sensitivity')->default(SensitivityLevel::Medium->value);
            $table->string('status')->default(ResourceStatus::Active->value);
            $table->string('canonical_source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('project_resources', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignUuid('environment_id')->nullable()->constrained('project_environments')->nullOnDelete();
            $table->string('relation_type')->default('primary');
            $table->string('status')->default(ResourceStatus::Active->value);
            $table->timestamps();

            $table->unique(['project_id', 'resource_id', 'environment_id'], 'project_resources_project_resource_environment_unique');
        });

        Schema::create('access_grants', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('environment_id')->nullable()->constrained('project_environments')->nullOnDelete();
            $table->foreignUuid('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('grant_kind');
            $table->string('access_level');
            $table->text('scope_description')->nullable();
            $table->foreignUuid('owner_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('granted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default(AccessGrantStatus::Active->value);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('review_due_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_events', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('target_type');
            $table->uuid('target_id');
            $table->foreignUuid('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
            $table->index(['project_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('access_grants');
        Schema::dropIfExists('project_resources');
        Schema::dropIfExists('resources');
    }
};

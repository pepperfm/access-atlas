<?php

declare(strict_types=1);

use App\Enums\InboxItemStatus;
use App\Enums\InboxSourceType;
use App\Enums\ReviewTaskStatus;
use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('secrets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('environment_id')->constrained('project_environments')->cascadeOnDelete();
            $table->uuid('resource_id')->nullable()->index();
            $table->string('name');
            $table->string('secret_type');
            $table->string('sensitivity')->default(SensitivityLevel::Medium->value);
            $table->string('storage_mode')->default(SecretStorageMode::ExternalReference->value);
            $table->text('external_reference')->nullable();
            $table->text('encrypted_value')->nullable();
            $table->foreignUuid('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default(SecretStatus::Active->value);
            $table->string('reveal_policy')->default(SecretRevealPolicy::ProjectOwnerOrSecurityAdmin->value);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('last_rotated_at')->nullable();
            $table->timestamp('rotation_due_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('secret_consumers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('secret_id')->constrained('secrets')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('resource_id')->nullable()->index();
            $table->foreignUuid('environment_id')->nullable()->constrained('project_environments')->nullOnDelete();
            $table->text('usage_note')->nullable();
            $table->timestamps();
        });

        Schema::create('review_tasks', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('target_type');
            $table->uuid('target_id');
            $table->string('task_type');
            $table->foreignUuid('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('due_at');
            $table->string('status')->default(ReviewTaskStatus::Open->value);
            $table->string('result')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
        });

        Schema::create('inbox_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('source_type')->default(InboxSourceType::Manual->value);
            $table->text('raw_text')->nullable();
            $table->json('parsed_summary')->nullable();
            $table->foreignUuid('suggested_project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('status')->default(InboxItemStatus::New->value);
            $table->foreignUuid('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('normalized_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('normalized_at')->nullable();
            $table->json('normalized_entities')->nullable();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_items');
        Schema::dropIfExists('review_tasks');
        Schema::dropIfExists('secret_consumers');
        Schema::dropIfExists('secrets');
    }
};

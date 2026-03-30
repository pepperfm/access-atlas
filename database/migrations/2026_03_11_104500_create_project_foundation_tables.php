<?php

declare(strict_types=1);

use App\Enums\EnvironmentStatus;
use App\Enums\MembershipStatus;
use App\Enums\ProjectCriticality;
use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('repository_url')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default(ProjectStatus::Active->value);
            $table->string('criticality')->default(ProjectCriticality::Medium->value);
            $table->foreignUuid('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('project_environments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->boolean('is_production')->default(false);
            $table->string('status')->default(EnvironmentStatus::Active->value);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'key']);
        });

        Schema::create('project_memberships', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('project_role')->default(ProjectRole::Developer->value);
            $table->string('status')->default(MembershipStatus::Active->value);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_memberships');
        Schema::dropIfExists('project_environments');
        Schema::dropIfExists('projects');
    }
};

<?php

declare(strict_types=1);

use App\Http\Controllers\Access\AccessGrantController;
use App\Http\Controllers\AuditEventController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inbox\InboxItemController;
use App\Http\Controllers\OffboardingController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Resources\ResourceController;
use App\Http\Controllers\Reviews\ReviewTaskController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Secrets\RevealSecretController;
use App\Http\Controllers\Secrets\SecretController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{managedUser}', [UserController::class, 'update'])->name('users.update');
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('projects/{project}/users', [ProjectController::class, 'addParticipant'])->name('projects.users.store');
    Route::delete('projects/{project}/users/{user}', [ProjectController::class, 'removeParticipant'])->name('projects.users.destroy');
    Route::post('projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::get('resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::post('resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::post('resources/link-project', [ResourceController::class, 'linkToProject'])->name('resources.link-project');
    Route::get('access-grants', [AccessGrantController::class, 'index'])->name('access-grants.index');
    Route::post('access-grants', [AccessGrantController::class, 'store'])->name('access-grants.store');
    Route::post('access-grants/{accessGrant}/revoke', [AccessGrantController::class, 'revoke'])->name('access-grants.revoke');
    Route::get('secrets', [SecretController::class, 'index'])->name('secrets.index');
    Route::post('secrets', [SecretController::class, 'store'])->name('secrets.store');
    Route::post('secrets/{secret}/reveal', RevealSecretController::class)->name('secrets.reveal');
    Route::get('reviews', [ReviewTaskController::class, 'index'])->name('reviews.index');
    Route::post('reviews', [ReviewTaskController::class, 'store'])->name('reviews.store');
    Route::post('reviews/{reviewTask}/complete', [ReviewTaskController::class, 'complete'])->name('reviews.complete');
    Route::get('inbox', [InboxItemController::class, 'index'])->name('inbox.index');
    Route::post('inbox', [InboxItemController::class, 'store'])->name('inbox.store');
    Route::post('inbox/{inboxItem}/normalize', [InboxItemController::class, 'normalize'])->name('inbox.normalize');
    Route::post('inbox/{inboxItem}/purge', [InboxItemController::class, 'purge'])->name('inbox.purge');
    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('audit', [AuditEventController::class, 'index'])->name('audit.index');
    Route::get('offboarding', [OffboardingController::class, 'index'])->name('offboarding.index');
});

require __DIR__ . '/settings.php';

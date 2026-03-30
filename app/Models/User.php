<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property \App\Enums\UserStatus $status
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $last_login_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectMembership> $projectMemberships
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasUuidPrimaryKey;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => UserStatus::class,
            'last_login_at' => 'immutable_datetime',
            'password' => 'hashed',
        ];
    }

    public function projectMemberships(): HasMany
    {
        return $this->hasMany(ProjectMembership::class);
    }
}

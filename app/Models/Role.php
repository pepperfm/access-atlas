<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasUuidPrimaryKey;
}

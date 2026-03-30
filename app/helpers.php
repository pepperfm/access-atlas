<?php

declare(strict_types=1);

if (!function_exists('user')) {
    function user(?string $guard = null): \App\Models\User
    {
        $user = auth($guard)->user();

        if (!$user instanceof \App\Models\User) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException()->setModel(\App\Models\User::class);
        }

        return $user;
    }
}

if (!function_exists('when')) {
    function when(bool $condition, callable $true, ?callable $false = null)
    {
        if ($condition) {
            return $true();
        }
        if ($false) {
            return $false();
        }

        return null;
    }
}

if (!function_exists('valueOrDefault')) {
    function valueOrDefault(mixed $value = null, mixed $default = null, ...$args)
    {
        if (blank($args) && blank($value)) {
            return $default;
        }

        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (!function_exists('db')) {
    function db(?string $connection = null): \Illuminate\Database\ConnectionInterface
    {
        return app('db')->connection($connection);
    }
}

if (!function_exists('enum_values')) {
    /**
     * @param class-string<\BackedEnum> $enumClass
     *
     * @return list<int|string>
     */
    function enum_values(string $enumClass): array
    {
        return array_map(
            static fn(\BackedEnum $case): int|string => $case->value,
            $enumClass::cases(),
        );
    }
}

if (!function_exists('enum_options')) {
    /**
     * @param class-string<\BackedEnum> $enumClass
     *
     * @return array<int|string, string>
     */
    function enum_options(string $enumClass): array
    {
        return collect($enumClass::cases())
            ->mapWithKeys(static fn(\BackedEnum $case): array => [
                $case->value => method_exists($case, 'label')
                    ? $case->label()
                    : str($case->name)->headline()->value(),
            ])
            ->all();
    }
}

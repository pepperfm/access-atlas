<?php

declare(strict_types=1);

namespace App\Concerns;

trait InteractsWithEnumOptions
{
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn(self $case): string => $case->value,
            self::cases(),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn(self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function label(): string
    {
        return str($this->name)->headline()->value();
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum ProjectRole: string
{
    use InteractsWithEnumOptions;

    case Owner = 'owner';
    case TechLead = 'tech_lead';
    case Manager = 'manager';
    case Developer = 'developer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Владелец',
            self::TechLead => 'Техлид',
            self::Manager => 'Менеджер',
            self::Developer => 'Разработчик',
        };
    }
}

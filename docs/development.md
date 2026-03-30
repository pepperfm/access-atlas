[← Modules](modules.md) · [Back to README](../README.md)

# Development

## Базовый runtime

Проект работает через Laravel Sail.

Основные команды:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail stop
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail bun run dev
```

## Предпочтительный workflow

1. Поднять Sail.
2. Обновить схему через `migrate:fresh --seed`, если менялись миграции.
3. Запустить `bun run dev`.
4. Делать точечные проверки по затронутому модулю.

## Проверки качества

### TypeScript

```bash
./vendor/bin/sail bun run typecheck
```

Что включает:

- Wayfinder codegen
- Ziggy codegen
- `vue-tsc --noEmit`

### PHPStan

```bash
./vendor/bin/sail php vendor/bin/phpstan analyse --no-progress
```

### PHP CS Fixer

```bash
./vendor/bin/sail php vendor/bin/php-cs-fixer fix --diff -v --dry-run
```

### Pint

```bash
./vendor/bin/sail php vendor/bin/pint --dirty --format agent
```

### Тесты

```bash
./vendor/bin/sail artisan test --compact
```

Для точечной проверки лучше запускать только затронутые feature tests.

## Frontend-конвенции

- owner-facing формы строим на Nuxt UI
- `USelectMenu` с object-options должен получать `value-key="value"`, если `v-model` хранит строку
- для даты использовать `UInputDate`
- для чисел использовать `UInputNumber`

## Backend-конвенции

- `User` — единственная человеческая сущность
- `app role` и `project role` не смешивать
- boundary types — через `spatie/laravel-data`
- helpers предпочтительнее фасадов, если helper есть

## Что важно помнить про рефакторы

- raw secret values нельзя тащить в search/listings/logging
- policies должны проверять product-level access отдельно от project-level role
- большие изменения удобнее валидировать прямым набором команд, а не только общим `make`

## Полезные ориентиры в коде

- `routes/web.php` — основные экраны
- `app/Actions` — use-cases
- `app/Data` — boundary layer
- `resources/js/pages` — Inertia pages
- `resources/js/types` — frontend contracts

## See Also

- [Getting Started](getting-started.md) — установка и первый запуск
- [Architecture](architecture.md) — архитектурные границы
- [Modules](modules.md) — что делает каждый модуль

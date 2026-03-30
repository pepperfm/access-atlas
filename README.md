# Access Atlas

> Внутренний реестр пользователей, проектов, ресурсов, доступов и секретов.

Access Atlas нужен для того, чтобы команда перестала хранить доступы в чатах, заметках и памяти отдельных людей.
Приложение собирает всё в одном месте: кто имеет доступ, к какому проекту относится ресурс, где лежит секрет и кто за него отвечает.

Сейчас продукт устроен вокруг двух уровней ролей:

- `app role` управляет доступом пользователя к самому приложению
- `project role` управляет ролью пользователя внутри конкретного проекта

## Что внутри

- **Пользователи** — единая человеческая сущность, без отдельного слоя `participants`
- **Проекты** — карточки проектов с окружениями и командой
- **Ресурсы** — внешние сервисы и внутренние системы вроде Stripe, GitHub, AWS, Sentry
- **Доступы** — кому и какой доступ выдали, к чему и кто за него отвечает
- **Секреты** — owner-friendly intake для паролей, токенов, API keys и внешних references
- **Операционные поверхности** — dashboard, inbox, search, audit, offboarding, reviews

## Стек

- PHP 8.4 + Laravel 12
- Inertia.js v2 + Vue 3
- Nuxt UI v4 + Tailwind CSS v4
- PostgreSQL
- `spatie/laravel-permission`
- `spatie/laravel-data`
- Laravel Sail
- Bun / `bunx`

## Быстрый старт

```bash
cp .env.example .env
./vendor/bin/sail up -d --build
./vendor/bin/sail composer install
./vendor/bin/sail bun install
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail bun run dev
```

После сидирования в системе будет создан владелец по умолчанию:

- email: `owner@example.com`
- password: `password`

## Типовой сценарий

1. Владелец заходит в приложение.
2. Создаёт пользователей и назначает им `app role`.
3. Создаёт проект и добавляет пользователей в команду проекта с `project role`.
4. Заводит ресурсы вроде Stripe или GitHub.
5. Фиксирует доступы: кому дали, к чему и какого уровня.
6. Добавляет секреты: либо сырое значение, либо внешний reference.

## Проверка качества

Предпочтительный локальный набор проверок:

```bash
./vendor/bin/sail bun run typecheck
./vendor/bin/sail php vendor/bin/phpstan analyse --no-progress
./vendor/bin/sail php vendor/bin/php-cs-fixer fix --diff -v --dry-run
./vendor/bin/sail php vendor/bin/pint --dirty --format agent
./vendor/bin/sail artisan test --compact
```

## Документация

| Раздел | Описание |
|-------|----------|
| [Getting Started](docs/getting-started.md) | Установка, запуск, сидирование и первый вход |
| [Architecture](docs/architecture.md) | Текущая архитектура и слои приложения |
| [Access Model](docs/access-model.md) | `app role`, `project role` и модель доступа |
| [Modules](docs/modules.md) | Что делает каждый экран и доменный модуль |
| [Development](docs/development.md) | Повседневная разработка, проверки и команды |

## Исходные внутренние документы

- [ТЗ MVP](tz-mvp-project-access-registry.md)
- [AGENTS.md](AGENTS.md)
- [CLAUDE.md](CLAUDE.md)

## Лицензия

MIT

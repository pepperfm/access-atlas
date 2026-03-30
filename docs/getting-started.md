[Back to README](../README.md) · [Architecture →](architecture.md)

# Getting Started

## Что нужно заранее

- Docker и Docker Compose
- PHP/Composer локально не обязательны, потому что проект работает через Sail
- Bun не нужен локально, если используешь `./vendor/bin/sail bun ...`

## Первичная установка

```bash
cp .env.example .env
./vendor/bin/sail up -d --build
./vendor/bin/sail composer install
./vendor/bin/sail bun install
./vendor/bin/sail artisan migrate:fresh --seed
```

## Запуск фронтенда

Для разработки:

```bash
./vendor/bin/sail bun run dev
```

Для проверки production-сборки:

```bash
./vendor/bin/sail bun run build
```

## Первый вход

Сидер создаёт владельца по умолчанию:

- email: `owner@example.com`
- password: `password`
- роль в приложении: `owner`

После входа владелец может:

- создавать пользователей
- назначать `app role`
- создавать проекты
- добавлять пользователей в проект
- заводить ресурсы, доступы и секреты

## Обязательные шаги после старта

1. Зайти под `owner@example.com`.
2. Создать реальных пользователей команды.
3. Раздать им `app role`.
4. Создать первый проект.
5. Добавить пользователей в проект с `project role`.

## Полезные команды

Запуск контейнеров:

```bash
./vendor/bin/sail up -d
```

Остановка:

```bash
./vendor/bin/sail stop
```

Сброс схемы:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

Проверка тестов:

```bash
./vendor/bin/sail artisan test --compact
```

## Что считать успешным стартом

Установка считается успешной, если:

- открывается страница логина
- можно войти под `owner@example.com`
- открывается dashboard
- работают страницы `Users`, `Projects`, `Resources`, `Accesses`, `Secrets`

## See Also

- [Architecture](architecture.md) — как устроено приложение
- [Access Model](access-model.md) — как работают роли и доступы
- [Development](development.md) — команды разработки и проверки

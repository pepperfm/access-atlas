[← Getting Started](getting-started.md) · [Back to README](../README.md) · [Access Model →](access-model.md)

# Architecture

## Общая модель

Access Atlas — это модульный монолит на Laravel.

Ключевая идея текущей архитектуры:

- `User` — единственная человеческая сущность
- `app role` — доступ к самому приложению
- `project role` — роль пользователя внутри проекта

Отдельного слоя `participants` больше нет.

## Базовые сущности

| Сущность | Назначение |
|---------|------------|
| `users` | Люди, которые могут входить в приложение |
| `projects` | Карточки проектов |
| `project_environments` | Окружения проекта |
| `project_memberships` | Связь `user ↔ project` с `project_role` |
| `resources` | Внешние сервисы и внутренние системы |
| `project_resources` | Привязка ресурса к проекту |
| `access_grants` | Кто имеет доступ к ресурсу |
| `secrets` | Секреты и references |
| `secret_consumers` | Кто использует секрет |
| `review_tasks` | Операционные задачи ревью |
| `inbox_items` | Ручной intake сырого текста |
| `audit_events` | Журнал значимых действий |

## Структура кода

```text
app/
  Actions/           use-case слой
  Data/              laravel-data boundary layer
  Enums/             app roles, project roles, статусы
  Http/Controllers/  Inertia delivery layer
  Models/            Eloquent-модели
  Policies/          authorization rules

resources/js/
  pages/             Inertia-страницы
  components/        общие UI-компоненты
  layouts/           layout-обёртки
  types/             frontend контракты
```

## Принципы слоёв

### Controllers

- тонкие
- собирают page props
- вызывают actions
- не держат бизнес-логику

### Actions

- исполняют use-case
- создают и меняют модели
- пишут audit, если операция значимая

### Data

- описывают request/output boundary
- держат snake_case mapping наружу
- дают typed payload между HTTP и доменом

### Policies

- проверяют доступ к сущностям
- работают поверх permission layer и project membership layer

## Frontend

Frontend строится на:

- Inertia pages
- Nuxt UI primitives
- typed props из `resources/js/types`

Новые owner-facing формы должны собираться из Nuxt UI, а не из ad hoc разметки.

## Текущая договорённость по навигации

- для обычной навигации не использовать `Link` из Inertia без необходимости
- использовать `route(...)`
- третий аргумент `false` не ставить без причины

## Что считать хорошей архитектурной границей

Хорошо:

- `UserController` управляет только app-level users и roles
- `ProjectController` управляет проектом и его командой
- `ResourceController` не знает про секретный reveal flow
- `SecretController` не знает про app-role management

Плохо:

- смешивать `app role` и `project role`
- держать отдельную человеческую сущность без реальной причины
- тащить raw secret values в search/listing/logging

## See Also

- [Access Model](access-model.md) — детали ролей и прав
- [Modules](modules.md) — как это проявляется в UI
- [Development](development.md) — как это поддерживать в коде

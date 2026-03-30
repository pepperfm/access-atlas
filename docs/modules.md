[← Access Model](access-model.md) · [Back to README](../README.md) · [Development →](development.md)

# Modules

## Users

Страница `Users` отвечает за:

- создание пользователей
- назначение `app role`
- изменение статуса пользователя

Это главный вход для owner-level администрирования команды.

## Projects

Страница `Projects` отвечает за:

- создание проекта
- редактирование названия, репозитория и критичности
- просмотр окружений
- добавление пользователей в проект
- назначение `project role`

## Resources

`Resources` — это понятные owner-facing сущности:

- Stripe account
- GitHub repository
- AWS account
- Sentry project

Форма сведена к минимуму:

- сервис
- тип сущности
- имя
- ответственный

## Accesses

`Accesses` отвечает на вопрос:

> Кто и какой доступ получил?

Основные поля:

- проект
- окружение
- ресурс
- пользователь
- формат доступа
- уровень доступа
- ответственный

## Secrets

`Secrets` — самый важный owner-facing intake flow.

Секрет можно:

- привязать к проекту
- связать с ресурсом
- сохранить как raw value
- сохранить как внешний reference

Форма должна быть понятна без технарского контекста.

## Reviews

`Reviews` — очередь задач:

- review доступа
- проверка секрета
- ротация секрета
- ownership review

Это operational surface, а не основной CRUD-флоу.

## Inbox

`Inbox` нужен для ручного intake:

- сырой фрагмент чата
- заметка
- ручная нормализация в секрет

Это мост между хаотичным вводом и структурированным реестром.

## Dashboard

Dashboard показывает:

- что скоро истечёт
- где нет владельца
- где нужен review
- какие записи требуют внимания

## Search

Search работает по метаданным:

- проект
- пользователь
- ресурс
- доступ
- секрет

Raw secret values сюда не попадают.

## Audit

Audit — журнал значимых действий:

- создание
- отзыв
- reveal
- другие security-relevant изменения

## Offboarding

Offboarding собирает user-centric checklist:

- проектные memberships
- access grants
- owned resources
- owned secrets
- review tasks

## See Also

- [Access Model](access-model.md) — как роли соотносятся с модулями
- [Architecture](architecture.md) — как эти модули разложены по коду
- [Development](development.md) — как их развивать и проверять

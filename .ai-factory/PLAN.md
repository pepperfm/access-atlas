# План: Reveal flow для секретов

Дата: 2026-03-12
Режим: fast

## Settings

- Тесты: да
- Логирование: minimal
- Документация: нет

## Контекст

Текущий backend уже умеет раскрывать секрет:
- `POST /secrets/{secret}/reveal`
- policy `SecretPolicy::reveal`
- `RevealSecretAction` возвращает реальное значение или внешний reference
- `RevealSecretController` кладёт это во flash как `revealed_secret`

Но нормального UX-потока потребления секрета нет:
- frontend не обрабатывает `revealed_secret`
- нельзя увидеть значение в безопасном overlay
- нельзя скопировать значение
- нельзя скопировать `.env`-строку
- для `high` / `critical` нет нормального prompt-а на reason в UI

Цель: сделать owner-friendly flow “раскрыть → посмотреть → скопировать / использовать”, полностью на Nuxt UI.

## Tasks

### Phase 1: Подготовить данные reveal на frontend

- [x] 1. Добавить `revealed_secret` в shared Inertia props через middleware.
  Files:
  - `app/Http/Middleware/HandleInertiaRequests.php`
  - `resources/js/types/secrets.ts`
  - `resources/js/types/global.d.ts` если потребуется общий shared prop
  Deliverable:
  - frontend получает `revealed_secret` как typed prop после reveal redirect
  Logging:
  - без дополнительного логирования

- [x] 2. Проверить и при необходимости уточнить контракт `RevealSecretData` для UI.
  Files:
  - `app/Data/Secrets/RevealSecretData.php`
  - `app/Actions/Secrets/RevealSecretAction.php`
  Deliverable:
  - данные reveal содержат всё, что нужно UI: имя, value, reason, `isReferenceOnly`, `externalReference`
  Logging:
  - без дополнительного логирования

### Phase 2: Построить reveal UX на Nuxt UI

- [x] 3. Добавить reveal modal/slideover на странице секретов.
  Files:
  - `resources/js/pages/Secrets/Index.vue`
  - при необходимости выделить `resources/js/components/secrets/RevealSecretModal.vue`
  Deliverable:
  - после successful reveal открывается Nuxt UI overlay
  - encrypted secret показывает значение
  - external reference показывает путь/ссылку
  Logging:
  - без дополнительного логирования

- [x] 4. Добавить copy actions для реального использования секрета.
  Files:
  - `resources/js/pages/Secrets/Index.vue`
  - или `resources/js/components/secrets/RevealSecretModal.vue`
  Deliverable:
  - `Скопировать значение`
  - `Скопировать как ENV`
  - для reference-only: `Скопировать reference`
  - toast success/error после copy
  Logging:
  - без дополнительного логирования

- [x] 5. Добавить UX для mandatory reason на `high` / `critical`.
  Files:
  - `resources/js/pages/Secrets/Index.vue`
  Deliverable:
  - перед reveal high/critical секретов пользователь вводит reason в modal/form
  - reason уходит в `POST /secrets/{secret}/reveal`
  - validation error reason показывается в UI, если backend его требует
  Logging:
  - без дополнительного логирования

### Phase 3: Довести поток до рабочего состояния

- [x] 6. Добавить безопасный post-reveal state management.
  Files:
  - `resources/js/pages/Secrets/Index.vue`
  Deliverable:
  - overlay закрывается предсказуемо
  - повторный reload не держит старое раскрытое значение лишний раз в UI
  - страница корректно ведёт себя после reveal success/error
  Logging:
  - без дополнительного логирования

- [x] 7. Проверить UX списка секретов и CTA вокруг reveal.
  Files:
  - `resources/js/pages/Secrets/Index.vue`
  Deliverable:
  - понятно, что можно раскрыть
  - понятно, что будет показано: значение или reference
  - нет лишней двусмысленности для владельца/техлида
  Logging:
  - без дополнительного логирования

### Phase 4: Проверка

- [x] 8. Добавить/обновить feature tests по reveal flow.
  Files:
  - `tests/Feature/Secrets/SecretRegistryTest.php`
  - при необходимости отдельный focused test
  Deliverable:
  - reveal route, permissions, reason requirement, flash payload покрыты тестами
  Logging:
  - без дополнительного логирования

- [x] 9. Прогнать browser smoke по `/secrets`.
  Checks:
  - список секретов открывается
  - reveal modal открывается
  - encrypted/reference modes рендерятся корректно
  - console без runtime errors
  Logging:
  - использовать browser logs / console only

## Commit Plan

1. `feat(secrets): add reveal modal flow`
2. `feat(secrets): add copy actions and reason prompt`
3. `test(secrets): cover reveal interaction flow`

## Проверка

- `./vendor/bin/sail bun run typecheck`
- `./vendor/bin/sail php vendor/bin/phpstan analyse --no-progress`
- `./vendor/bin/sail php vendor/bin/pint --dirty --format agent`
- `./vendor/bin/sail artisan test --compact tests/Feature/Secrets/SecretRegistryTest.php`
- browser smoke: `/secrets`

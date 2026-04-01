# Project Rules

> Short, actionable rules and conventions for this project. Loaded automatically by $aif-implement.

## Rules

- By default, keep application logging at error level only; do not add info/warning/debug logs unless the user explicitly asks for diagnostic logging.
- In owner-facing forms, put field explanations into `UFormField help`. Do not use item-level `description` for explaining what a field means when the message semantically belongs to the field itself.
- When using Wayfinder route helpers in UI link props like `to` or `href`, pass `.url()`; use the route object itself only where the API explicitly accepts a Wayfinder definition, such as form helpers.

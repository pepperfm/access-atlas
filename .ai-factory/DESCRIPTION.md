# Project: Access Atlas

## Overview
Access Atlas is an internal project-first registry for projects, participants, resources, access grants, and secrets. Its MVP replaces fragmented credential storage across chats and notes with a single control plane that keeps ownership, review, rotation, auditability, and offboarding visible.

The product is not a raw secret vault. Secrets are modeled as part of the broader access graph: project, environment, resource, owner, consumers, lifecycle state, review cadence, and reveal audit all matter.

## Core Features
- Project-centric registry of projects, environments, participants, resources, access grants, and secrets
- Application authorization based on `User` + Spatie `Role` + `Permission`
- Ownership, review, rotation, revocation, archival, and offboarding workflows
- Inbox for manual import of credentials and context from chats or free-form text
- Global metadata search and dashboard views for stale, risky, and overdue objects
- Audit log for security-relevant actions, including secret reveal events

## Domain Modules
- Identity and app authorization: `users`, `roles`, `permissions`
- People directory: `participants`
- Project registry: `projects`, `project_environments`, `project_memberships`
- Resource registry: `resources`, `project_resources`
- Access registry: `access_grants`
- Secret registry: `secrets`, `secret_consumers`
- Governance workflows: `review_tasks`, offboarding summaries
- Operational intake and traceability: `inbox_items`, `audit_events`

## Tech Stack
- **Language:** PHP 8.4, TypeScript
- **Backend:** Laravel 12
- **Frontend:** Inertia.js v2 + Vue 3 + Nuxt UI v4 + Tailwind CSS v4
- **Database:** PostgreSQL
- **Authorization:** `spatie/laravel-permission`
- **Data mapping:** `spatie/laravel-data`
- **Array sugar / helpers:** `pepperfm/macros-for-laravel`
- **Local runtime:** Laravel Sail
- **Bundler / package runner:** Bun and `bunx`

## Project Conventions
- UUID is the primary key for all core domain models. The preferred implementation is a shared UUID trait on models rather than repeated per-model setup.
- Application models are expected to use `Model::unguard()` in a provider, with no `$fillable` arrays. Model properties should be documented with PHPDoc blocks instead.
- Prefer Laravel helpers and fluent helper APIs over facades when the helper form exists: `str()->uuid()->toString()`, `str('...')->value()`, `auth()->login()`, `to_route()`, and similar framework sugar.
- Prefer application helpers over facade access where the project already exposes a safe helper.
- Use `spatie/laravel-data` as the main boundary type for request DTOs, action input, response payloads, and typed view/API data.
- Frontend tooling is Bun-first. Even if upstream starter scripts still reference `npm`, project work should standardize on `bun` and `bunx`.
- Nuxt UI is the primary component layer for frontend work inside the Inertia/Vite stack.

## Architecture Notes
- Pattern: modular monolith. The application ships as one Laravel app, but domain boundaries should be explicit from the start.
- Domain modules should stay project-first: project context is the main entrypoint for resources, accesses, secrets, and operational workflows.
- Metadata search must never index raw secret values.
- Deactivate, revoke, and archive flows are preferred over destructive deletes for security-sensitive entities.
- Auditability is a first-class requirement for access and secret lifecycle actions.

## Non-Functional Requirements
- Logging: security-relevant state changes and reveal actions must be auditable
- Security: secret values are hidden by default and only revealed explicitly with audit logging
- Maintainability: typed DTO boundaries via `laravel-data`, helper-first Laravel style, and domain-oriented modules
- Search safety: search is metadata-first and excludes raw secret material
- Operational clarity: dashboard and offboarding flows must expose stale ownership, overdue reviews, and revoke checklists

## Architecture
See `.ai-factory/ARCHITECTURE.md` for detailed architecture guidelines.
Pattern: Modular Monolith

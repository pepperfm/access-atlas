# Project Roadmap

> Build Access Atlas as a project-first internal registry for projects, participants, resources, access grants, and secrets with auditability, review, rotation, and offboarding workflows.

## Milestones

- [x] **Domain Foundation** — introduce UUID-based domain models, shared UUID trait, `Model::unguard()`, model PHPDoc, and baseline enums/statuses for the MVP entities
- [x] **Authorization Model** — adapt `spatie/laravel-permission` to UUIDs, seed app roles and permissions, and separate app authorization from project roles and external access levels
- [x] **Projects and Environments Registry** — deliver projects, explicit environments, and project memberships as the core navigation entrypoint
- [x] **Participants Directory** — add participant records, links to users, ownership metadata, and project assignment workflows
- [x] **Resources and Access Grants** — implement resource catalog, project-resource links, and access grant lifecycle with ownership and revoke/archive states
- [x] **Secrets Registry** — implement secret metadata, protected value storage, reveal flow, consumers, and strict audit logging around secret access
- [x] **Review and Rotation Workflows** — add review tasks, verification and rotation due dates, and overdue lifecycle management
- [x] **Inbox Intake Pipeline** — add inbox items for manual import from chats/text and triage into structured domain records
- [x] **Audit Log and Activity Traceability** — expose auditable events for security-relevant actions across secrets, accesses, reviews, and membership changes
- [x] **Global Search and Dashboard** — deliver metadata-first search plus dashboard views for overdue reviews, stale ownership, expiring access, and risky secrets
- [x] **Offboarding Console** — build participant-centric offboarding summary and revoke checklist across memberships, accesses, resources, and secrets
- [x] **Nuxt UI Application Shell** — replace starter pages with a cohesive Nuxt UI-based product shell, navigation, tables, filters, forms, and detail views for the main modules
- [x] **Quality and Delivery Hardening** — add focused Pest coverage, factories/seeders for domain modules, Bun-first scripts cleanup, and CI-quality gates for the MVP surface
- [x] **Project Bootstrap and Context Setup** — Laravel 12 + Inertia/Vue + Sail environment, starter auth/settings shell, and AI project context are in place

## Completed

| Milestone | Date |
|-----------|------|
| Project Bootstrap and Context Setup | 2026-03-11 |
| Domain Foundation | 2026-03-11 |
| Authorization Model | 2026-03-11 |
| Projects and Environments Registry | 2026-03-11 |
| Participants Directory | 2026-03-11 |
| Resources and Access Grants | 2026-03-11 |
| Secrets Registry | 2026-03-11 |
| Review and Rotation Workflows | 2026-03-11 |
| Inbox Intake Pipeline | 2026-03-11 |
| Audit Log and Activity Traceability | 2026-03-11 |
| Global Search and Dashboard | 2026-03-11 |
| Offboarding Console | 2026-03-11 |
| Nuxt UI Application Shell | 2026-03-11 |
| Quality and Delivery Hardening | 2026-03-11 |

# Project Context

## Purpose
- Maintain a personal, single-user Laravel workspace focused on weight-loss tracking, meal planning, and daily schedule management.
- Deliver server-rendered pages with Blade and Tailwind, layering small Alpine.js interactions where needed.
- Keep deployment simple enough to run on low-cost or self-hosted infrastructure without multi-user concerns.

## Tech Stack
- PHP 8.2+ with Laravel 12 as the primary backend framework.
- Blade templates styled with Tailwind CSS 4 and bundled via Vite.
- Alpine.js provides the only JavaScript interactivity layer; Axios is available for AJAX needs.
- SQLite (`database/database.sqlite`) is the default development datastore; swap via environment config when needed.
- Pest v4 for automated tests and Laravel Pint for formatting.

## Project Conventions

### Code Style
- Follow Laravel and PSR-12 conventions; `vendor/bin/pint --dirty` enforces formatting on touched files.
- Use PHP 8 features (constructor property promotion, typed properties, explicit return types) and PHPDoc array shapes when helpful.
- Prefer Form Request classes for validation and small dedicated classes for reusable logic as complexity grows.
- Build Blade views with Tailwind utilities, extracting Blade components/partials before duplicating markup; keep Alpine scripts inline and declarative.

### Architecture Patterns
- Keep routes in `routes/web.php`, delegating to controllers for coordination; push business logic into services or actions when controllers become busy.
- Use Eloquent models for persistence, leaning on relationships and query scopes instead of manual queries.
- Default to server-rendered flows; only reach for APIs or SPA patterns when unavoidable.
- Organize front-end assets through Vite with entry points in `resources/js/app.js` and CSS in `resources/css/app.css`.

### Testing Strategy
- Do not create or run automated tests for this personal project.
- When validating changes, rely on quick manual verification in the browser or through artisan tinker/database queries as appropriate.

### Git Workflow
- Work from short-lived feature branches and merge into `main` once specs and tests are satisfied.
- Reference the relevant OpenSpec change (if any) in commit messages or PR descriptions.
- Keep commits focused (spec updates, implementation, follow-up chores) to simplify reviews.

## Domain Context
- Application is private and only operated by the owner, so no authentication or authorization flows are required.
- Primary features revolve around weight-loss goals (progress tracking, calorie intake, workouts) and schedule coordination (tasks, calendar reminders).
- UX can assume trusted input but should still guard against accidental data loss.

## Important Constraints
- Avoid introducing authentication, multi-tenancy, or complex account management.
- Prefer minimal dependencies—favor built-in Laravel features, Tailwind, and Alpine over heavier libraries.
- Ensure everything runs locally with `composer run dev` (Laravel server + Vite) and SQLite without extra services.
- Document notable behaviors via OpenSpec before large changes to keep the single source of truth current.

## External Dependencies
- No required third-party APIs today; optional integrations should be feature-flagged and clearly documented in specs.
- CDN-hosted assets (e.g., fonts) may be used for convenience but should degrade gracefully if offline.

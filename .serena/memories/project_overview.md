# Project Overview
- Laravel 12 skeleton intended for a personal, single-user web app centered on weight-loss tracking, meal planning, and schedule management.
- PHP 8.2+, Pest for testing, Vite + Tailwind CSS for the frontend. Frontend interactivity should use lightweight Alpine.js rather than heavier frameworks.
- Default database setup relies on the SQLite file at `database/database.sqlite`; migrations live under `database/migrations`.
- New Laravel 12 streamlined structure: routes in `routes/*.php`, controllers in `app/Http/Controllers`, models in `app/Models`, providers registered via `bootstrap/providers.php`. 
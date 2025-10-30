# Suggested Commands
- `composer install` / `npm install` – install PHP and JS dependencies.
- `composer run dev` – run the bundled dev stack (Laravel server, queue listener, logs, Vite) via concurrently.
- `npm run dev` or `npm run build` – build assets with Vite.
- `php artisan serve` – serve the application locally.
- `php artisan migrate` – run database migrations (defaults to SQLite file at `database/database.sqlite`).
- `php artisan test` – execute Pest test suite.
- `vendor/bin/pint --dirty` – format only changed PHP files.
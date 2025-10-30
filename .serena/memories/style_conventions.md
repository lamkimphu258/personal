# Style & Conventions
- Follow Laravel 12 conventions: route definitions in `routes/web.php`, thin controllers, business logic extracted to dedicated classes when it grows.
- Use PHP 8 features (strict types, constructor property promotion, return types) and annotate arrays with PHPDoc shapes when helpful.
- Formatting enforced with Laravel Pint; adhere to PSR-12 indentation (4 spaces) and trailing newline per `.editorconfig`.
- Write Blade views using Tailwind utility classes and sprinkle Alpine.js for interactivity; avoid large JavaScript frameworks.
- Prefer Form Request classes for validation and Pest feature tests for endpoints or pages.
# Repository Guidelines

## Project Structure & Module Organization

This repository is a Laravel 12 application. Put application code in `app/` (HTTP controllers, models, providers, and related classes), route definitions in `routes/`, and configuration in `config/`. Database migrations, factories, and seeders belong in `database/`. Blade templates and frontend source assets live in `resources/`; Vite's public entry point and built assets are under `public/`. Automated tests are in `tests/Feature` and `tests/Unit`. Runtime files such as logs and cache belong in `storage/`; do not commit generated runtime data.

## Build, Test, and Development Commands

- `composer install` installs PHP dependencies; `npm install` installs frontend dependencies.
- `composer run setup` performs the repository's setup steps, including environment/key setup, migration, and asset build.
- `composer run dev` starts the Laravel server, queue listener, log tail, and Vite dev server together.
- `php artisan serve` starts only the Laravel server; `npm run dev` starts the Vite watcher.
- `npm run build` creates production frontend assets in `public/build`.
- `composer test` clears config and runs the PHPUnit suite via Artisan. Tests use in-memory SQLite as configured in `phpunit.xml`.
- `vendor/bin/pint` formats PHP files using Laravel Pint.

## Coding Style & Naming Conventions

Follow the repository's `.editorconfig`: UTF-8, LF line endings, four spaces, and a final newline. Use Laravel conventions and PSR-4 namespaces (`App\\` maps to `app/`): class names use `StudlyCase`, methods and variables use `camelCase`, and database tables/columns use Laravel's conventional `snake_case`. Keep route declarations in the appropriate `routes/*.php` file and use focused classes rather than placing application logic in route closures.

## Testing Guidelines

PHPUnit 11 is configured with separate `Unit` and `Feature` suites. Name test files with a `Test.php` suffix and group them by behavior under the matching directory. Add feature coverage for HTTP behavior and unit coverage for isolated logic. Run `composer test` before submitting changes; no coverage threshold is configured.

## Commit & Pull Request Guidelines

The available history contains only `feat: initial laravel setup`, so no broader commit convention is established. Use concise imperative subjects, optionally prefixed by a type such as `feat:`, `fix:`, or `chore:`. Pull requests should explain the change and its impact, link related issues when applicable, and include screenshots for visible UI changes. Mention relevant test or build commands and their results.

## Security & Configuration

Create local configuration from `.env.example`; keep secrets and machine-specific `.env` values out of version control. Prefer environment variables for deployment-specific settings and migrations for schema changes.

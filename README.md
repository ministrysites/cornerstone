# Cornerstone Starter Kit

Cornerstone is an opinionated Laravel starter kit built for MinistrySites projects. It gives us a clean Laravel 13 base with the tooling, conventions, and local package hooks used across this stack so new projects start from the same foundation.

This starter kit is primarily for our own use. If the choices here match how you like to build, you are welcome to use it too. If they do not, you will likely be better served by forking it and shaping it around your own conventions rather than trying to generalize this one.

## Important note

This repository is intentionally opinionated.

That is not accidental and is not a temporary state. The purpose of this starter kit is to encode a specific workflow with specific defaults and assumptions for our projects.

## What is included

- Laravel 13 on PHP 8.3+
- Livewire for server-driven UI
- Vite with Tailwind CSS v4
- Pest for testing
- Laravel Pint for formatting
- Larastan / PHPStan for static analysis
- Laravel Boost and Pail for local development
- Quick intallation for optional packages by using `php artisan cornerstone:install`

## Requirements

Before setup, make sure you have:

- PHP 8.3 or newer
- Composer
- Node.js and npm
- A database supported by Laravel

## Getting started

If dependencies are not installed yet, the fastest path is:

```bash
composer run setup
```

That script will:

1. Install PHP dependencies
2. Create `.env` from `.env.example` if needed
3. Generate an app key
4. Run database migrations
5. Install JavaScript dependencies
6. Build frontend assets

If you prefer to do that manually:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Local development

Start the full local development stack with:

```bash
composer run dev
```

This runs:

- The Laravel app server
- A queue listener
- Laravel Pail for logs
- The Vite dev server

If you only need frontend assets:

```bash
npm run dev
```

## Quality checks

Common project checks are already defined in Composer:

```bash
composer run test
composer run stan
composer run pint:test
composer run check
```

Useful fix commands:

```bash
composer run pint
composer run fix
```

## GitHub setup

If you use this starter kit on GitHub, protect the `main` branch before team use.

- Require pull requests before merging
- Require passing CI checks before merging
- Block direct pushes to `main`

## Project structure

- `resources/views/layouts/app.blade.php` contains the base Blade layout with Vite and Livewire assets
- `routes/web.php` currently serves the default welcome page at `/`
- `app/` is the application code for controllers, models, providers, and future domain logic
- `tests/` contains Pest-based feature and unit tests
- `stubs/` contains custom publishable stubs used by this starter

## First customizations

After creating a new project from this starter, the usual first edits are:

- Set the application name and environment values in `.env`
- Replace the default welcome page with your actual homepage or dashboard
- Add project-specific routes, Livewire components, and domain models
- Configure queue, mail, cache, and filesystem drivers for the target environment

## Contributing

Issues and pull requests are welcome, but keep the scope aligned with the purpose of the starter kit.

The fastest way to get a contribution accepted is to keep it focused on:

- fixing bugs
- improving reliability
- clarifying behavior or documentation
- improving maintainability without changing the starter kit's opinions

Pull requests that add features, broaden the starter kit's scope, or change the underlying opinions and defaults are unlikely to be merged.

If you need materially different behavior, the better path is usually to fork this starter kit and adapt it to your own conventions.

## Notes

- The app uses strict types in project PHP files by default
- The repository is intended to be a starting point, not a finished product

# Cornerstone

Cornerstone is the Laravel starter kit  to spin up new projects. It is a clean Laravel 13 + Livewire 4 base with the tooling, conventions, and quality gates across the stack, so every new project starts from the same foundation.

This kit is primarily for our own use. If the choices here line up with how you like to build, you are welcome to use it. If they do not, you will almost certainly be happier forking it and reshaping it than trying to bend it to a different philosophy.

## Opinions, on purpose

Cornerstone is intentionally opinionated. That is not a temporary state and it is not up for debate on a per-project basis. The whole point of the kit is to encode a specific workflow with specific defaults so that humans and AI tools generating code on top of it land in the same place.

The shape of those opinions:

- **Explicit over clever.** Strict types on every PHP file. Typed properties, typed return values, narrow public state.
- **Livewire-first.** Server-driven components for web UI, Alpine for small client-side niceties. No SPA build pipeline. No duplicated state across a JS framework.
- **Tested from commit one.** Pest feature tests for user-facing behavior, Pest architecture tests for the boundaries we care about.
- **Analyzed before merge.** Larastan / PHPStan and Pint run locally and in CI, with protected branches expected to flow through pull requests. `composer check` is the gate.

## What's in the box

- Laravel 13 on PHP 8.3+
- Livewire 4 for server-driven UI
- Vite 8 + Tailwind CSS 4
- Pest 4 (feature, unit, architecture suites)
- Larastan / PHPStan
- Laravel Pint with a tightened ruleset
- Laravel Boost and Pail for local dev and AI-aware scaffolding
- `php artisan cornerstone:install` for pulling in predetermained but optional packages

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- A database supported by Laravel (SQLite is fine for local work)
- A local dev environment that can serve the app over HTTPS (Herd, Valet, or equivalent)

## Getting started

```bash
composer create-project ministrysites/cornerstone my-app
cd my-app
composer run dev
```

If you already have the repo cloned and just need to set it up:

```bash
composer run setup
```

That script installs PHP and JS dependencies, creates `.env` from `.env.example` if needed, generates an app key, runs migrations, and builds frontend assets.

## Local development

```bash
composer run dev
```

Runs the Laravel app server, a queue listener, Laravel Pail for logs, and the Vite dev server together. If you only need frontend assets, `npm run dev` is enough.

### HTTPS locally

The kit assumes your local environment serves the app over HTTPS. That gives you a secure context for secure cookies, OAuth redirects, and third-party callbacks without surprises. If your environment does not terminate TLS, either add HTTPS support or adjust `AppServiceProvider` for that environment instead of relying on the starter default.

## Quality checks

Cornerstone treats `composer run check` as the definition of green.

```bash
composer run check        # pint (dirty) + phpstan + pest
composer run check:all    # pint (full) + phpstan + pest
composer run fix          # pint --dirty, then phpstan, then pest
composer run fix:all      # pint (full), then phpstan, then pest
```

Individual tools:

```bash
composer run pint         # format
composer run pint:test    # check formatting without writing
composer run stan         # phpstan
composer run test         # pest in parallel
```

## Architecture conventions

- Livewire is the default for web UI and interactive pages.
- Standard controllers are fine for request-response flows that Livewire does not improve: redirects, OAuth callbacks, webhook endpoints, JSON APIs.
- Validate input explicitly at the boundary where it enters the system.
- Keep Livewire components and controllers thin. If a component grows a real domain, push the logic into a typed service or action and call it from the component.
- Prefer feature tests for user-facing behavior. Use unit tests for isolated logic. Use architecture tests for rules you want to be unbreakable.
- Do not commit debug helpers (`dd`, `dump`, `ray`, `var_dump`) into application code. The architecture suite enforces this.
- Do not call `env()` outside of `config/`. The architecture suite enforces this too.

## AI-assisted development

Cornerstone is designed to work well with Laravel Boost and with AI coding tools generally. The guardrails that matter live in three places:

1. **Pint + PHPStan + Pest.** If generated code does not pass `composer run check`, it is not done.
2. **Architecture tests.** Rules like "strict types on every file" and "no debug helpers in committed code" are enforced by the test suite, not by vibes.
3. **Boost guidelines under `.ai/guidelines`.** This is the single place to add project-specific AI guidance. Treat it as the primary home for guardrails instead of maintaining separate agent-specific instruction files.

Useful Boost commands:

```bash
php artisan boost:install   # publish Boost resources (first time)
php artisan boost:update    # refresh Boost resources after dependency changes
```

## Definition of done

Before opening a pull request or handing work off, a change should meet these checks:

1. `composer run pint:test` passes.
2. `composer run stan` passes.
3. `composer run test` passes, including the architecture suite.
4. No debug helpers remain in committed code.
5. The change follows the kit's explicit-code and Livewire-first conventions.

## Project layout

- `app/Livewire` — Livewire components for web-facing UI
- `app/Http/Controllers` — controllers for request-response flows that do not fit Livewire
- `app/Models` — Eloquent models
- `resources/views/layouts/app.blade.php` — base Blade layout with Vite and Livewire assets
- `resources/views/livewire` — Livewire component views
- `routes/web.php` — web routes (the example Livewire homepage lives here)
- `tests/Feature` — Pest feature tests
- `tests/Unit` — Pest unit tests
- `tests/Architecture` — Pest architecture tests that encode the kit's rules
- `.ai/guidelines` — project-specific Boost guidance for AI-assisted development

## First customizations

After creating a new project from Cornerstone, the usual first edits are:

- Set the application name and environment values in `.env`
- Replace the example homepage with your actual landing page, dashboard, or product entry point
- Add project-specific routes, Livewire components, and domain models
- Configure queue, mail, cache, and filesystem drivers for the target environment
- Add project-specific Boost guidelines under `.ai/guidelines` for any conventions unique to the project

## GitHub setup

If you host the project on GitHub, protect `main` before team use:

- Require pull requests before merging
- Require passing CI checks before merging
- Block direct pushes to `main`

## Contributing

Issues and pull requests are welcome, but the scope of this kit is narrow on purpose. The fastest way to get a contribution accepted is to keep it focused on:

- fixing bugs
- improving reliability
- clarifying behavior or documentation
- improving maintainability without changing the kit's opinions

Pull requests that add features, broaden the scope, or change the underlying opinions and defaults are unlikely to be merged. If you need materially different behavior, fork the kit and adapt it to your own conventions.

## License

Cornerstone is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

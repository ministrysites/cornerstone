# Cornerstone

Cornerstone is the Laravel starter kit to spin up new projects. It is a clean Laravel 13 + Livewire 4 base with the tooling, conventions, and quality gates wired in so every new project starts from the same foundation.

This kit is primarily for our own use. If the choices here line up with how you like to build, you are welcome to use it. If they do not, you will almost certainly be happier forking it and reshaping it than trying to bend it to a different philosophy.

## Opinions, on purpose

Cornerstone is intentionally opinionated. That is not a temporary state and it is not up for debate on a per-project basis. The whole point of the kit is to encode a specific workflow with specific defaults so that humans and AI tools generating code on top of it land in the same place.

The shape of those opinions:

- **Explicit over clever.** Strict types on every PHP file. Typed properties, typed return values, narrow public state. No type information in docblocks — if the interpreter can enforce it, the interpreter should.
- **Livewire-first.** Server-driven components for web UI, Alpine for small client-side niceties. No SPA build pipeline. No duplicated state across a JS framework.
- **Tested from commit one.** Pest feature tests for user-facing behavior, Pest unit tests for internal business logic, Pest architecture tests for the boundaries we care about.
- **Analyzed before merge.** Larastan / PHPStan and Pint run locally and in CI. `composer run check` is the gate.

## What's in the box

- Laravel 13 on PHP 8.3+
- Livewire 4 for server-driven UI
- Vite 8 + Tailwind CSS 4
- Pest 4 (feature, unit, architecture suites)
- Larastan / PHPStan
- Laravel Pint with a tightened ruleset
- Laravel Boost and Pail for local dev and AI-aware scaffolding
- Customized stubs under `stubs/` so `php artisan make:*` produces Cornerstone-conforming files out of the box
- `php artisan cornerstone:install` for pulling in predetermined but optional packages

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- A database supported by Laravel
- A local dev environment that can serve the app over HTTPS (Herd, Valet, or equivalent) is prefered, though you can adjust the HTTPS settings in `AppServiceProvider` if you prefer.

## Getting started

```bash
composer create-project ministrysites/cornerstone my-app
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

Runs the Laravel app server, a queue listener, Laravel Pail for logs, and the Vite dev server together.

### HTTPS locally

The kit assumes your local environment serves the app over HTTPS. That gives you a secure context for secure cookies, OAuth redirects, and third-party callbacks without surprises. If your environment does not terminate TLS, either add HTTPS support or adjust `AppServiceProvider` for that environment instead of relying on the starter default.

## Quality checks

`composer run check` is the definition of green. Before opening a pull request or handing work off, it must pass.

```bash
composer run check        # pint (dirty) + phpstan + pest
composer run check:all    # pint (full) + phpstan + pest
composer run fix          # same as check, but pint is allowed to write
composer run fix:all      # same as check:all, but pint is allowed to write
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
- Keep Livewire components and controllers thin. When orchestration grows, push it into a typed service in `app/Services/`, pass data with readonly value objects in `app/Data/`, and put backed enums in `app/Enums/`.
- Prefer feature tests for user-facing behavior. Use unit tests for isolated logic. Use architecture tests for rules you want to be unbreakable.

The architecture suite enforces several rules without room for negotiation:

- Strict types on every PHP file.
- No type information in docblocks (`@param`, `@return`, `@var`, array shapes).
- No `final` on classes. Extending existing classes is a legitimate pattern here.
- No debug helpers (`dd`, `dump`, `ray`, `var_dump`) in application code.
- No `env()` calls outside of `config/`.
- Public Livewire properties must carry `#[Locked]`, `#[Validate]`, `#[Url]`, or `#[Modelable]`.
- Models use attribute-based configuration (`#[Fillable]`, `#[Hidden]`, `#[UseFactory]`) and do not call facades.

## AI-assisted development

Cornerstone is designed to work well with Laravel Boost and with AI coding tools generally. The guardrails that matter live in three places:

1. **Pint + PHPStan + Pest.** If generated code does not pass `composer run check`, it is not done.
2. **Architecture tests.** The rules above are enforced by the test suite, not by convention alone.
3. **Boost guidelines under `.ai/guidelines`.** The single place to add project-specific AI guidance. Treat it as the primary home for guardrails instead of maintaining separate agent-specific instruction files.

Useful Boost commands:

```bash
php artisan boost:install   # publish Boost resources (first time)
php artisan boost:update    # refresh Boost resources after dependency changes
```

## Project layout

- `app/Livewire` — Livewire components for web-facing UI
- `app/Http/Controllers` — controllers for request-response flows that do not fit Livewire
- `app/Models` — Eloquent models
- `app/Services` — typed services and actions called from Livewire components and controllers
- `app/Data` — readonly value objects and DTOs (no enums)
- `app/Enums` — backed enums
- `resources/views/layouts/app.blade.php` — base Blade layout with Vite and Livewire assets
- `resources/views/livewire` — Livewire component views
- `routes/web.php` — web routes
- `tests/Feature` — Pest feature tests
- `tests/Unit` — Pest unit tests
- `tests/Architecture` — Pest architecture tests that encode the kit's rules
- `stubs/` — Cornerstone-customized stubs used by `php artisan make:*`
- `.ai/guidelines` — project-specific Boost guidance for AI-assisted development
- `.cornerstone/demo-manifest.txt` — list of demo files removed by `php artisan cornerstone:cleanup`

## First customizations

After creating a new project from Cornerstone, the usual first edits are:

- Set the application name and environment values in `.env`
- Add project-specific routes, Livewire components, and domain models
- Configure queue, mail, cache, and filesystem drivers for the target environment
- Add project-specific Boost guidelines under `.ai/guidelines` for any conventions unique to the project

## Removing the demo content

Cornerstone ships with a small set of demo files: a Livewire homepage, a sample service, a DTO, an enum, and their tests. This allows AI tools have concrete, in-repo examples of the kit's conventions before any real code has been written. Every demo file is tracked in `.cornerstone/demo-manifest.txt`.

When your project has grown its own examples in each of the directories the demo covered (`app/Livewire/`, `app/Services/`, `app/Data/`, `app/Enums/`, `tests/Unit/`, `tests/Feature/`), run:

```bash
php artisan cornerstone:cleanup
```

The command prints the full list of files it is about to delete, asks for explicit confirmation, and then removes every listed file along with the manifest itself. Running it earlier leaves AI tooling and new contributors without a reference shape to follow. Git history is your safety net if you change your mind.

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

## Cornerstone Conventions

Cornerstone is an opinionated Laravel 13 + Livewire 4 starter kit focused on explicit code and strong guardrails for AI-assisted development. Follow these rules when adding or modifying code in a Cornerstone project.

### Type safety

- Every PHP file must start with `declare(strict_types=1);`.
- Use native typehints on every property, parameter, and return value. Do not use docblocks to express types the interpreter can already enforce.
- Do not add `@param`, `@return`, `@var`, or array-shape docblocks. If a type cannot be expressed natively, that is usually a sign the code should be restructured.
- Never add `final` to classes. This is a no-`final` codebase by design — extending existing classes is a legitimate pattern here.

### Livewire

- Livewire is the default for web UI and interactive pages.
- Every public Livewire property must carry one of `#[Locked]`, `#[Validate]`, `#[Url]`, or `#[Modelable]`. Unbound public state is client-writable and is rejected by the architecture suite. Pick the attribute that matches intent:
  - `#[Locked]` — server-owned state the client must not change.
  - `#[Validate]` — user input validated on the server.
  - `#[Url]` — state synced to the query string.
  - `#[Modelable]` — parent-bound component state.
- Static component data (copy, menu items, option lists) belongs in `private const` arrays, not in methods called from `render()`. See `app/Livewire/HomePage.php` for the reference shape.
- Keep Livewire components thin. When orchestration grows, extract it into a typed service or action and call it from the component.

### Controllers

- Use standard controllers for request-response flows that do not benefit from Livewire: OAuth callbacks, webhooks, JSON APIs, redirects.
- Controllers in `App\Http\Controllers` must extend `App\Http\Controllers\Controller`. The architecture suite enforces this.

### Models

- Prefer attribute-based Eloquent configuration over the protected-property equivalents. Use `#[Fillable]`, `#[Hidden]`, and `#[UseFactory]` attributes on the class. See `app/Models/User.php` for the reference shape.
- Keep `use HasFactory` when you use `#[UseFactory]` — the attribute selects the factory class, the trait provides the `::factory()` static method.
- Do not call facades from inside models.

### Validation and defensive code

- Validate input explicitly at system boundaries (HTTP requests, Livewire actions, queue payloads).
- Inside the application, trust the types. Do not add `is_array()`, `isset()`, or `instanceof` checks against values that native typehints already guarantee.
- Do not add error handling, fallbacks, or backwards-compatibility shims for scenarios that cannot happen. Fix the root cause instead.

### Configuration

- Never call `env()` outside of `config/`. Use `config('...')` everywhere else. The architecture suite enforces this.
- Never commit `dd`, `dump`, `ray`, or `var_dump` calls to `app/` or `routes/`. The architecture suite enforces this.

### Comments

- Default to writing no comments. Typed names and small functions are the documentation.
- Only add a comment when it explains a non-obvious *why*: a hidden constraint, a subtle invariant, a workaround for a specific bug. Never narrate *what* the code does.

### Scope discipline

- Prefer editing existing files over creating new ones. Match the existing file and namespace structure.
- When a new file is genuinely needed, scaffold it with `php artisan make:*` and build on the resulting file. The project's `stubs/` directory holds Cornerstone-customized stubs that already encode the conventions in this guide (strict types, native type hints, no docblocks). Do not hand-roll equivalents from scratch.
- For patterns Laravel does not scaffold, follow the shape of whatever is already in the matching directory: typed services in `app/Services/`, readonly value objects / DTOs in `app/Data/` (DTOs only, no enums), and backed enums in `app/Enums/`. When adding code in one of these directories, open an existing file there and mirror its shape.
- Do not install new Composer or npm packages without explicit instruction.
- Do not introduce new base classes, abstractions, or patterns beyond what the task requires.
- Do not add agent-specific instruction files when Boost guidelines can express the same rule.
- Do not assume standard Laravel auth is present unless the codebase clearly introduces it.

### Definition of done

Before considering a change complete, run:

```bash
composer run fix
```

This runs Pint (on dirty files), then PHPStan, then the full Pest suite including the architecture tests. All three must pass. If you need the full-repo style pass as well, use `composer run fix:all`.

The architecture tests and the conventions above are part of the definition of done, not suggestions.

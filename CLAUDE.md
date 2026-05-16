<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

# Project Conventions

These conventions extend (and where noted, override) the Boost guidelines above.

## Docker

The stack runs entirely in Docker Compose: `app` (php 8.4-fpm), `nginx`, `mysql` (dev, persisted), `mysql_test` (test, tmpfs), `redis`. Frontend tooling (Vite/Node) runs on the **host** — there is no Node container.

**Override of Boost rules:** every PHP / Composer / Artisan invocation must be prefixed with `docker compose exec app`. Bare `php artisan` on the host fails because `DB_HOST=mysql` and `REDIS_HOST=redis` only resolve inside the compose network.

| Task              | Command                                                                     |
| ----------------- | --------------------------------------------------------------------------- |
| Bring stack up    | `docker compose up -d --build`                                              |
| Composer          | `docker compose exec app composer <cmd>`                                    |
| Artisan           | `docker compose exec app php artisan <cmd>`                                 |
| Tinker            | `docker compose exec app php artisan tinker --execute '<code>'`             |
| TS type gen       | `docker compose exec app php artisan typescript:transform`                  |
| Tests             | `docker compose exec app php artisan test --compact`                        |
| Lint+format (all) | `npm run lint` (from host) — Prettier+ESLint+Pint, auto-fixes in place      |
| Lint+format (BE)  | `npm run lint:be` — Pint inside `app` container                             |
| Lint+format (FE)  | `npm run lint:fe` — Prettier `--write` + ESLint on host                     |
| Logs (Pail)       | `docker compose exec app php artisan pail`                                  |
| MySQL (dev)       | `docker compose exec mysql mysql -urex_test -psecret rex_test`              |
| MySQL (test)      | `docker compose exec mysql_test mysql -urex_test -psecret rex_test_testing` |
| Redis             | `docker compose exec redis redis-cli`                                       |
| App shell         | `docker compose exec app sh`                                                |

Frontend (on host): `npm install`, `npm run dev`, `npm run build`. App is served at `http://localhost:${APP_PORT:-8080}`.

Rebuild only after Dockerfile / `docker/**` changes: `docker compose up -d --build app`. Composer changes don't require a rebuild.

## Linting & Formatting

One command — `npm run lint` — formats and lints the whole stack. Both sides must be clean before any change is declared done. The lint scripts auto-fix in place (Prettier `--write` on the FE, Pint `--dirty` on the BE), so running `npm run lint` is destructive and expected to mutate files.

- **`npm run lint`** — runs `lint:fe` then `lint:be`. Run from the host; assumes the docker stack is up so the BE half can `docker compose exec` into the `app` container.
- **`npm run lint:fe`** — Prettier `--write` then ESLint over `resources/js/**/*.{ts,tsx}`. Config in [eslint.config.mjs](eslint.config.mjs) (flat, ESLint 9 + typescript-eslint + React + hooks + `eslint-config-prettier`). Prettier config in [.prettierrc.json](.prettierrc.json); ignore list in [.prettierignore](.prettierignore).
- **`npm run lint:be`** — Pint `--dirty --format agent` inside the `app` container. Formats and lints PHP.
- **`npm run format:fe`** / **`npm run format:check:fe`** — granular Prettier write / check, available if you need only the formatter step.
- **`npm run typecheck`** — `tsc --noEmit`. Separate gate from lint; run it too before declaring an FE change done.

Prettier owns FE formatting; ESLint owns FE code quality (the two are aligned via `eslint-config-prettier`, which mutes any ESLint rule that fights Prettier). On the BE, Pint does both jobs.

This overrides the Boost guideline that says to run `vendor/bin/pint` directly — use `npm run lint:be` (or `npm run lint`) so the BE/FE workflow stays unified.

## Testing

Tests target the `mysql_test` service (database `rex_test_testing`), configured via `<env>` blocks in `phpunit.xml`. Do not change `DB_*` in `.env` for tests.

- `mysql_test` is on **tmpfs** — its data is wiped on container restart. That's intentional; do not add a volume.
- `mysql_test` runs with binlog disabled and relaxed durability flags. Never apply those flags to the dev `mysql`.
- Use `RefreshDatabase` in feature tests (it re-runs migrations per test class). Use `DatabaseTransactions` only after a one-time `migrate --env=testing`.
- Host port for the test DB is `${FORWARD_DB_TEST_PORT:-33306}` if you want to attach a GUI client.

## Business Logic — Laravel Actions

`lorisleiva/laravel-actions` is the canonical home for business logic. Every use case is a single-purpose class under `app/Actions/`. Controllers, jobs, commands, and listeners are thin shells that delegate to an Action.

- All business logic lives in `app/Actions/`. Controllers, models, service classes, and route closures must not contain business logic.
- One Action = one `handle()`. Split if it grows multiple responsibilities.
- Use the combined `Lorisleiva\Actions\Concerns\AsAction` trait. Do not import individual `AsObject` / `AsController` / etc. traits.
- Inputs and outputs are typed `Data` objects (see next section), not loose arrays.
- Name actions as imperative verb phrases: `CreateInvoice`, `SendWelcomeEmail`. Not `InvoiceService`, not `InvoiceCreator`.
- **Wrap CRUD writes in a transaction.** Any Action whose `handle()` performs more than one persistence operation (multiple inserts/updates/deletes, or a read-then-write) MUST run inside `DB::transaction(function () { ... })`. Single-statement writes are exempt but cheap to wrap; when in doubt, wrap. Do not swallow exceptions inside the closure — let them bubble so the transaction rolls back.

**Runners (decide by call site):**

| Runner         | Used as         | Invocation                                                                                                                                                                                                                                                                                                                                                                                            |
| -------------- | --------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `AsController` | HTTP endpoint   | `Route::post('/invoices', CreateInvoice::class)`. Always implement `asController()` — even as a passthrough to `$this->handle(...)` — to keep the HTTP boundary explicit and `handle()` as pure business logic. The `asController()` method is where you may convert HTTP shapes (status codes, headers, redirects) or accept request-only inputs; `handle()` is what jobs, commands, and tests call. |
| `AsJob`        | Queued job      | `CreateInvoice::dispatch($data)`. Pass IDs or `Data` snapshots, never Eloquent models.                                                                                                                                                                                                                                                                                                                |
| `AsCommand`    | Artisan command | Set `public string $commandSignature = '…';` and (if needed) `asCommand(Command $command)`.                                                                                                                                                                                                                                                                                                           |
| `AsListener`   | Event listener  | `Event::listen(SomeEvent::class, MyAction::class)`. Override `asListener()` only to reshape.                                                                                                                                                                                                                                                                                                          |
| `AsFake`       | Test fakes      | `MyAction::shouldRun()` / `::mock()` in tests.                                                                                                                                                                                                                                                                                                                                                        |
| (default)      | Direct call     | `MyAction::run($data)`.                                                                                                                                                                                                                                                                                                                                                                               |

**Tests are required for every Action.** No Action lands without at least:

- A happy-path test calling `::run(...)` directly with valid input.
- A test for each failure / edge case the Action defines (validation rejection, authorization, missing dependency, transaction rollback on a downstream error).
- A thin feature test exercising the HTTP / job / command wiring only when the Action is exposed via one of those runners — assert routing/serialization, not the business logic again.

Coverage is non-negotiable for Actions because they are the unit of business logic in this codebase. PRs that add or modify an Action without tests should not be merged.

## DTOs — spatie/laravel-data

`spatie/laravel-data` is the single source of truth for data shapes crossing boundaries (HTTP, jobs, Actions). `spatie/laravel-typescript-transformer` emits matching TS types.

- All DTOs extend `Spatie\LaravelData\Data` and live in `app/Data/`. Don't return raw arrays / stdClass from Actions or controllers.
- For request validation, type-hint the `Data` subclass directly on the Action's `handle()` — Laravel Actions resolves and laravel-data validates. No separate FormRequest classes.
- Any `Data` consumed by the frontend gets `#[Spatie\TypeScriptTransformer\Attributes\TypeScript]` so it lands in `generated.d.ts`.
- Regenerate types after touching any `#[TypeScript]` class: `docker compose exec app php artisan typescript:transform`. Never hand-edit `resources/js/types/generated.d.ts`.
- Transformer config lives in `app/Providers/TypeScriptTransformerServiceProvider.php`.
- **Error messages should be as informative as possible to API consumers who may be third parties.** Any DTO with a `rules()` method must also define a `messages()` method that overrides Laravel's defaults where they're vague (e.g. `exists`, `regex`, domain rules like `phone:AU,NZ`). State the constraint and an example of valid input where it helps. Add tests asserting the message text for the ambiguous cases so the contract doesn't silently regress.

## Frontend — TypeScript & TanStack Query

The frontend is **TypeScript** (strict mode). All new components are `.tsx`; helpers and hooks are `.ts`. No `.js`/`.jsx` files. Run `npm run lint` (which fans out to `lint:fe` + `lint:be`) and `npm run typecheck` before declaring any change — FE or BE — done.

FE tooling: Prettier owns formatting ([.prettierrc.json](.prettierrc.json) + [.prettierignore](.prettierignore)); ESLint owns code quality ([eslint.config.mjs](eslint.config.mjs) — flat, ESLint 9 + typescript-eslint + React + hooks + `eslint-config-prettier`). The generated `resources/js/types/generated.d.ts` is excluded from both. See the **Linting & Formatting** section above for the canonical command.

Server state goes through `@tanstack/react-query`. One `QueryClient` lives in `resources/js/app.tsx`; the tree is wrapped in `QueryClientProvider`.

- **No raw `fetch` / `axios` in components.** Reads use `useQuery`, writes use `useMutation`. Raw `fetch` is only allowed inside the centralized wrapper at `resources/js/lib/apiFetch.ts`.
- **No `useEffect` + `useState` for server data.** Replace with `useQuery`.
- **One hook per endpoint**, under `resources/js/queries/` (reads) or `resources/js/mutations/` (writes). Components import the hook; they don't write `useQuery` inline.
- **Query keys are namespaced arrays** of serializable values: `['ping']`, `['invoices', 'list', { status }]`, `['invoices', 'detail', id]`. Don't use plain strings.
- **Type query results against `App.Data.*`** from `resources/js/types/generated.d.ts` (declared globally — no import). Don't hand-write TS shapes for backend data — extend the backend `Data` class with `#[TypeScript]` and regenerate.
- **`apiFetch<T>(url, options)`** is the only fetch wrapper. It attaches the CSRF token from `<meta name="csrf-token">` for non-GET verbs, sets `Accept: application/json`, JSON-encodes `body`, and throws on non-2xx so TanStack's `isError` works.
- **Invalidate after mutations:** `queryClient.invalidateQueries({ queryKey: [...] })` inside `onSuccess`. Use `setQueryData` for optimistic updates. Don't manually `refetch()`.
- **Per-query loading/error UI.** No global error boundary; read `isLoading` / `isError` / `error` at the call site.
- **`QueryClient` defaults live once** in `resources/js/app.tsx` (`staleTime`, `refetchOnWindowFocus`, retry). Override per-hook only when a resource genuinely behaves differently.
- **No SSR / `HydrationBoundary`.** This is a CSR SPA.

**Layout:**

```
resources/js/
├── app.tsx                 # entry; QueryClientProvider here
├── Root.tsx                # top-level component
├── queries/                # useFooQuery() hooks
├── mutations/              # useFooMutation() hooks
├── lib/apiFetch.ts         # shared fetch wrapper
└── types/generated.d.ts    # generated; do not edit
```

**Hook skeleton:**

```ts
// resources/js/queries/usePing.ts
import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '../lib/apiFetch';

export function usePing() {
    return useQuery({
        queryKey: ['ping'],
        queryFn: () => apiFetch<App.Data.PingData>('/api/ping'),
    });
}
```

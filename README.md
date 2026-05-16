# rex_test

A Laravel 13 + React 19 SPA for managing contacts. The full stack runs in Docker Compose; the frontend tooling (Vite/Node) runs on the host.

## Tech stack

### Backend

- **PHP 8.4** with **Laravel 13**
- **MySQL 8.4** — separate `mysql` (dev, persisted) and `mysql_test` (tmpfs, fast) services
- **Redis 7** for cache, sessions, and queues
- **nginx 1.27** in front of PHP-FPM
- **[lorisleiva/laravel-actions](https://laravelactions.com)** — single-purpose action classes are the canonical home for business logic
- **[spatie/laravel-data](https://spatie.be/docs/laravel-data)** — typed DTOs at every boundary (HTTP, jobs, actions)
- **[spatie/laravel-typescript-transformer](https://spatie.be/docs/typescript-transformer)** — emits TypeScript types from `Data` classes
- **[propaganistas/laravel-phone](https://github.com/Propaganistas/Laravel-Phone)** — phone number validation/formatting (AU/NZ)
- **[laravel/prompts](https://laravel.com/docs/prompts)** — interactive console UIs (used by `contacts:console`)
- **[laravel/boost](https://github.com/laravel/boost)** — MCP server for AI agents
- **[laravel/pail](https://github.com/laravel/pail)** — tail application logs
- **[laravel/pint](https://laravel.com/docs/pint)** — PHP code formatter
- **PHPUnit 12** for tests

### Frontend

- **TypeScript** (strict mode) — all new files are `.ts` / `.tsx`
- **React 19** with **react-router-dom 7**
- **[@tanstack/react-query 5](https://tanstack.com/query)** — all server state
- **Vite 8** + **@vitejs/plugin-react**
- **Tailwind CSS 4** via `@tailwindcss/vite`
- **ESLint 9** (flat config, typescript-eslint, react, react-hooks) + **Prettier 3** (aligned via `eslint-config-prettier`)

## Project layout

```
app/
├── Actions/              # business logic (one Action per use case)
├── Contact/              # Contact domain module
├── Data/                 # spatie/laravel-data DTOs (#[TypeScript] -> generated.d.ts)
├── Http/
├── Models/
└── Providers/

resources/js/
├── app.tsx               # entry; QueryClientProvider here
├── Root.tsx              # top-level component
├── pages/
├── queries/              # one useFooQuery() hook per endpoint
├── mutations/            # one useFooMutation() hook per endpoint
├── lib/apiFetch.ts       # the only fetch wrapper
└── types/generated.d.ts  # generated; do not edit

docker/                   # Dockerfile + nginx config
```

## Prerequisites

- **Docker Desktop** (or any Docker engine with Compose v2)
- **Node.js 20+** and **npm** on the host (frontend tooling runs locally, not in a container)

That's it — PHP, Composer, MySQL, and Redis are all containerised.

## First-time setup

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Bring the stack up (builds the app image, starts mysql/redis/nginx)
docker compose up -d --build

# 3. Install PHP dependencies
docker compose exec app composer install

# 4. Generate the application key
docker compose exec app php artisan key:generate

# 5. Run migrations (and seeders if you want demo data)
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed   # optional

# 6. Generate TypeScript types from spatie/laravel-data classes
docker compose exec app php artisan typescript:transform

# 7. Install frontend dependencies (on the host, not in Docker)
npm install

# 8. Start Vite (on the host)
npm run dev
```

The app is served at **<http://localhost:8080>** (override with `APP_PORT` in `.env`).

## Daily commands

All PHP/Composer/Artisan commands run **inside the `app` container** — bare `php artisan` on the host fails because `DB_HOST=mysql` only resolves on the compose network.

| Task                | Command                                                                     |
| ------------------- | --------------------------------------------------------------------------- |
| Start stack         | `docker compose up -d`                                                      |
| Stop stack          | `docker compose down`                                                       |
| Rebuild app image   | `docker compose up -d --build app` (only after `docker/**` changes)         |
| Composer            | `docker compose exec app composer <cmd>`                                    |
| Artisan             | `docker compose exec app php artisan <cmd>`                                 |
| Tinker              | `docker compose exec app php artisan tinker`                                |
| Tail logs           | `docker compose exec app php artisan pail`                                  |
| TS type generation  | `docker compose exec app php artisan typescript:transform`                  |
| Contacts CLI        | `docker compose exec app php artisan contacts:console`                      |
| MySQL (dev)         | `docker compose exec mysql mysql -urex_test -psecret rex_test`              |
| MySQL (test)        | `docker compose exec mysql_test mysql -urex_test -psecret rex_test_testing` |
| Redis CLI           | `docker compose exec redis redis-cli`                                       |
| App shell           | `docker compose exec app sh`                                                |
| Vite dev server     | `npm run dev` (host)                                                        |
| Production build    | `npm run build` (host)                                                      |

## Testing

Tests target the dedicated `mysql_test` service (tmpfs-backed, binlog disabled — fast and ephemeral). Configured via `<env>` blocks in `phpunit.xml`; do not change `DB_*` in `.env` for tests.

```bash
# Run the whole suite
docker compose exec app php artisan test --compact

# A single file
docker compose exec app php artisan test --compact tests/Feature/ExampleTest.php

# A single test by name
docker compose exec app php artisan test --compact --filter=testCreatesContact
```

## Linting & formatting

One command formats and lints both halves of the stack. It auto-fixes in place (Prettier `--write`, Pint `--dirty`), so it will mutate files.

```bash
npm run lint        # Prettier + ESLint (frontend), then Pint (backend, via docker exec)
npm run lint:fe     # frontend only
npm run lint:be     # backend only
npm run typecheck   # tsc --noEmit (separate gate from lint)
```

Run **both `npm run lint` and `npm run typecheck`** before declaring any change done.

## Ports

| Service        | Host port (default)          | Override env var          |
| -------------- | ---------------------------- | ------------------------- |
| App (nginx)    | `8080`                       | `APP_PORT`                |
| MySQL (dev)    | `3306`                       | `FORWARD_DB_PORT`         |
| MySQL (test)   | `33306`                      | `FORWARD_DB_TEST_PORT`    |
| Redis          | `6379`                       | `FORWARD_REDIS_PORT`      |

## Troubleshooting

- **"Unable to locate file in Vite manifest"** — run `npm run dev` (or `npm run build` for a one-off prod bundle).
- **Frontend types out of sync with backend** — re-run `docker compose exec app php artisan typescript:transform`. Never hand-edit `resources/js/types/generated.d.ts`.
- **`mysql_test` data disappeared** — that's intentional. It runs on tmpfs and resets on container restart.
- **Permission errors on `storage/` or `bootstrap/cache/`** — `docker compose exec app chown -R www-data:www-data storage bootstrap/cache`.

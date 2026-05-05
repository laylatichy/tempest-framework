# Playground — Tempest under FrankenPHP worker mode

Hello-world Tempest app for exercising the framework against [FrankenPHP][frankenphp] in worker mode. Lives in the framework monorepo so the symlinked `tempest/framework` package picks up local edits instantly.

[frankenphp]: https://frankenphp.dev/

## Layout

```
playground/
├── app/
│   ├── HelloController.php       # GET /
│   └── config/database.config.php
├── public/
│   └── index.php                 # FrankenPHP worker bootstrap
├── composer.json                 # path repo -> ../ (framework root)
├── docker-compose.yaml           # app + mysql, FRANKENPHP_CONFIG sets worker mode
├── tempest                       # CLI shim
├── hooks/
│   └── migration                 # shinsenter `migration` hook -> tempest migrate:up
└── .env
```

## Prerequisites

- Docker (Orbstack / Docker Desktop)
- PHP 8.5 + Composer on host (only needed if you want to run `composer`/`tempest` outside the container)

## First run

```bash
cd playground

# install deps on host so vendor/ exists for the bind mount
composer install

# bring up app + mysql
docker compose up -d

# follow worker logs
docker compose logs -f tempest-playground-app
```

App listens on `http://localhost:20200`. MySQL on `localhost:20206` (user `app` / pass `app` / db `playground`).

## Verify worker mode

```bash
curl http://localhost:20200/
curl http://localhost:20200/
curl http://localhost:20200/
```

You should see the same `worker pid` and `worker booted at` across calls, with `requests served by this worker` incrementing `1, 2, 3, ...`. That proves the PHP process is persisted between requests instead of being torn down per-SAPI-style.

If the counter resets to 1 every call, the worker is being rebuilt — check the bootstrap (`public/index.php`) is being entered through `frankenphp_handle_request()` and not via a path that calls `Kernel::shutdown()` (which `exit()`s).

## Hot reload

`FRANKENPHP_CONFIG` in `docker-compose.yaml` declares a worker block with `watch` directives for:
- `playground/app/**/*.php`
- `playground/public/*.php`
- `packages/**/*.php`  (framework packages — symlinked through `vendor/`)
- `src/**/*.php`       (framework glue)

Edit any of those, save, re-curl. Worker restarts automatically (`max_consecutive_failures 10` keeps it from death-looping on syntax errors).

## Common commands

```bash
# CLI inside container
docker compose exec tempest-playground-app php tempest routes
docker compose exec tempest-playground-app php tempest discovery:clear

# CLI on host (uses host PHP + symlinked framework)
php tempest routes

# rebuild
docker compose down && docker compose up -d

# wipe mysql volume
docker compose down -v
```

## Teardown

```bash
docker compose down -v
```

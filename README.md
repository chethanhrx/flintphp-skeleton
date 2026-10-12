# FlintPHP Application Skeleton

Official starter for [FlintPHP](https://github.com/chethanhrx/flintphp).

Explicit composition. No facades. No automatic discovery.

## Create a project

```bash
composer create-project flintphp/skeleton my-app
cd my-app
```

> Note: skeleton tracks `flintphp/framework:^1.0`. For latest `main` features
> (`PhpFileLoader`, `setExceptionHandler`, `Response::error`), require
> `dev-main` or wait for the next tagged release.

## Run

```bash
php -S localhost:8000 -t public/
```

## Sample API

```bash
# Status
curl -s http://localhost:8000/api/status

# List users
curl -s http://localhost:8000/api/users

# Create user
curl -s -X POST http://localhost:8000/api/users \
  -H 'Content-Type: application/json' \
  -d '{"name":"Ada","email":"ada@example.com"}'

# Show user
curl -s http://localhost:8000/api/users/1

# Delete user (requires Bearer token)
curl -s -X DELETE http://localhost:8000/api/users/1 \
  -H 'Authorization: Bearer flint-demo-token'
```

Demo token: `flint-demo-token` (see `StaticTokenUserProvider`).

## Structure

| Path | Role |
|------|------|
| `public/index.php` | HTTP entrypoint |
| `bootstrap/app.php` | Application, bootstrappers, routes |
| `config/*.php` | Explicit PHP config arrays |
| `src/Http/UserController.php` | Sample API controller |
| `src/Auth/` | Demo bearer token provider |
| `storage/` | SQLite DB file |
| `bin/flint` | CLI launcher |

## Philosophy

FlintPHP favors explicit wiring over magic. Keep bootstrap readable and dependencies constructor-injected.

Framework: https://github.com/chethanhrx/flintphp

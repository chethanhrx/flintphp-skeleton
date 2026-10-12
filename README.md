# FlintPHP Application Skeleton

Official starter for [FlintPHP](https://github.com/chethanhrx/flintphp) **^1.1**.

Explicit composition. No facades. No automatic discovery.

## Create a project

```bash
composer create-project flintphp/skeleton my-app
cd my-app
composer update flintphp/framework
php -S localhost:8000 -t public/
```

If Packagist has not published 1.1.0 yet:

```bash
composer require flintphp/framework:dev-main
```

## Sample API

```bash
curl -s http://localhost:8000/api/status

curl -s -X POST http://localhost:8000/api/users \
  -H 'Content-Type: application/json' \
  -d '{"name":"Ada","email":"ada@example.com"}'

curl -s http://localhost:8000/api/users

curl -s -X DELETE http://localhost:8000/api/users/1 \
  -H 'Authorization: Bearer flint-demo-token'
```

Demo token: `flint-demo-token`

## What's included

- JSON exception handling
- SQLite users table (auto-created in `storage/`)
- Validation on `POST /api/users`
- Bearer auth on `DELETE /api/users/{id}`
- Explicit `config/app.php` + `config/database.php`

## Structure

| Path | Role |
|------|------|
| `public/index.php` | HTTP entry |
| `bootstrap/app.php` | App, bootstrappers, routes |
| `config/` | PHP config arrays |
| `src/Http/UserController.php` | Sample controller |
| `src/Auth/` | Demo token provider |
| `storage/` | SQLite database |

Framework: https://github.com/chethanhrx/flintphp

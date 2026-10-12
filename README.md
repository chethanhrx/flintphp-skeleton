# FlintPHP Application Skeleton

Official starter for [FlintPHP](https://github.com/chethanhrx/flintphp).

Explicit composition. No facades. No automatic discovery.

## Create a project

```bash
composer create-project flintphp/skeleton my-app
cd my-app
```

## Run

```bash
php -S localhost:8000 -t public/
```

Open:

- `http://localhost:8000/` → welcome JSON
- `http://localhost:8000/api/status` → status JSON

## Structure

| Path | Role |
|------|------|
| `public/index.php` | HTTP entrypoint |
| `bootstrap/app.php` | Application + explicit routes |
| `config/app.php` | Example config array |
| `.env.example` | Env template (use `FlintPHP\Framework\Config\Env`) |
| `src/` | Your `App\` code |
| `storage/` | Writable storage |
| `tests/` | PHPUnit tests |
| `bin/flint` | CLI launcher |

## Add a route

Edit `bootstrap/app.php`:

```php
$router->get('/hello', function (Request $request): Response {
    return Response::json(['hello' => 'world']);
});
```

## Environment files (explicit)

```php
use FlintPHP\Framework\Config\Env;

$env = Env::load(__DIR__ . '/../.env');
// merge into config yourself — nothing is auto-injected
```

## Philosophy

FlintPHP favors explicit wiring over magic. Keep bootstrap readable, dependencies constructor-injected, and production behavior predictable.

Framework docs: https://github.com/chethanhrx/flintphp

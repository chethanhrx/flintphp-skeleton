<?php

declare(strict_types=1);

use App\Auth\StaticTokenUserProvider;
use App\Http\UserController;
use FlintPHP\Framework\Authentication\AuthenticatorInterface;
use FlintPHP\Framework\Authentication\BearerTokenAuthenticator;
use FlintPHP\Framework\Authentication\Middleware\RequireAuthenticationMiddleware;
use FlintPHP\Framework\Authentication\UserProviderInterface;
use FlintPHP\Framework\Config\ConfigRepository;
use FlintPHP\Framework\Config\PhpFileLoader;
use FlintPHP\Framework\Database\ConnectionInterface;
use FlintPHP\Framework\Database\DatabaseBootstrapper;
use FlintPHP\Framework\Foundation\Application;
use FlintPHP\Framework\Http\Exception\JsonExceptionHandler;
use FlintPHP\Framework\Http\Request;
use FlintPHP\Framework\Http\Response;
use FlintPHP\Framework\Validation\ValidationBootstrapper;
use FlintPHP\Framework\Validation\Validator;

$basePath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Explicit configuration (no auto-discovery)
|--------------------------------------------------------------------------
*/

$configData = PhpFileLoader::loadDirectory($basePath . '/config');
$app = new Application($basePath, new ConfigRepository($configData));
$app->setExceptionHandler(new JsonExceptionHandler());

/*
|--------------------------------------------------------------------------
| Bootstrappers
|--------------------------------------------------------------------------
*/

$app->bootstrapWith([
    new DatabaseBootstrapper(),
    new ValidationBootstrapper(),
]);

/*
|--------------------------------------------------------------------------
| Demo auth: Bearer token "flint-demo-token"
|--------------------------------------------------------------------------
*/

$app->container()->singleton(
    UserProviderInterface::class,
    static fn (): UserProviderInterface => new StaticTokenUserProvider('flint-demo-token')
);

$app->container()->singleton(
    AuthenticatorInterface::class,
    static fn ($c): AuthenticatorInterface => new BearerTokenAuthenticator(
        $c->get(UserProviderInterface::class)
    )
);

$app->container()->singleton(
    RequireAuthenticationMiddleware::class,
    static fn ($c): RequireAuthenticationMiddleware => new RequireAuthenticationMiddleware(
        $c->get(AuthenticatorInterface::class)
    )
);

/*
|--------------------------------------------------------------------------
| Schema (idempotent)
|--------------------------------------------------------------------------
*/

/** @var ConnectionInterface $db */
$db = $app->container()->get(ConnectionInterface::class);
$db->execute(
    'CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        created_at TEXT NOT NULL
    )'
);

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
*/

$router = $app->router();

$router->get('/', static function (Request $request): Response {
    return Response::json([
        'name' => 'FlintPHP',
        'message' => 'Skeleton is running.',
        'endpoints' => [
            'GET /api/status',
            'GET /api/users',
            'POST /api/users',
            'GET /api/users/{id}',
            'DELETE /api/users/{id} (Bearer flint-demo-token)',
        ],
    ]);
});

$router->get('/api/status', static function (Request $request): Response {
    return Response::json([
        'status' => 'operational',
        'framework' => 'FlintPHP',
        'time' => time(),
    ]);
});

$router->get('/api/users', [UserController::class, 'index']);
$router->post('/api/users', [UserController::class, 'store']);
$router->get('/api/users/{id}', [UserController::class, 'show']);
$router->delete(
    '/api/users/{id}',
    [UserController::class, 'destroy'],
    middleware: [RequireAuthenticationMiddleware::class]
);

return $app;

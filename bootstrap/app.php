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
use FlintPHP\Framework\Health\HealthChecker;
use FlintPHP\Framework\Http\Exception\JsonExceptionHandler;
use FlintPHP\Framework\Http\Middleware\CorsMiddleware;
use FlintPHP\Framework\Http\Middleware\RequestIdMiddleware;
use FlintPHP\Framework\Http\Request;
use FlintPHP\Framework\Http\Response;
use FlintPHP\Framework\Observability\Contract\LoggerInterface;
use FlintPHP\Framework\Validation\ValidationBootstrapper;

$basePath = dirname(__DIR__);

$storageDir = $basePath . '/storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0775, true);
}

$configData = PhpFileLoader::loadDirectory($basePath . '/config');
$app = new Application($basePath, new ConfigRepository($configData));

/** @var LoggerInterface $logger */
$logger = $app->container()->get(LoggerInterface::class);
$app->setExceptionHandler(new JsonExceptionHandler($logger));

$app->bootstrapWith([
    new DatabaseBootstrapper(),
    new ValidationBootstrapper(),
]);

// Production-oriented global middleware (explicit registration)
$app->container()->singleton(RequestIdMiddleware::class, static fn () => new RequestIdMiddleware());
$app->container()->singleton(
    CorsMiddleware::class,
    static fn () => new CorsMiddleware(
        allowedOrigins: ['*'], // tighten in production
    )
);
$app->addMiddleware(RequestIdMiddleware::class);
$app->addMiddleware(CorsMiddleware::class);

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

$router = $app->router();

$router->get('/', static function (Request $request): Response {
    return Response::json([
        'name' => 'FlintPHP',
        'message' => 'Skeleton is running.',
        'endpoints' => [
            'GET /health',
            'GET /api/status',
            'GET /api/users',
            'POST /api/users',
            'GET /api/users/{id}',
            'DELETE /api/users/{id} (Bearer flint-demo-token)',
        ],
    ]);
});

$router->get('/health', static function () use ($db): Response {
    return HealthChecker::withDatabase($db)->toResponse();
});

$router->get('/api/status', static function (Request $request): Response {
    return Response::json([
        'status' => 'operational',
        'framework' => 'FlintPHP',
        'time' => time(),
        'request_id' => $request->attribute(\FlintPHP\Framework\Http\Middleware\RequestIdMiddleware::ATTRIBUTE),
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

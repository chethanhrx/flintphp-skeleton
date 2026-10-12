<?php

declare(strict_types=1);

use FlintPHP\Framework\Foundation\Application;
use FlintPHP\Framework\Http\Exception\JsonExceptionHandler;
use FlintPHP\Framework\Http\Request;
use FlintPHP\Framework\Http\Response;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Explicit composition root. No service discovery, no facades.
|
*/

$app = new Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| API-friendly exception boundary (must be set before kernel() is used)
|--------------------------------------------------------------------------
*/

$app->setExceptionHandler(new JsonExceptionHandler());

/*
|--------------------------------------------------------------------------
| Routes (explicit registration)
|--------------------------------------------------------------------------
*/

$router = $app->router();

$router->get('/', static function (Request $request): Response {
    return Response::json([
        'name' => 'FlintPHP',
        'message' => 'Skeleton is running.',
    ]);
});

$router->get('/api/status', static function (Request $request): Response {
    return Response::json([
        'status' => 'operational',
        'framework' => 'FlintPHP',
        'time' => time(),
    ]);
});

return $app;

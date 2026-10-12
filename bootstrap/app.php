<?php

declare(strict_types=1);

use FlintPHP\Framework\Foundation\Application;
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

/*
| Example of a controlled API error:
|
| $router->get('/api/fail', static function (): Response {
|     return Response::error('Something went wrong', 400, 'bad_request');
| });
*/

return $app;

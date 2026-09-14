<?php

declare(strict_types=1);

use FlintPHP\Framework\Http\Request;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Boot The Application
|--------------------------------------------------------------------------
|
| We need to bootstrap the application and load its configurations and
| routes. The application will be ready to handle requests afterwards.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->boot();

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$request = Request::fromGlobals();

$response = $app->kernel()->handle($request);

$response->send();

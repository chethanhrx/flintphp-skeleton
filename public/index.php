<?php

declare(strict_types=1);

use FlintPHP\Framework\Http\Request;

require __DIR__ . '/../vendor/autoload.php';

/** @var \FlintPHP\Framework\Foundation\Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';
$app->boot();

$request = Request::fromGlobals();
$response = $app->kernel()->handle($request);
$response->send();

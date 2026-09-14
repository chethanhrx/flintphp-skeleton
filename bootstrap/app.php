<?php

declare(strict_types=1);

use FlintPHP\Framework\Foundation\Application;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we create the FlintPHP Application instance. It coordinates the
| bootstrap process and holds the application's base path.
|
*/

$app = new Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

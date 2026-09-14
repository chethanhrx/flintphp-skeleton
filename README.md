# FlintPHP Application Skeleton

This repository contains the official application skeleton for the FlintPHP framework.

> **Note:** This repository is the application starter. The underlying framework code is located at [flintphp/framework](https://github.com/chethanhrx/flintphp).

## Create a FlintPHP application

Create a new FlintPHP project using Composer:

```bash
composer create-project flintphp/skeleton my-app
cd my-app
php bin/flint
```

## Run the application

You can run the application using PHP's built-in development server:

```bash
php -S localhost:8000 -t public/
```

Then visit `http://localhost:8000` in your browser.

## Structure

- `bin/flint`: The command-line launcher for the application.
- `bootstrap/app.php`: Bootstraps the application and returns the Application instance.
- `config/`: Directory for your application's configuration files.
- `public/index.php`: The HTTP entry point for all web requests.
- `src/`: Your application source code (PSR-4 autoloaded under the `App\` namespace).
- `storage/`: Directory for generated files, logs, and other application storage.
- `tests/`: Directory for your application's test suite.

## Philosophy

FlintPHP uses explicit composition and avoids unnecessary magic or discovery. The application structure remains deliberately minimal and production-oriented, providing a fast, secure, and modern PHP foundation.

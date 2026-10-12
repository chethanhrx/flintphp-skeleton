<?php

declare(strict_types=1);

namespace App\Tests;

use FlintPHP\Framework\Foundation\Application;
use FlintPHP\Framework\Http\Request;
use PHPUnit\Framework\TestCase;

class SkeletonTest extends TestCase
{
    private string $skeletonDir;

    protected function setUp(): void
    {
        $this->skeletonDir = dirname(__DIR__);
    }

    public function testComposerJsonIsValid(): void
    {
        $composerJsonPath = $this->skeletonDir . '/composer.json';
        $this->assertFileExists($composerJsonPath);

        $json = json_decode((string) file_get_contents($composerJsonPath), true);
        $this->assertIsArray($json);
        $this->assertSame('flintphp/skeleton', $json['name']);
        $this->assertArrayHasKey('flintphp/framework', $json['require']);
    }

    public function testExpectedFilesExist(): void
    {
        $this->assertFileExists($this->skeletonDir . '/bin/flint');
        $this->assertFileExists($this->skeletonDir . '/bootstrap/app.php');
        $this->assertFileExists($this->skeletonDir . '/public/index.php');
        $this->assertFileExists($this->skeletonDir . '/config/app.php');
        $this->assertFileExists($this->skeletonDir . '/config/database.php');
        $this->assertFileExists($this->skeletonDir . '/src/Http/UserController.php');
        $this->assertFileExists($this->skeletonDir . '/.env.example');
    }

    public function testBootstrapLoadsAndApplicationCanBeConstructed(): void
    {
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $this->assertInstanceOf(Application::class, $app);
    }

    public function testStatusRoute(): void
    {
        /** @var Application $app */
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $response = $app->kernel()->handle(new Request('GET', '/api/status', [], '', [], [], [], [], null));

        $this->assertSame(200, $response->status());
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('operational', $payload['status']);
    }

    public function testUsersListRoute(): void
    {
        /** @var Application $app */
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $response = $app->kernel()->handle(new Request('GET', '/api/users', [], '', [], [], [], [], null));

        $this->assertSame(200, $response->status());
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('data', $payload);
    }

    public function testNotFoundIsJson(): void
    {
        /** @var Application $app */
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $response = $app->kernel()->handle(new Request('GET', '/missing', [], '', [], [], [], [], null));

        $this->assertSame(404, $response->status());
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Not Found', $payload['error']['message']);
    }

    public function testDeleteRequiresAuth(): void
    {
        /** @var Application $app */
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $response = $app->kernel()->handle(new Request('DELETE', '/api/users/1', [], '', [], [], [], [], null));

        $this->assertSame(401, $response->status());
    }

    public function testPhpBinFlintListWorks(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php ' . escapeshellarg($this->skeletonDir . '/bin/flint') . ' list', $output, $returnVar);

        $this->assertSame(0, $returnVar);
    }
}

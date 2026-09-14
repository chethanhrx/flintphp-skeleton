<?php

declare(strict_types=1);

namespace App\Tests;

use FlintPHP\Framework\Foundation\Application;
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
        
        $json = json_decode(file_get_contents($composerJsonPath), true);
        $this->assertIsArray($json);
        $this->assertEquals('flintphp/skeleton', $json['name']);
        
        // Framework dependency is declared correctly
        $this->assertArrayHasKey('flintphp/framework', $json['require']);
    }

    public function testExpectedFilesExist(): void
    {
        $this->assertFileExists($this->skeletonDir . '/bin/flint');
        $this->assertFileExists($this->skeletonDir . '/bootstrap/app.php');
        $this->assertFileExists($this->skeletonDir . '/public/index.php');
        $this->assertFileExists($this->skeletonDir . '/.env.example');
        $this->assertFileExists($this->skeletonDir . '/.gitignore');
    }

    public function testBootstrapLoadsAndApplicationCanBeConstructed(): void
    {
        $app = require $this->skeletonDir . '/bootstrap/app.php';
        $this->assertInstanceOf(Application::class, $app);
    }

    public function testCliLauncherIsExecutable(): void
    {
        $this->assertTrue(is_executable($this->skeletonDir . '/bin/flint'));
    }

    public function testPhpBinFlintWorks(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php ' . escapeshellarg($this->skeletonDir . '/bin/flint') . ' 2>&1', $output, $returnVar);
        
        $this->assertEquals(1, $returnVar); // No command provided -> returns 1
        $this->assertStringContainsString('No command provided', implode("\n", $output));
    }

    public function testPhpBinFlintListWorks(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php ' . escapeshellarg($this->skeletonDir . '/bin/flint') . ' list', $output, $returnVar);
        
        $this->assertEquals(0, $returnVar);
    }

    public function testPhpBinFlintHelpWorks(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php ' . escapeshellarg($this->skeletonDir . '/bin/flint') . ' help', $output, $returnVar);
        
        $this->assertEquals(0, $returnVar);
    }

    public function testUnknownCliCommandReturnsNonZero(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php ' . escapeshellarg($this->skeletonDir . '/bin/flint') . ' unknown_command 2>&1', $output, $returnVar);
        
        $this->assertEquals(1, $returnVar);
    }

    public function testHttpEntryPointCanBeLoadedWithoutSyntaxErrors(): void
    {
        $output = [];
        $returnVar = -1;
        exec('php -l ' . escapeshellarg($this->skeletonDir . '/public/index.php'), $output, $returnVar);
        
        $this->assertEquals(0, $returnVar);
        $this->assertStringContainsString('No syntax errors detected', implode("\n", $output));
    }
}

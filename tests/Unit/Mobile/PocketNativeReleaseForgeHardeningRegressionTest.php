<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativeReleaseForgeHardeningRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function testReleaseForgePreflightsWithoutSecretsAndRedactsSignedBuildDiagnostics(): void
    {
        $builder = file_get_contents($this->root . '/native/pocket-companion/scripts/build-android-release.mjs');
        self::assertIsString($builder);

        self::assertStringContainsString("delete preflightEnv[name]", $builder);
        self::assertStringContainsString("gradleArgs('help')", $builder);
        self::assertStringContainsString("gradleArgs('bundleRelease')", $builder);
        self::assertStringContainsString("'[REDACTED]'", $builder);
        self::assertStringContainsString('(storePassword=)', $builder);
        self::assertStringContainsString('(keyPassword=)', $builder);
        self::assertStringNotContainsString('shell:', $builder);
        self::assertStringContainsString("process.env.ComSpec || 'cmd.exe'", $builder);
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketNativeReleaseForgeRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function testReleaseIdentityAndFirstVersionAreSourceControlled(): void
    {
        $release = file_get_contents($this->root . '/native/pocket-companion/config/release.json');
        self::assertIsString($release);
        self::assertStringContainsString('"applicationId": "uk.co.greatmarketrealm.pocket"', $release);
        self::assertStringContainsString('"versionCode": 1', $release);
        self::assertStringContainsString('"versionName": "1.0.0"', $release);
    }

    public function testPrepareAppliesReleaseConfigurationAfterCapacitorSync(): void
    {
        $prepare = file_get_contents($this->root . '/native/pocket-companion/scripts/prepare-android.mjs');
        self::assertIsString($prepare);
        self::assertStringContainsString("runCapacitor('sync', 'android');", $prepare);
        self::assertStringContainsString('configure-android-release.mjs', $prepare);
    }

    public function testSigningSecretsComeFromEnvironmentRatherThanRepository(): void
    {
        $configure = file_get_contents($this->root . '/native/pocket-companion/scripts/configure-android-release.mjs');
        $ignore = file_get_contents($this->root . '/.gitignore');
        self::assertIsString($configure);
        self::assertIsString($ignore);
        self::assertStringContainsString('System.getenv("GMRC_UPLOAD_STORE_PASSWORD")', $configure);
        self::assertStringContainsString('System.getenv("GMRC_UPLOAD_KEY_PASSWORD")', $configure);
        self::assertStringContainsString('*.jks', $ignore);
        self::assertStringContainsString('*.keystore', $ignore);
    }

    public function testBundleCommandFailsClosedWithoutSigningMaterial(): void
    {
        $builder = file_get_contents($this->root . '/native/pocket-companion/scripts/build-android-release.mjs');
        self::assertIsString($builder);
        self::assertStringContainsString("'GMRC_UPLOAD_STORE_FILE'", $builder);
        self::assertStringContainsString("'bundleRelease'", $builder);
        self::assertStringContainsString('Keep these values outside Git.', $builder);
    }

    public function testReleaseForgeDocumentsPlayAppSigningBoundary(): void
    {
        $doc = file_get_contents($this->root . '/docs/mobile/phase-iii-m7a-release-forge.md');
        self::assertIsString($doc);
        self::assertStringContainsString('dedicated **upload key**', $doc);
        self::assertStringContainsString('Google Play App Signing', $doc);
        self::assertStringContainsString('npm run android:bundle', $doc);
    }
}

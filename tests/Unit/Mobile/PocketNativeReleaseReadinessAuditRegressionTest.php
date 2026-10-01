<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeReleaseReadinessAuditRegressionTest extends TestCase
{
    private function root(): string
    {
        return dirname(__DIR__, 3);
    }

    public function test_release_audit_is_available_from_the_native_workshop(): void
    {
        $package = file_get_contents($this->root() . '/native/pocket-companion/package.json');
        $audit = file_get_contents($this->root() . '/native/pocket-companion/scripts/release-audit.mjs');

        self::assertIsString($package);
        self::assertStringContainsString('"release:audit": "node scripts/release-audit.mjs"', $package);
        self::assertIsString($audit);
        self::assertStringContainsString('target >= 36', $audit);
        self::assertStringContainsString('compile >= 36', $audit);
    }

    public function test_release_audit_guards_privacy_and_native_architecture_contracts(): void
    {
        $audit = file_get_contents($this->root() . '/native/pocket-companion/scripts/release-audit.mjs');

        self::assertStringContainsString('nativeCharacterDatabase === false', $audit);
        self::assertStringContainsString('offlineCharacterWrites === false', $audit);
        self::assertStringContainsString('wordpressPasswordInNativeApp === false', $audit);
        self::assertStringContainsString('ACCESS_(FINE|COARSE|BACKGROUND)_LOCATION', $audit);
        self::assertStringContainsString('RECORD_AUDIO|CAMERA|READ_CONTACTS|WRITE_CONTACTS', $audit);
        self::assertStringContainsString('usesCleartextTraffic', $audit);
    }

    public function test_release_identity_remains_first_play_release(): void
    {
        $release = json_decode((string) file_get_contents($this->root() . '/native/pocket-companion/config/release.json'), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('uk.co.greatmarketrealm.pocket', $release['applicationId']);
        self::assertSame('1.0.0', $release['versionName']);
        self::assertSame(1, $release['versionCode']);
        self::assertTrue($release['playAppSigning']);
    }
}

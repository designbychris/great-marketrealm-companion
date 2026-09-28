<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeWorkshopRegressionTest extends TestCase
{
    public function testNativeWorkshopPreservesGmrcAsTheAuthority(): void
    {
        $root = __DIR__ . '/../../../';
        $package = file_get_contents($root . 'native/pocket-companion/package.json');
        $capacitor = file_get_contents($root . 'native/pocket-companion/capacitor.config.json');
        $environment = file_get_contents($root . 'native/pocket-companion/config/environments.json');
        $workshop = file_get_contents($root . 'native/pocket-companion/www/index.html');
        $bridge = file_get_contents($root . 'app/Mobile/PocketNativeBridge.php');

        self::assertStringContainsString('"@capacitor/core": "^8.0.0"', $package);
        self::assertStringContainsString('"node": ">=22"', $package);
        self::assertStringContainsString('"appId": "uk.co.greatmarketrealm.pocket"', $capacitor);
        self::assertStringNotContainsString('"url"', $capacitor);
        self::assertStringContainsString('"gmrcIsAuthoritative": true', $environment);
        self::assertStringContainsString('"nativeCharacterDatabase": false', $environment);
        self::assertStringContainsString('"offlineCharacterWrites": false', $environment);
        self::assertStringContainsString('"authenticationPhase": "III.M.6B"', $environment);
        self::assertStringContainsString('Native authentication', $workshop);
        self::assertStringContainsString("CONTRACT_VERSION = '1.0'", $bridge);
        self::assertStringContainsString("'same_origin_required' => true", $bridge);
    }
}

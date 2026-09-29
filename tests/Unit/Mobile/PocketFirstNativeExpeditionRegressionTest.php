<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketFirstNativeExpeditionRegressionTest extends TestCase
{
    public function testNativeWorkshopImplementsFirstAndroidGuildGateRoundTrip(): void
    {
        $root = __DIR__ . '/../../../';
        $package = file_get_contents($root . 'native/pocket-companion/package.json');
        $client = file_get_contents($root . 'native/pocket-companion/src/main.js');
        $prepare = file_get_contents($root . 'native/pocket-companion/scripts/prepare-android.mjs');
        $config = file_get_contents($root . 'native/pocket-companion/capacitor.config.json');
        $nativeGate = file_get_contents($root . 'native/pocket-companion/index.html');

        self::assertStringContainsString('"@capacitor/app": "^8.0.0"', $package);
        self::assertStringContainsString('"@capacitor/browser": "^8.0.0"', $package);
        self::assertStringContainsString('"webDir": "dist"', $config);
        self::assertStringContainsString("App.addListener('appUrlOpen'", $client);
        self::assertStringContainsString("Browser.open({ url: data.authorize_url })", $client);
        self::assertStringContainsString('/wp-json/gmrc-pocket/v1/native/token', $client);
        self::assertStringContainsString('/wp-json/gmrc-pocket/v1/session', $client);
        self::assertStringContainsString('let accessToken = null', $client);
        self::assertStringNotContainsString('localStorage', $client);
        self::assertStringNotContainsString('Preferences.set', $client);
        self::assertStringContainsString('android:scheme="uk.co.greatmarketrealm.pocket"', $prepare);
        self::assertStringContainsString('android:host="auth"', $prepare);
        self::assertStringContainsString('android:path="/callback"', $prepare);
        self::assertStringContainsString('execFileSync(process.execPath', $prepare);
        self::assertStringContainsString('@capacitor/cli/bin/capacitor', $prepare);
        self::assertStringNotContainsString('npx.cmd', $prepare);

        self::assertStringContainsString('/auby-pocket.png', $nativeGate);
        self::assertStringContainsString('Auby, Keeper of the Kingdoms', $nativeGate);
        self::assertStringNotContainsString('🍆', $nativeGate);
        self::assertStringContainsString("resources/android/", $prepare);
        self::assertFileExists($root . 'native/pocket-companion/public/auby-pocket.png');
        self::assertFileExists($root . 'native/pocket-companion/resources/android/mipmap-xxxhdpi/ic_launcher.png');
        self::assertFileExists($root . 'assets/images/pocket/auby-pocket-master.png');
        self::assertFileExists($root . 'assets/images/pocket/app-icon-192.png');
        self::assertFileExists($root . 'assets/images/pocket/app-icon-512.png');
    }
}

<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeKeyMemoryRegressionTest extends TestCase
{
    public function testNativePocketPersistsOnlyTheBearerTokenInPlatformSecureStorage(): void
    {
        $root = __DIR__ . '/../../../';
        $package = file_get_contents($root . 'native/pocket-companion/package.json');
        $client = file_get_contents($root . 'native/pocket-companion/src/main.js');

        self::assertStringContainsString('"@aparajita/capacitor-secure-storage": "^8.0.1"', $package);
        self::assertStringContainsString("import { SecureStorage } from '@aparajita/capacitor-secure-storage'", $client);
        self::assertStringContainsString("Capacitor.isNativePlatform()", $client);
        self::assertStringContainsString("SecureStorage.setKeyPrefix(STORAGE_PREFIX)", $client);
        self::assertStringContainsString("SecureStorage.set(TOKEN_KEY, candidate)", $client);
        self::assertStringContainsString("SecureStorage.get(TOKEN_KEY)", $client);
        self::assertStringContainsString("SecureStorage.remove(TOKEN_KEY)", $client);
        self::assertStringNotContainsString('localStorage', $client);
        self::assertStringNotContainsString('Preferences.set', $client);
        self::assertStringNotContainsString('password', strtolower($client));
    }

    public function testNativePocketRestoresValidSessionAndForgetsInvalidOrRevokedKeys(): void
    {
        $root = __DIR__ . '/../../../';
        $client = file_get_contents($root . 'native/pocket-companion/src/main.js');

        self::assertStringContainsString('async function restoreSession()', $client);
        self::assertStringContainsString('const session = await verifySession(stored)', $client);
        self::assertStringContainsString("showSignedOut('Your Guild key needs renewing.')", $client);
        self::assertStringContainsString('await forgetStoredToken()', $client);
        self::assertStringContainsString("method: 'POST'", $client);
        self::assertStringContainsString('Authorization: `Bearer ${token}`', $client);
        self::assertStringContainsString("showSignedOut('The Guild key has been returned to Auby.')", $client);
    }

    public function testAubyHasDistinctGateAndAuthenticatedSuccessStates(): void
    {
        $root = __DIR__ . '/../../../';
        $client = file_get_contents($root . 'native/pocket-companion/src/main.js');
        $gate = file_get_contents($root . 'native/pocket-companion/index.html');

        self::assertFileExists($root . 'native/pocket-companion/public/auby-success.png');
        self::assertStringContainsString('id="auby-state"', $gate);
        self::assertStringContainsString('href="/auby-success.png"', $gate);
        self::assertStringContainsString("auby.src = success ? '/auby-success.png' : '/auby-pocket.png'", $client);
        self::assertStringContainsString('giving a happy thumbs up', $client);
        self::assertStringContainsString('The Keeper remembers your key.', $client);
        self::assertStringContainsString('Auby is checking your Guild key…', $client);
    }
}

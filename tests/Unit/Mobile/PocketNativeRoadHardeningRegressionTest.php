<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeRoadHardeningRegressionTest extends TestCase
{
    public function testNativePocketSurfacesOfflineStateWithoutHidingTheOpenCharacter(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $gate = file_get_contents($root . '/native/pocket-companion/index.html');

        self::assertIsString($client);
        self::assertIsString($gate);
        self::assertStringContainsString('id="connection-banner"', $gate);
        self::assertStringContainsString("window.addEventListener('offline'", $client);
        self::assertStringContainsString("window.addEventListener('online'", $client);
        self::assertStringContainsString('Your open character remains available.', $gate);
        self::assertStringNotContainsString("showView('gate');\n  updateConnectionState", $client);
    }

    public function testLiveMutationsFailClosedWhenTheRoadIsOffline(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString("requireOnline('Updating Adventuring Measures')", $client);
        self::assertStringContainsString("requireOnline('Changing a spell reserve')", $client);
        self::assertStringContainsString('needs a connection. Nothing has been changed.', $client);
        self::assertStringContainsString("cache: 'no-store'", $client);
        self::assertStringNotContainsString('localStorage', $client);
    }

    public function testResumeRevalidatesAStaleGuildKeyWithoutDiscardingItForTemporaryNetworkFailure(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString("App.addListener('appStateChange'", $client);
        self::assertStringContainsString('const RESUME_REVALIDATE_AFTER_MS = 60 * 1000;', $client);
        self::assertStringContainsString('async function revalidateOnResume()', $client);
        self::assertStringContainsString("showSignedOut('Your Guild key needs renewing.')", $client);
        self::assertStringContainsString('A temporary road failure must never discard a valid stored key or open character.', $client);
    }
}

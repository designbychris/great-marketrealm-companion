<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativePrivacyPullRefreshRegressionTest extends TestCase
{
    public function testRegisterAndOpenLedgerShareAuthoritativePullToRefresh(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('function installPullToRefresh(view, refresh)', $client);
        self::assertStringContainsString('installPullToRefresh(registerView, openAdventurersRegister);', $client);
        self::assertStringContainsString('installPullToRefresh(characterView, refreshOpenCharacter);', $client);
        self::assertStringContainsString('async function refreshOpenCharacter()', $client);
        self::assertStringContainsString('const characters = await fetchCharacters();', $client);
        self::assertStringContainsString("cache: 'no-store'", $client);
        self::assertStringContainsString("if (!isOnline())", $client);
        self::assertStringNotContainsString('localStorage', $client);
    }

    public function testCharacterRefreshPreservesTheActiveTabAndDiceMemory(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString('const tab = activeCharacterTab();', $client);
        self::assertStringContainsString('const rememberedHistory = [...diceHistory];', $client);
        self::assertStringContainsString('const rememberedMode = diceMode;', $client);
        self::assertStringContainsString('openCharacter(id, tab);', $client);
        self::assertStringContainsString('diceHistory = rememberedHistory;', $client);
        self::assertStringContainsString('diceMode = rememberedMode;', $client);
    }

    public function testMoreProvidesCanonicalPrivacyAndSupportDestinations(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');

        self::assertIsString($client);
        self::assertStringContainsString("const PRIVACY_URL = 'https://greatmarketrealm.co.uk/the-pocket-companion/privacy/';", $client);
        self::assertStringContainsString("const SUPPORT_URL = 'https://greatmarketrealm.co.uk/support/';", $client);
        self::assertStringContainsString('function privacySupportPanel()', $client);
        self::assertStringContainsString('Privacy Policy', $client);
        self::assertStringContainsString('Support', $client);
        self::assertStringContainsString('await Browser.open({ url });', $client);
        $gate = file_get_contents($root . '/native/pocket-companion/index.html');
        self::assertIsString($gate);
        self::assertStringContainsString('id="gate-privacy"', $gate);
        self::assertStringContainsString('id="gate-support"', $gate);
    }
}

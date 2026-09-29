<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketNativeAdventurersRegisterRegressionTest extends TestCase
{
    public function testNativeRegisterUsesExistingOwnerScopedPocketCharactersContract(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $api = file_get_contents($root . '/app/Mobile/PocketApi.php');

        self::assertStringContainsString("const CHARACTERS = `${ORIGIN}/wp-json/gmrc-pocket/v1/characters`;", $client);
        self::assertStringContainsString('Authorization: `Bearer ${accessToken}`', $client);
        self::assertStringContainsString("cache: 'no-store'", $client);
        self::assertStringContainsString('Repository::all() is explicitly scoped to the authenticated WP user.', $api);
        self::assertStringContainsString("'Cache-Control' => 'private, no-store'", $api);
    }

    public function testRegisterPresentsCanonicalCharacterIdentityPortraitAndMeasures(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $html = file_get_contents($root . '/native/pocket-companion/index.html');

        self::assertStringContainsString('Adventurers&#039; Register', str_replace("'", '&#039;', $html));
        self::assertStringContainsString('character.portrait', $client);
        self::assertStringContainsString('character.name', $client);
        self::assertStringContainsString('character.race', $client);
        self::assertStringContainsString('character.class', $client);
        self::assertStringContainsString('character.level', $client);
        self::assertStringContainsString('character.hp?.current', $client);
        self::assertStringContainsString('character.armour_class', $client);
        self::assertStringContainsString('character.abilities', $client);
    }

    public function testFirstNativeLedgerRemainsReadOnlyAndDoesNotPersistCharacterData(): void
    {
        $root = dirname(__DIR__, 3);
        $client = file_get_contents($root . '/native/pocket-companion/src/main.js');
        $html = file_get_contents($root . '/native/pocket-companion/index.html');

        self::assertStringNotContainsString("SecureStorage.set('character", $client);
        self::assertStringNotContainsString('localStorage', $client);
        self::assertStringNotContainsString('sessionStorage', $client);
        self::assertStringContainsString('This first native ledger is read-only.', $client);
        self::assertStringContainsString('platform-secure storage', $html);
    }
}

<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketAppShellRegressionTest extends TestCase
{
    public function testPocketAppShellPreservesCharacterAndResourceControls(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('gmrc-pocket-nav', $source);
        self::assertStringContainsString('data-pocket-nav', $source);
        self::assertStringContainsString('function showDetail(character)', $source);
        self::assertStringContainsString('changeSpellSlot(character,slot,action)', $source);
        self::assertStringContainsString('pocketDiceworks()', $source);
    }

    public function testPocketGateRetainsExistingSecureAuthenticationFlow(): void
    {
        $pocket = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        $gate = file_get_contents(__DIR__ . '/../../../app/Modules/GuildGate/Views/index.php');
        self::assertIsString($pocket);
        self::assertIsString($gate);
        self::assertStringContainsString("'return_route' => 'pocket'", $pocket);
        self::assertStringContainsString('gmrc-guild-gate--pocket', $gate);
        self::assertStringContainsString("wp_nonce_field('gmrc_guild_gate_login'", $gate);
        self::assertStringContainsString('gmrc-guild-gate__turnstile', $gate);
    }
}

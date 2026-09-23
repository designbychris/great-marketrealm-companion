<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketGuildGateReadableLabelsRegressionTest extends TestCase
{
    public function testPocketLoginLabelsHaveExplicitReadableColourWithoutChangingAuthentication(): void
    {
        $css = file_get_contents(__DIR__ . '/../../../assets/css/modules/guild-gate/guild-gate.css');
        $view = file_get_contents(__DIR__ . '/../../../app/Modules/GuildGate/Views/index.php');
        self::assertIsString($css);
        self::assertIsString($view);
        self::assertStringContainsString('.gmrc-guild-gate--pocket .gmrc-guild-gate__folio label,', $css);
        self::assertStringContainsString('.gmrc-guild-gate--pocket .gmrc-guild-gate__folio .gmrc-guild-gate__remember span', $css);
        self::assertStringContainsString('color: #304a35;', $css);
        self::assertStringContainsString('for="gmrc-gate-login"', $view);
        self::assertStringContainsString('for="gmrc-gate-password"', $view);
        self::assertStringContainsString("wp_nonce_field('gmrc_guild_gate_login'", $view);
    }
}

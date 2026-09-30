<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

final class PocketGuildGateViewInitiativeRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 3);
    }

    public function testNativeGuildGateUsesCanonicalMarketrealmArtworkAndReadableCard(): void
    {
        $html = file_get_contents($this->root . '/native/pocket-companion/index.html');
        $css = file_get_contents($this->root . '/native/pocket-companion/src/native.css');

        self::assertIsString($html);
        self::assertIsString($css);
        self::assertFileExists($this->root . '/native/pocket-companion/public/guild-gate-view.webp');
        self::assertStringContainsString('class="gate-scene"', $html);
        self::assertStringContainsString('url("/guild-gate-view.webp")', $css);
        self::assertStringContainsString('background:rgba(7,22,16,.76)', $css);
    }

    public function testPocketApiProjectsAuthoritativeNumericInitiativeModifier(): void
    {
        $api = file_get_contents($this->root . '/app/Mobile/PocketApi.php');
        self::assertIsString($api);
        self::assertStringContainsString("'initiative_modifier' => $character->initiative()->modifier()", $api);
    }

    public function testNativeInitiativeRollUsesSharedDiceworksAndProjectedModifier(): void
    {
        $script = file_get_contents($this->root . '/native/pocket-companion/src/main.js');
        self::assertIsString($script);
        self::assertStringContainsString('data-initiative-roll', $script);
        self::assertStringContainsString("performTrainingRoll('Initiative', Number(character.initiative_modifier ?? 0), 'initiative')", $script);
    }

    public function testBrowserPocketInitiativeUsesExistingDiceworksWithAuthoritativeModifier(): void
    {
        $page = file_get_contents($this->root . '/app/Mobile/PocketPage.php');
        self::assertIsString($page);
        self::assertStringContainsString("rollAbility('Initiative',Number(character.initiative_modifier))", $page);
        self::assertStringNotContainsString("Math.floor((Number(character.initiative", $page);
    }

    public function testNativeLiveActionsTranslateFetchFailuresIntoPocketLanguage(): void
    {
        $script = file_get_contents($this->root . '/native/pocket-companion/src/main.js');
        self::assertIsString($script);
        self::assertStringContainsString('function liveActionError(error, action)', $script);
        self::assertStringContainsString("liveActionError(error, 'Adventuring Measures')", $script);
        self::assertStringContainsString("liveActionError(error, 'The spell-slot ledger')", $script);
        self::assertStringContainsString('Nothing has been changed; try again when the road is clear.', $script);
    }

    public function testDesktopLedgerAlreadyUsesAuthoritativeInitiativeRollTrigger(): void
    {
        $view = file_get_contents($this->root . '/app/Modules/Characters/Views/show.php');
        self::assertIsString($view);
        self::assertStringContainsString("'label' => 'Initiative'", $view);
        self::assertStringContainsString("'modifier' => $initiativeValue->modifier()", $view);
        self::assertStringContainsString("'kind' => 'initiative'", $view);
    }
}

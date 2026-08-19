<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use PHPUnit\Framework\TestCase;

final class PaladinSacredActionsBrowserHardeningRegressionTest extends TestCase
{
    public function testGuildDiceUsesLedgerLevelDelegationForRollTriggers(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/guild-dice.js'
        );

        self::assertStringContainsString(
            "ledger.addEventListener('click'",
            $script
        );

        self::assertStringContainsString(
            "target.closest(",
            $script
        );

        self::assertStringContainsString(
            "'[data-guild-roll]'",
            $script
        );

        self::assertStringContainsString(
            'openTray(trigger)',
            $script
        );
    }

    public function testGuildDiceNoLongerDependsOnPerTriggerClickBindings(): void
    {
        $script = $this->source(
            'assets/js/modules/characters/guild-dice.js'
        );

        self::assertStringNotContainsString(
            "triggers.forEach(function (trigger) {\n"
            . "            trigger.addEventListener('click'",
            $script
        );
    }

    public function testSmiteButtonRetainsStandardGuildDiceContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-guild-roll="damage"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-source="Divine Smite"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-damage-type="radiant"',
            $view
        );

        self::assertStringContainsString(
            'Roll Smite',
            $view
        );
    }

    public function testSmiteLayoutUsesReadableResponsiveActionRows(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-sacred-smite-option',
            $css
        );

        self::assertStringContainsString(
            'grid-template-columns:',
            $css
        );

        self::assertStringContainsString(
            '@media (min-width: 680px)',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 679px)',
            $css
        );

        self::assertStringContainsString(
            'white-space: normal',
            $css
        );
    }

    public function testLayOnHandsKeepsSelfAndExternalRecipientBoundaryClear(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Heal this Paladin',
            $view
        );

        self::assertStringContainsString(
            'Record spend for another creature',
            $view
        );

        self::assertStringContainsString(
            'name="target"',
            $view
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
            $this->root()
            . '/'
            . $relative
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDicePresentationTest extends TestCase
{
    public function testOpenLedgerIncludesGuildDiceTrayAndRollTriggers(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString('data-guild-dice-tray', $view);
        self::assertStringContainsString('components.controls.guild-roll-trigger', $view);
        self::assertStringContainsString("'label' => 'Initiative'", $view);
        self::assertStringContainsString('$label . \' Saving Throw\'', $view);
        self::assertStringContainsString('$label . \' Check\'', $view);
    }

    public function testLedgerProvidesCharacterAwareRollContext(): void
    {
        $root = dirname(__DIR__, 5);
        $view = file_get_contents(
            $root . '/app/Modules/Characters/Views/show.php'
        );
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($view);
        self::assertIsString($script);
        self::assertStringContainsString('data-guild-roll-context', $view);
        self::assertStringContainsString('data-guild-context-kind', $view);
        self::assertStringContainsString('data-guild-context-source', $view);
        self::assertStringContainsString('data-guild-context-ability', $view);
        self::assertStringContainsString('data-guild-context-proficiency', $view);
        self::assertStringContainsString("'kind' => 'initiative'", $view);
        self::assertStringContainsString("'kind' => 'ability-check'", $view);
        self::assertStringContainsString("'kind' => 'saving-throw'", $view);
        self::assertStringContainsString("'kind' => 'skill-check'", $view);
        self::assertStringContainsString('Skills::governingAbility($identifier)', $view);
        self::assertStringContainsString('data-roll-kind="attack"', $view);
        self::assertStringContainsString('data-roll-kind="spell-attack"', $view);
        self::assertStringContainsString('const contextSummary = function (selection)', $script);
        self::assertStringContainsString('const paintContext = function (selection)', $script);
        self::assertStringContainsString("return parts.join(' · ');", $script);
    }

    public function testRollTriggerExposesGenericFutureReadyContract(): void
    {
        $root = dirname(__DIR__, 5);
        $component = file_get_contents(
            $root . '/app/Views/components/controls/guild-roll-trigger.php'
        );

        self::assertIsString($component);
        self::assertStringContainsString('data-guild-roll="d20"', $component);
        self::assertStringContainsString('data-roll-label=', $component);
        self::assertStringContainsString('data-roll-modifier=', $component);
        self::assertStringContainsString('data-roll-kind=', $component);
        self::assertStringContainsString('data-roll-source=', $component);
        self::assertStringContainsString('data-roll-ability=', $component);
        self::assertStringContainsString('data-roll-proficiency=', $component);
    }
}

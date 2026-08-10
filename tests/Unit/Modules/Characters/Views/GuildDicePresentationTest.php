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
    }
}

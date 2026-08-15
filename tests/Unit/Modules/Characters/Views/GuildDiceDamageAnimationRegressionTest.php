<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceDamageAnimationRegressionTest extends TestCase
{
    public function testDamageAndD20RollsUseSharedDiceStageAnimation(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'guild-dice.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'const paintDice = function (',
            $script
        );

        self::assertStringContainsString(
            'paintDice(rolled.dice, rolled.sides, null);',
            $script
        );

        self::assertStringContainsString(
            'paintDice(rolled.dice, 20, rolled.keptIndex);',
            $script
        );

        self::assertStringContainsString(
            "stage.classList.add('is-rolling');",
            $script
        );
    }
}

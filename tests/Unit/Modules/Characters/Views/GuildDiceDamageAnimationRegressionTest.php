<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceDamageAnimationRegressionTest extends TestCase
{
    public function testDamageRollUsesSharedDiceAnimation(): void
    {
        $root = dirname(__DIR__, 5);

        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/'
            . 'guild-dice.js'
        );

        self::assertIsString($script);

        self::assertStringContainsString(
            'const animateDie = function (',
            $script
        );

        self::assertStringContainsString(
            "animateDie(\n"
            . "                    damage.total",
            $script
        );

        self::assertStringContainsString(
            "animateDie(\n"
            . "                rolled.natural,",
            $script
        );

        self::assertStringContainsString(
            "die.classList.add(\n"
            . "                    'is-rolling'",
            $script
        );
    }
}

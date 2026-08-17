<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class GuildDiceTargetingLifecycleRegressionTest extends TestCase
{
    public function testTargetListenersAreBoundOnceOutsideSituationalReset(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);

        $resetStart = strpos(
            $script,
            'const resetSituational = function ()'
        );
        $resetEnd = strpos(
            $script,
            'const readableKind = function (kind)',
            $resetStart
        );

        self::assertIsInt($resetStart);
        self::assertIsInt($resetEnd);

        $resetBlock = substr(
            $script,
            $resetStart,
            $resetEnd - $resetStart
        );

        self::assertStringNotContainsString(
            "targetKind.addEventListener",
            $resetBlock
        );
        self::assertStringNotContainsString(
            "targetName.addEventListener",
            $resetBlock
        );

        self::assertSame(
            1,
            substr_count(
                $script,
                "targetKind.addEventListener('change'"
            )
        );
        self::assertSame(
            1,
            substr_count(
                $script,
                "targetName.addEventListener('input'"
            )
        );
    }

    public function testTargetAwareD20AndFormulaResultsPaintTheirTarget(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root . '/assets/js/modules/characters/guild-dice.js'
        );

        self::assertIsString($script);
        self::assertSame(
            2,
            substr_count(
                $script,
                'paintTargetResult(target);'
            )
        );
        $formulaStart = strpos(
            $script,
            'const performFormula = function (selection)'
        );
        $formulaEnd = strpos(
            $script,
            'const performD20 = function (selection, mode)',
            $formulaStart
        );

        self::assertIsInt($formulaStart);
        self::assertIsInt($formulaEnd);

        $formulaBlock = substr(
            $script,
            $formulaStart,
            $formulaEnd - $formulaStart
        );

        self::assertSame(
            1,
            substr_count(
                $formulaBlock,
                'paintTargetResult(target);'
            )
        );

        $d20Start = $formulaEnd;
        $d20End = strpos(
            $script,
            'const performCriticalDamage = function ()',
            $d20Start
        );

        self::assertIsInt($d20End);

        $d20Block = substr(
            $script,
            $d20Start,
            $d20End - $d20Start
        );

        self::assertSame(
            1,
            substr_count(
                $d20Block,
                'paintTargetResult(target);'
            )
        );

        self::assertStringContainsString(
            'paintTargetResult(critical.target || null);',
            $script
        );
    }
}

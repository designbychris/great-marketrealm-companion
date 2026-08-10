<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Scripts;

use PHPUnit\Framework\TestCase;

final class DiceOfDestinyScriptContractTest extends TestCase
{
    public function testScriptRollsThreeD6AndUpdatesRealAbilityFields(): void
    {
        $root = dirname(__DIR__, 5);
        $script = file_get_contents(
            $root
            . '/assets/js/modules/characters/dice-of-destiny.js'
        );

        self::assertIsString($script);
        self::assertStringContainsString('secureD6', $script);
        self::assertStringContainsString(
            '[secureD6(), secureD6(), secureD6()]',
            $script
        );
        self::assertStringContainsString(
            'selectScore(select, total)',
            $script
        );
        self::assertStringContainsString(
            "querySelectorAll('[data-destiny-ability]')",
            $script
        );
        self::assertStringContainsString(
            'window.crypto.getRandomValues',
            $script
        );
    }
}

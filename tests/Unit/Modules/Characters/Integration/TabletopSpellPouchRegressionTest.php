<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Integration;

use PHPUnit\Framework\TestCase;

final class TabletopSpellPouchRegressionTest extends TestCase
{
    public function testBridgeProjectsAvailableCharacterSpellEntriesWithCastingMeasures(): void
    {
        $source = file_get_contents(dirname(__DIR__, 5) . '/app/Modules/Characters/Services/TabletopCharacterBridge.php');
        self::assertIsString($source);
        self::assertStringContainsString('ArcanePantryPresenter', $source);
        self::assertStringContainsString("'spellcasting' => [", $source);
        self::assertStringContainsString("['cantrip', 'spell']", $source);
        self::assertStringNotContainsString('&& ! empty($entry[\'learned\'])', $source);
        self::assertStringContainsString("'spell_attack'", $source);
        self::assertStringContainsString("'save_dc'", $source);
        self::assertStringContainsString("'slots'", $source);
    }
}

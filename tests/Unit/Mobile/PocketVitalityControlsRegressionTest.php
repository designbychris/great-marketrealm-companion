<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Mobile;

use PHPUnit\Framework\TestCase;

/** Guard the mobile damage/healing contract without requiring a browser runtime. */
final class PocketVitalityControlsRegressionTest extends TestCase
{
    private function source(): string
    {
        $source = file_get_contents(dirname(__DIR__, 3) . '/app/Mobile/PocketPage.php');
        self::assertIsString($source);
        return $source;
    }

    public function testDamageConsumesTemporaryHpBeforeCurrentHp(): void
    {
        $source = $this->source();
        self::assertStringContainsString("Math.min(temporary,n)", $source);
        self::assertStringContainsString("Math.max(0,current-(n-absorbed))", $source);
    }

    public function testHealingIsCappedAtMaximumAndDoesNotReplaceTemporaryHp(): void
    {
        $source = $this->source();
        self::assertStringContainsString("Math.min(Number(hp.maximum),current+n)", $source);
        self::assertStringContainsString("kind==='damage'?nextTemp:temporary", $source);
    }

    public function testQuickActionsUseExistingSaveAndStaleStateProtection(): void
    {
        $source = $this->source();
        self::assertStringContainsString('await saveVitality(character,current,temporary)', $source);
        self::assertStringContainsString('if(busy||stale)return;', $source);
        self::assertStringContainsString('currentCharacters=characters', $source);
    }
}

<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketSpellkeeperRegisterRegressionTest extends TestCase
{
    public function testLegacyAndArcanePantryIdentitiesAreResolvedWithoutGuessing(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString("'market-missile' => 'mystery-mustard-missile'", $source);
        self::assertStringContainsString('new ArcaneAbilityCatalogue()', $source);
        self::assertStringContainsString('$ability->id() === $identifier', $source);
        self::assertStringContainsString("'level' => \$record?->level() ?? \$arcane?->spellLevel()", $source);
        self::assertStringContainsString("'resolved' => \$record !== null || \$arcane !== null", $source);
    }

    public function testScalingSpellIsNotPresentedAsAFlatAutomaticRoll(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString('$arcane->slotLevelScaling() !== []', $source);
        self::assertStringContainsString('$arcane->characterLevelScaling() !== []', $source);
        self::assertStringContainsString("'formula' => \$scales ? null : \$formula", $source);
    }
}

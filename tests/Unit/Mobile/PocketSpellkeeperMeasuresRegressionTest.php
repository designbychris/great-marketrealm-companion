<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PocketSpellkeeperMeasuresRegressionTest extends TestCase
{
    public function testPocketUsesCanonicalCastingPresenterAndOwnerScopedResourceState(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketApi.php');
        self::assertIsString($source);
        self::assertStringContainsString('new ArcanePantryPresenter($arcaneCatalogue)', $source);
        self::assertStringContainsString('new ActiveClassResourceRepository())->find($character->id())', $source);
        self::assertStringContainsString('new SharedSpellSlotReserveService())->present($character, $slotState)', $source);
    }

    public function testPocketDisplaysSlotsReadOnlyAndRollsSpellAttacks(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../app/Mobile/PocketPage.php');
        self::assertIsString($source);
        self::assertStringContainsString('Spell slots · read only', $source);
        self::assertStringContainsString("diceTray.rollAbility('Spell attack',casting.attack_bonus)", $source);
        self::assertStringContainsString('Casting or rolling a spell does not expend a slot.', $source);
    }
}

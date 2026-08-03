<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrow;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrows;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SavingThrowsTest extends TestCase
{
    public function testCreatesAllSavingThrowsFromAbilityScores(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );

        self::assertSame(
            2,
            $savingThrows->strength()->modifier()
        );

        self::assertSame(
            1,
            $savingThrows->dexterity()->modifier()
        );

        self::assertSame(
            1,
            $savingThrows->constitution()->modifier()
        );

        self::assertSame(
            0,
            $savingThrows->intelligence()->modifier()
        );

        self::assertSame(
            -1,
            $savingThrows->wisdom()->modifier()
        );

        self::assertSame(
            -2,
            $savingThrows->charisma()->modifier()
        );
    }

    public function testAddsProficiencyToSelectedAbilities(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'strength',
                'constitution',
            ]
        );

        self::assertSame(
            4,
            $savingThrows->strength()->modifier()
        );

        self::assertSame(
            3,
            $savingThrows->constitution()->modifier()
        );

        self::assertTrue(
            $savingThrows->strength()->isProficient()
        );

        self::assertTrue(
            $savingThrows->constitution()->isProficient()
        );

        self::assertFalse(
            $savingThrows->dexterity()->isProficient()
        );
    }

    public function testNormalisesProficiencyNames(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                ' STRENGTH ',
                'Constitution',
            ]
        );

        self::assertSame(
            [
                'strength',
                'constitution',
            ],
            $savingThrows->proficiencies()
        );
    }

    public function testRemovesDuplicateProficiencies(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'strength',
                'Strength',
                ' strength ',
            ]
        );

        self::assertSame(
            ['strength'],
            $savingThrows->proficiencies()
        );
    }

    public function testRejectsUnsupportedProficiency(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The saving-throw ability "luck" is not supported.'
        );

        SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['luck']
        );
    }

    public function testRejectsNonStringProficiency(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Saving Throw proficiency identifiers must be strings.'
        );

        SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [123]
        );
    }

    public function testRetrievesSavingThrowByAbilityName(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );

        self::assertSame(
            $savingThrows->strength(),
            $savingThrows->get('strength')
        );

        self::assertSame(
            $savingThrows->wisdom(),
            $savingThrows->get(' WISDOM ')
        );
    }

    public function testGetRejectsUnsupportedAbility(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );

        $savingThrows->get('luck');
    }

    public function testReturnsAllSavingThrowsInAbilityOrder(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );

        self::assertSame(
            [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ],
            array_keys(
                $savingThrows->all()
            )
        );

        self::assertContainsOnlyInstancesOf(
            SavingThrow::class,
            $savingThrows->all()
        );
    }

    public function testCreatesCollectionFromExistingSavingThrows(): void
    {
        $savingThrows = SavingThrows::fromThrows(
            SavingThrow::fromModifier(4, true),
            SavingThrow::fromModifier(1),
            SavingThrow::fromModifier(3, true),
            SavingThrow::fromModifier(0),
            SavingThrow::fromModifier(-1),
            SavingThrow::fromModifier(-2)
        );

        self::assertSame(
            4,
            $savingThrows->strength()->modifier()
        );

        self::assertSame(
            3,
            $savingThrows->constitution()->modifier()
        );

        self::assertSame(
            [
                'strength',
                'constitution',
            ],
            $savingThrows->proficiencies()
        );
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'strength',
                'constitution',
            ]
        );

        $second = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'strength',
                'constitution',
            ]
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCollectionsAreNotEqual(): void
    {
        $first = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['strength']
        );

        $second = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['wisdom']
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testProficiencyBonusAffectsOnlyProficientThrows(): void
    {
        $savingThrows = SavingThrows::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(5),
            ['strength']
        );

        self::assertSame(
            7,
            $savingThrows->strength()->modifier()
        );

        self::assertSame(
            1,
            $savingThrows->dexterity()->modifier()
        );
    }

    private function abilityScores(): AbilityScores
    {
        return AbilityScores::fromScores(
            strength: AbilityScore::fromInt(14),
            dexterity: AbilityScore::fromInt(12),
            constitution: AbilityScore::fromInt(13),
            intelligence: AbilityScore::fromInt(10),
            wisdom: AbilityScore::fromInt(8),
            charisma: AbilityScore::fromInt(6),
        );
    }
}

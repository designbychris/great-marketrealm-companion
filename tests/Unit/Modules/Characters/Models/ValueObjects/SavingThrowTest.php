<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SavingThrow;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SavingThrowTest extends TestCase
{
    #[DataProvider('validModifierProvider')]
    public function testCreatesSavingThrowFromModifier(
        int $modifier,
        bool $proficient
    ): void {
        $savingThrow = SavingThrow::fromModifier(
            $modifier,
            $proficient
        );

        self::assertSame(
            $modifier,
            $savingThrow->modifier()
        );

        self::assertSame(
            $modifier,
            $savingThrow->value()
        );

        self::assertSame(
            $proficient,
            $savingThrow->isProficient()
        );
    }

    /**
     * @return array<string,array{int,bool}>
     */
    public static function validModifierProvider(): array
    {
        return [
            'minimum' => [-20, false],
            'negative' => [-3, false],
            'zero' => [0, false],
            'positive' => [5, true],
            'maximum' => [30, true],
        ];
    }

    #[DataProvider('invalidModifierProvider')]
    public function testRejectsUnsupportedModifier(
        int $modifier
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Saving Throw must be between -20 and 30; received %d.',
                $modifier
            )
        );

        SavingThrow::fromModifier(
            $modifier
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidModifierProvider(): array
    {
        return [
            'below minimum' => [-21],
            'far below minimum' => [-100],
            'above maximum' => [31],
            'far above maximum' => [100],
        ];
    }

    #[DataProvider('nonProficientAbilityProvider')]
    public function testCalculatesNonProficientSavingThrow(
        int $abilityScore,
        int $expectedModifier
    ): void {
        $savingThrow = SavingThrow::fromAbility(
            AbilityScore::fromInt($abilityScore),
            ProficiencyBonus::fromInt(2),
            false
        );

        self::assertSame(
            $expectedModifier,
            $savingThrow->modifier()
        );

        self::assertFalse(
            $savingThrow->isProficient()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function nonProficientAbilityProvider(): array
    {
        return [
            'score 1' => [1, -5],
            'score 8' => [8, -1],
            'score 10' => [10, 0],
            'score 14' => [14, 2],
            'score 20' => [20, 5],
        ];
    }

    #[DataProvider('proficientAbilityProvider')]
    public function testAddsProficiencyBonus(
        int $abilityScore,
        int $proficiencyBonus,
        int $expectedModifier
    ): void {
        $savingThrow = SavingThrow::fromAbility(
            AbilityScore::fromInt($abilityScore),
            ProficiencyBonus::fromInt(
                $proficiencyBonus
            ),
            true
        );

        self::assertSame(
            $expectedModifier,
            $savingThrow->modifier()
        );

        self::assertTrue(
            $savingThrow->isProficient()
        );
    }

    /**
     * @return array<string,array{int,int,int}>
     */
    public static function proficientAbilityProvider(): array
    {
        return [
            'level one average score' => [
                10,
                2,
                2,
            ],
            'level one strong score' => [
                16,
                2,
                5,
            ],
            'higher proficiency' => [
                14,
                4,
                6,
            ],
            'maximum proficiency' => [
                20,
                6,
                11,
            ],
        ];
    }

    #[DataProvider('signedModifierProvider')]
    public function testFormatsSignedModifier(
        int $modifier,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            SavingThrow::fromModifier(
                $modifier
            )->signed()
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function signedModifierProvider(): array
    {
        return [
            'negative' => [-3, '-3'],
            'zero' => [0, '+0'],
            'positive' => [5, '+5'],
        ];
    }

    public function testEqualSavingThrowsAreEqual(): void
    {
        self::assertTrue(
            SavingThrow::fromModifier(
                4,
                true
            )->equals(
                SavingThrow::fromModifier(
                    4,
                    true
                )
            )
        );
    }

    public function testDifferentModifiersAreNotEqual(): void
    {
        self::assertFalse(
            SavingThrow::fromModifier(
                4,
                true
            )->equals(
                SavingThrow::fromModifier(
                    5,
                    true
                )
            )
        );
    }

    public function testDifferentProficiencyStatesAreNotEqual(): void
    {
        self::assertFalse(
            SavingThrow::fromModifier(
                4,
                true
            )->equals(
                SavingThrow::fromModifier(
                    4,
                    false
                )
            )
        );
    }

    public function testReturnsSupportedBounds(): void
    {
        self::assertSame(
            -20,
            SavingThrow::minimum()
        );

        self::assertSame(
            30,
            SavingThrow::maximum()
        );
    }

    public function testConvertsToSignedString(): void
    {
        self::assertSame(
            '+4',
            (string) SavingThrow::fromModifier(4)
        );

        self::assertSame(
            '-2',
            (string) SavingThrow::fromModifier(-2)
        );
    }

    public function testSavingThrowIsImmutable(): void
    {
        $first = SavingThrow::fromModifier(
            2,
            false
        );

        $second = SavingThrow::fromModifier(
            5,
            true
        );

        self::assertSame(
            2,
            $first->modifier()
        );

        self::assertFalse(
            $first->isProficient()
        );

        self::assertSame(
            5,
            $second->modifier()
        );

        self::assertTrue(
            $second->isProficient()
        );

        self::assertNotSame(
            $first,
            $second
        );
    }
}

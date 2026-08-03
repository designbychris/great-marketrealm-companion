<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ProficiencyBonusTest extends TestCase
{
    #[DataProvider('validBonusProvider')]
    public function testCreatesAProficiencyBonusFromAnInteger(
        int $value
    ): void {
        self::assertSame(
            $value,
            ProficiencyBonus::fromInt(
                $value
            )->value()
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function validBonusProvider(): array
    {
        return [
            'minimum' => [2],
            'three' => [3],
            'four' => [4],
            'five' => [5],
            'maximum' => [6],
        ];
    }

    #[DataProvider('invalidBonusProvider')]
    public function testRejectsAnUnsupportedBonus(
        int $value
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Proficiency Bonus must be between 2 and 6; received %d.',
                $value
            )
        );

        ProficiencyBonus::fromInt($value);
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidBonusProvider(): array
    {
        return [
            'below minimum' => [1],
            'zero' => [0],
            'negative' => [-1],
            'above maximum' => [7],
            'far above maximum' => [100],
        ];
    }

    #[DataProvider('levelProvider')]
    public function testCalculatesTheBonusFromLevel(
        int $level,
        int $expectedBonus
    ): void {
        $bonus = ProficiencyBonus::fromLevel(
            Level::fromInt($level)
        );

        self::assertSame(
            $expectedBonus,
            $bonus->value()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function levelProvider(): array
    {
        return [
            'level 1' => [1, 2],
            'level 2' => [2, 2],
            'level 3' => [3, 2],
            'level 4' => [4, 2],

            'level 5' => [5, 3],
            'level 6' => [6, 3],
            'level 7' => [7, 3],
            'level 8' => [8, 3],

            'level 9' => [9, 4],
            'level 10' => [10, 4],
            'level 11' => [11, 4],
            'level 12' => [12, 4],

            'level 13' => [13, 5],
            'level 14' => [14, 5],
            'level 15' => [15, 5],
            'level 16' => [16, 5],

            'level 17' => [17, 6],
            'level 18' => [18, 6],
            'level 19' => [19, 6],
            'level 20' => [20, 6],
        ];
    }

    public function testEqualBonusesAreEqual(): void
    {
        self::assertTrue(
            ProficiencyBonus::fromInt(3)->equals(
                ProficiencyBonus::fromInt(3)
            )
        );
    }

    public function testDifferentBonusesAreNotEqual(): void
    {
        self::assertFalse(
            ProficiencyBonus::fromInt(3)->equals(
                ProficiencyBonus::fromInt(4)
            )
        );
    }

    public function testReturnsTheMinimumSupportedValue(): void
    {
        self::assertSame(
            2,
            ProficiencyBonus::minimum()
        );
    }

    public function testReturnsTheMaximumSupportedValue(): void
    {
        self::assertSame(
            6,
            ProficiencyBonus::maximum()
        );
    }

    #[DataProvider('signedValueProvider')]
    public function testReturnsASignedValue(
        int $value,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            ProficiencyBonus::fromInt(
                $value
            )->signed()
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function signedValueProvider(): array
    {
        return [
            'two' => [2, '+2'],
            'three' => [3, '+3'],
            'four' => [4, '+4'],
            'five' => [5, '+5'],
            'six' => [6, '+6'],
        ];
    }

    #[DataProvider('stringValueProvider')]
    public function testConvertsToAString(
        int $value,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            (string) ProficiencyBonus::fromInt(
                $value
            )
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function stringValueProvider(): array
    {
        return [
            'minimum' => [2, '2'],
            'middle' => [4, '4'],
            'maximum' => [6, '6'],
        ];
    }

    public function testFromLevelReturnsAProficiencyBonus(): void
    {
        self::assertInstanceOf(
            ProficiencyBonus::class,
            ProficiencyBonus::fromLevel(
                Level::fromInt(7)
            )
        );
    }

    public function testCalculationUsesLevelBandsOfFour(): void
    {
        self::assertSame(
            2,
            ProficiencyBonus::fromLevel(
                Level::fromInt(4)
            )->value()
        );

        self::assertSame(
            3,
            ProficiencyBonus::fromLevel(
                Level::fromInt(5)
            )->value()
        );

        self::assertSame(
            5,
            ProficiencyBonus::fromLevel(
                Level::fromInt(16)
            )->value()
        );

        self::assertSame(
            6,
            ProficiencyBonus::fromLevel(
                Level::fromInt(17)
            )->value()
        );
    }

    public function testValueObjectIsImmutable(): void
    {
        $first = ProficiencyBonus::fromInt(2);
        $second = ProficiencyBonus::fromInt(5);

        self::assertSame(
            2,
            $first->value()
        );

        self::assertSame(
            5,
            $second->value()
        );

        self::assertNotSame(
            $first,
            $second
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ArmourClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArmourClassTest extends TestCase
{
    #[DataProvider('validArmourClassProvider')]
    public function testCreatesAnArmourClassFromAnInteger(
        int $value
    ): void {
        $armourClass = ArmourClass::fromInt(
            $value
        );

        self::assertSame(
            $value,
            $armourClass->value()
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function validArmourClassProvider(): array
    {
        return [
            'minimum' => [0],
            'very low' => [5],
            'average' => [10],
            'armoured' => [15],
            'high' => [20],
            'very high' => [25],
            'maximum' => [30],
        ];
    }

    public function testAcceptsTheMinimumArmourClass(): void
    {
        self::assertSame(
            0,
            ArmourClass::fromInt(0)->value()
        );
    }

    public function testAcceptsTheMaximumArmourClass(): void
    {
        self::assertSame(
            30,
            ArmourClass::fromInt(30)->value()
        );
    }

    #[DataProvider('invalidArmourClassProvider')]
    public function testRejectsAnUnsupportedArmourClass(
        int $value
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Armour Class must be between 0 and 30; received %d.',
                $value
            )
        );

        ArmourClass::fromInt($value);
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidArmourClassProvider(): array
    {
        return [
            'below minimum' => [-1],
            'far below minimum' => [-100],
            'above maximum' => [31],
            'far above maximum' => [100],
        ];
    }

    #[DataProvider('unarmouredArmourClassProvider')]
    public function testCalculatesUnarmouredArmourClass(
        int $dexterity,
        int $expectedArmourClass
    ): void {
        $armourClass = ArmourClass::unarmoured(
            AbilityScore::fromInt(
                $dexterity
            )
        );

        self::assertSame(
            $expectedArmourClass,
            $armourClass->value()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function unarmouredArmourClassProvider(): array
    {
        return [
            'dexterity 1 gives minus five' => [
                1,
                5,
            ],
            'dexterity 2 gives minus four' => [
                2,
                6,
            ],
            'dexterity 6 gives minus two' => [
                6,
                8,
            ],
            'dexterity 8 gives minus one' => [
                8,
                9,
            ],
            'dexterity 10 gives zero' => [
                10,
                10,
            ],
            'dexterity 11 gives zero' => [
                11,
                10,
            ],
            'dexterity 12 gives plus one' => [
                12,
                11,
            ],
            'dexterity 14 gives plus two' => [
                14,
                12,
            ],
            'dexterity 16 gives plus three' => [
                16,
                13,
            ],
            'dexterity 18 gives plus four' => [
                18,
                14,
            ],
            'dexterity 20 gives plus five' => [
                20,
                15,
            ],
        ];
    }

    public function testEqualArmourClassesAreEqual(): void
    {
        self::assertTrue(
            ArmourClass::fromInt(15)->equals(
                ArmourClass::fromInt(15)
            )
        );
    }

    public function testDifferentArmourClassesAreNotEqual(): void
    {
        self::assertFalse(
            ArmourClass::fromInt(15)->equals(
                ArmourClass::fromInt(16)
            )
        );
    }

    public function testReturnsTheMinimumSupportedValue(): void
    {
        self::assertSame(
            0,
            ArmourClass::minimum()
        );
    }

    public function testReturnsTheMaximumSupportedValue(): void
    {
        self::assertSame(
            30,
            ArmourClass::maximum()
        );
    }

    #[DataProvider('stringValueProvider')]
    public function testConvertsToAString(
        int $value,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            (string) ArmourClass::fromInt(
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
            'zero' => [
                0,
                '0',
            ],
            'average' => [
                10,
                '10',
            ],
            'armoured' => [
                16,
                '16',
            ],
            'maximum' => [
                30,
                '30',
            ],
        ];
    }

    public function testUnarmouredCalculationReturnsAnArmourClass(): void
    {
        self::assertInstanceOf(
            ArmourClass::class,
            ArmourClass::unarmoured(
                AbilityScore::fromInt(14)
            )
        );
    }

    public function testUnarmouredArmourClassUsesDexterityModifierRatherThanScore(): void
    {
        $armourClass = ArmourClass::unarmoured(
            AbilityScore::fromInt(14)
        );

        self::assertSame(
            12,
            $armourClass->value()
        );

        self::assertNotSame(
            24,
            $armourClass->value()
        );
    }

    public function testArmourClassIsImmutable(): void
    {
        $original = ArmourClass::fromInt(12);

        $other = ArmourClass::fromInt(18);

        self::assertSame(
            12,
            $original->value()
        );

        self::assertSame(
            18,
            $other->value()
        );

        self::assertNotSame(
            $original,
            $other
        );
    }
}

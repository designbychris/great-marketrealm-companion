<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PassivePerception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PassivePerceptionTest extends TestCase
{
    #[DataProvider('validValueProvider')]
    public function testCreatesPassivePerceptionFromAnInteger(
        int $value
    ): void {
        self::assertSame(
            $value,
            PassivePerception::fromInt(
                $value
            )->value()
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function validValueProvider(): array
    {
        return [
            'minimum' => [0],
            'very low' => [5],
            'average' => [10],
            'aware' => [15],
            'high' => [20],
            'very high' => [30],
            'maximum' => [40],
        ];
    }

    #[DataProvider('invalidValueProvider')]
    public function testRejectsAnUnsupportedValue(
        int $value
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Passive Perception must be between 0 and 40; received %d.',
                $value
            )
        );

        PassivePerception::fromInt(
            $value
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidValueProvider(): array
    {
        return [
            'below minimum' => [-1],
            'far below minimum' => [-100],
            'above maximum' => [41],
            'far above maximum' => [100],
        ];
    }

    #[DataProvider('wisdomProvider')]
    public function testCalculatesPassivePerceptionFromWisdom(
        int $wisdom,
        int $expected
    ): void {
        $passivePerception =
            PassivePerception::fromWisdom(
                AbilityScore::fromInt(
                    $wisdom
                )
            );

        self::assertSame(
            $expected,
            $passivePerception->value()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function wisdomProvider(): array
    {
        return [
            'wisdom 1' => [1, 5],
            'wisdom 2' => [2, 6],
            'wisdom 6' => [6, 8],
            'wisdom 8' => [8, 9],
            'wisdom 10' => [10, 10],
            'wisdom 11' => [11, 10],
            'wisdom 12' => [12, 11],
            'wisdom 14' => [14, 12],
            'wisdom 16' => [16, 13],
            'wisdom 18' => [18, 14],
            'wisdom 20' => [20, 15],
        ];
    }

    public function testFromWisdomReturnsPassivePerception(): void
    {
        self::assertInstanceOf(
            PassivePerception::class,
            PassivePerception::fromWisdom(
                AbilityScore::fromInt(14)
            )
        );
    }

    public function testUsesWisdomModifierRatherThanWisdomScore(): void
    {
        $passivePerception =
            PassivePerception::fromWisdom(
                AbilityScore::fromInt(14)
            );

        self::assertSame(
            12,
            $passivePerception->value()
        );

        self::assertNotSame(
            24,
            $passivePerception->value()
        );
    }

    public function testEqualValuesAreEqual(): void
    {
        self::assertTrue(
            PassivePerception::fromInt(12)->equals(
                PassivePerception::fromInt(12)
            )
        );
    }

    public function testDifferentValuesAreNotEqual(): void
    {
        self::assertFalse(
            PassivePerception::fromInt(12)->equals(
                PassivePerception::fromInt(13)
            )
        );
    }

    public function testReturnsTheMinimumSupportedValue(): void
    {
        self::assertSame(
            0,
            PassivePerception::minimum()
        );
    }

    public function testReturnsTheMaximumSupportedValue(): void
    {
        self::assertSame(
            40,
            PassivePerception::maximum()
        );
    }

    #[DataProvider('stringValueProvider')]
    public function testConvertsToAString(
        int $value,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            (string) PassivePerception::fromInt(
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
            'minimum' => [0, '0'],
            'average' => [10, '10'],
            'high' => [20, '20'],
            'maximum' => [40, '40'],
        ];
    }

    public function testValueObjectIsImmutable(): void
    {
        $first = PassivePerception::fromInt(10);
        $second = PassivePerception::fromInt(15);

        self::assertSame(
            10,
            $first->value()
        );

        self::assertSame(
            15,
            $second->value()
        );

        self::assertNotSame(
            $first,
            $second
        );
    }
}

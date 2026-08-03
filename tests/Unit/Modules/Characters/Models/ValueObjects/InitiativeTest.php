<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Initiative;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InitiativeTest extends TestCase
{
    #[DataProvider('validModifierProvider')]
    public function testCreatesInitiativeFromAModifier(
        int $modifier
    ): void {
        $initiative = Initiative::fromModifier(
            $modifier
        );

        self::assertSame(
            $modifier,
            $initiative->modifier()
        );

        self::assertSame(
            $modifier,
            $initiative->value()
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function validModifierProvider(): array
    {
        return [
            'minimum' => [-10],
            'negative' => [-3],
            'minus one' => [-1],
            'zero' => [0],
            'plus one' => [1],
            'positive' => [5],
            'maximum' => [20],
        ];
    }

    #[DataProvider('invalidModifierProvider')]
    public function testRejectsAnUnsupportedModifier(
        int $modifier
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Initiative must be between -10 and 20; received %d.',
                $modifier
            )
        );

        Initiative::fromModifier(
            $modifier
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidModifierProvider(): array
    {
        return [
            'below minimum' => [-11],
            'far below minimum' => [-100],
            'above maximum' => [21],
            'far above maximum' => [100],
        ];
    }

    #[DataProvider('dexterityProvider')]
    public function testCalculatesInitiativeFromDexterity(
        int $dexterity,
        int $expectedModifier
    ): void {
        $initiative = Initiative::fromDexterity(
            AbilityScore::fromInt(
                $dexterity
            )
        );

        self::assertSame(
            $expectedModifier,
            $initiative->modifier()
        );
    }

    /**
     * @return array<string,array{int,int}>
     */
    public static function dexterityProvider(): array
    {
        return [
            'dexterity 1' => [1, -5],
            'dexterity 2' => [2, -4],
            'dexterity 6' => [6, -2],
            'dexterity 8' => [8, -1],
            'dexterity 10' => [10, 0],
            'dexterity 11' => [11, 0],
            'dexterity 12' => [12, 1],
            'dexterity 14' => [14, 2],
            'dexterity 16' => [16, 3],
            'dexterity 18' => [18, 4],
            'dexterity 20' => [20, 5],
        ];
    }

    #[DataProvider('signedValueProvider')]
    public function testReturnsASignedModifier(
        int $modifier,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            Initiative::fromModifier(
                $modifier
            )->signed()
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function signedValueProvider(): array
    {
        return [
            'negative' => [-3, '-3'],
            'minus one' => [-1, '-1'],
            'zero' => [0, '+0'],
            'plus one' => [1, '+1'],
            'positive' => [5, '+5'],
        ];
    }

    public function testEqualInitiativesAreEqual(): void
    {
        self::assertTrue(
            Initiative::fromModifier(2)->equals(
                Initiative::fromModifier(2)
            )
        );
    }

    public function testDifferentInitiativesAreNotEqual(): void
    {
        self::assertFalse(
            Initiative::fromModifier(2)->equals(
                Initiative::fromModifier(3)
            )
        );
    }

    public function testReturnsTheMinimumSupportedModifier(): void
    {
        self::assertSame(
            -10,
            Initiative::minimum()
        );
    }

    public function testReturnsTheMaximumSupportedModifier(): void
    {
        self::assertSame(
            20,
            Initiative::maximum()
        );
    }

    #[DataProvider('stringValueProvider')]
    public function testConvertsToASignedString(
        int $modifier,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            (string) Initiative::fromModifier(
                $modifier
            )
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function stringValueProvider(): array
    {
        return [
            'negative' => [-2, '-2'],
            'zero' => [0, '+0'],
            'positive' => [4, '+4'],
        ];
    }

    public function testFromDexterityReturnsAnInitiative(): void
    {
        self::assertInstanceOf(
            Initiative::class,
            Initiative::fromDexterity(
                AbilityScore::fromInt(14)
            )
        );
    }

    public function testInitiativeUsesDexterityModifierRatherThanScore(): void
    {
        $initiative = Initiative::fromDexterity(
            AbilityScore::fromInt(14)
        );

        self::assertSame(
            2,
            $initiative->modifier()
        );

        self::assertNotSame(
            14,
            $initiative->modifier()
        );
    }

    public function testInitiativeIsImmutable(): void
    {
        $first = Initiative::fromModifier(2);
        $second = Initiative::fromModifier(5);

        self::assertSame(
            2,
            $first->modifier()
        );

        self::assertSame(
            5,
            $second->modifier()
        );

        self::assertNotSame(
            $first,
            $second
        );
    }
}

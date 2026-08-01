<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CharacterClassTest extends TestCase
{
    public function testCanBeCreatedFromAString(): void
    {
        $class = CharacterClass::fromString(
            'fighter'
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testNormalisesUppercaseInput(): void
    {
        $class = CharacterClass::fromString(
            'FIGHTER'
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        $class = CharacterClass::fromString(
            '  fighter  '
        );

        self::assertSame(
            'fighter',
            $class->value()
        );
    }

    public function testReturnsDisplayLabel(): void
    {
        $class = CharacterClass::fromString(
            'fighter'
        );

        self::assertSame(
            'Fighter',
            $class->label()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $class = CharacterClass::fromString(
            'fighter'
        );

        self::assertSame(
            'fighter',
            (string) $class
        );
    }

    public function testEqualClassesAreEqual(): void
    {
        $first = CharacterClass::fromString(
            'fighter'
        );

        $second = CharacterClass::fromString(
            'Fighter'
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentClassesAreNotEqual(): void
    {
        $fighter = CharacterClass::fromString(
            'fighter'
        );

        $wizard = CharacterClass::fromString(
            'wizard'
        );

        self::assertFalse(
            $fighter->equals($wizard)
        );
    }

    public function testRejectsEmptyClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString('');
    }

    public function testRejectsWhitespaceOnlyClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString('   ');
    }

    public function testRejectsUnsupportedClass(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterClass::fromString(
            'sandwich knight'
        );
    }

    #[DataProvider('hitDieProvider')]
    public function testReturnsTheCorrectHitDie(
        string $className,
        int $expectedHitDie
    ): void {
        self::assertSame(
            $expectedHitDie,
            CharacterClass::fromString(
                $className
            )->hitDie()
        );
    }

    /**
     * @return array<string,array{string,int}>
     */
    public static function hitDieProvider(): array
    {
        return [
            'artificer' => ['artificer', 8],
            'barbarian' => ['barbarian', 12],
            'bard' => ['bard', 8],
            'cleric' => ['cleric', 8],
            'druid' => ['druid', 8],
            'fighter' => ['fighter', 10],
            'monk' => ['monk', 8],
            'paladin' => ['paladin', 10],
            'ranger' => ['ranger', 10],
            'rogue' => ['rogue', 8],
            'sorcerer' => ['sorcerer', 6],
            'warlock' => ['warlock', 8],
            'wizard' => ['wizard', 6],
        ];
    }

    #[DataProvider('startingHitPointsProvider')]
    public function testCalculatesStartingHitPoints(
        string $className,
        int $constitutionScore,
        int $expectedHitPoints
    ): void {
        $class = CharacterClass::fromString(
            $className
        );

        $constitution = AbilityScore::fromInt(
            $constitutionScore
        );

        self::assertSame(
            $expectedHitPoints,
            $class->startingHitPoints(
                $constitution
            )
        );
    }

    /**
     * @return array<string,array{string,int,int}>
     */
    public static function startingHitPointsProvider(): array
    {
        return [
            'barbarian with +3 constitution' => [
                'barbarian',
                16,
                15,
            ],
            'fighter with +2 constitution' => [
                'fighter',
                14,
                12,
            ],
            'cleric with +1 constitution' => [
                'cleric',
                12,
                9,
            ],
            'wizard with no modifier' => [
                'wizard',
                10,
                6,
            ],
            'sorcerer with -1 constitution' => [
                'sorcerer',
                8,
                5,
            ],
            'wizard with very low constitution' => [
                'wizard',
                1,
                1,
            ],
        ];
    }

    public function testReturnsEverySupportedClass(): void
    {
        $classes = CharacterClass::all();

        self::assertCount(
            13,
            $classes
        );

        self::assertContainsOnlyInstancesOf(
            CharacterClass::class,
            $classes
        );
    }

    public function testAllReturnsEveryExpectedClassIdentifier(): void
    {
        $values = array_map(
            static fn (
                CharacterClass $class
            ): string => $class->value(),
            CharacterClass::all()
        );

        self::assertSame(
            [
                'artificer',
                'barbarian',
                'bard',
                'cleric',
                'druid',
                'fighter',
                'monk',
                'paladin',
                'ranger',
                'rogue',
                'sorcerer',
                'warlock',
                'wizard',
            ],
            $values
        );
    }
}

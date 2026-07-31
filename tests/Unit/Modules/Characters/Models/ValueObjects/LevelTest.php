<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LevelTest extends TestCase
{
    public function testCanBeCreatedFromAnInteger(): void
    {
        $level = Level::fromInt(10);

        self::assertSame(
            10,
            $level->value()
        );
    }

    public function testCanCreateTheStartingLevel(): void
    {
        $level = Level::one();

        self::assertSame(
            1,
            $level->value()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $level = Level::fromInt(10);

        self::assertSame(
            '10',
            (string) $level
        );
    }

    public function testEqualLevelsAreEqual(): void
    {
        $first = Level::fromInt(10);
        $second = Level::fromInt(10);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentLevelsAreNotEqual(): void
    {
        $first = Level::fromInt(10);
        $second = Level::fromInt(11);

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testCanProgressToTheNextLevel(): void
    {
        $level = Level::fromInt(4);
        $next = $level->next();

        self::assertSame(
            5,
            $next->value()
        );
    }

    public function testProgressingDoesNotModifyTheOriginalLevel(): void
    {
        $level = Level::fromInt(4);

        $level->next();

        self::assertSame(
            4,
            $level->value()
        );
    }

    public function testCanReturnToThePreviousLevel(): void
    {
        $level = Level::fromInt(5);
        $previous = $level->previous();

        self::assertSame(
            4,
            $previous->value()
        );
    }

    public function testReturningDoesNotModifyTheOriginalLevel(): void
    {
        $level = Level::fromInt(5);

        $level->previous();

        self::assertSame(
            5,
            $level->value()
        );
    }

    public function testStartingLevelIsMinimum(): void
    {
        self::assertTrue(
            Level::one()->isMinimum()
        );
    }

    public function testHigherLevelIsNotMinimum(): void
    {
        self::assertFalse(
            Level::fromInt(2)->isMinimum()
        );
    }

    public function testLevelTwentyIsMaximum(): void
    {
        self::assertTrue(
            Level::fromInt(20)->isMaximum()
        );
    }

    public function testLowerLevelIsNotMaximum(): void
    {
        self::assertFalse(
            Level::fromInt(19)->isMaximum()
        );
    }

    public function testRejectsLevelsBelowOne(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Level::fromInt(0);
    }

    public function testRejectsNegativeLevels(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Level::fromInt(-1);
    }

    public function testRejectsLevelsAboveTwenty(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Level::fromInt(21);
    }

    public function testCannotProgressBeyondLevelTwenty(): void
    {
        $level = Level::fromInt(20);

        $this->expectException(
            LogicException::class
        );

        $level->next();
    }

    public function testCannotReturnBelowLevelOne(): void
    {
        $level = Level::one();

        $this->expectException(
            LogicException::class
        );

        $level->previous();
    }

    #[DataProvider('proficiencyBonusProvider')]
    public function testReturnsTheCorrectProficiencyBonus(
        int $levelValue,
        int $expectedBonus
    ): void {
        $level = Level::fromInt($levelValue);

        self::assertSame(
            $expectedBonus,
            $level->proficiencyBonus()
        );
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function proficiencyBonusProvider(): array
    {
        return [
            'level 1' => [1, 2],
            'level 4' => [4, 2],
            'level 5' => [5, 3],
            'level 8' => [8, 3],
            'level 9' => [9, 4],
            'level 12' => [12, 4],
            'level 13' => [13, 5],
            'level 16' => [16, 5],
            'level 17' => [17, 6],
            'level 20' => [20, 6],
        ];
    }
}

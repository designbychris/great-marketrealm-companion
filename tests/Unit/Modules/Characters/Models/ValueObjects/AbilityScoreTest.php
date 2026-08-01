<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AbilityScoreTest extends TestCase
{
    public function testCanBeCreatedFromAnInteger(): void
    {
        $score = AbilityScore::fromInt(16);

        self::assertSame(
            16,
            $score->value()
        );
    }

    public function testCanCreateTheAverageAbilityScore(): void
    {
        $score = AbilityScore::average();

        self::assertSame(
            10,
            $score->value()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $score = AbilityScore::fromInt(16);

        self::assertSame(
            '16',
            (string) $score
        );
    }

    public function testEqualScoresAreEqual(): void
    {
        $first = AbilityScore::fromInt(16);
        $second = AbilityScore::fromInt(16);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentScoresAreNotEqual(): void
    {
        $first = AbilityScore::fromInt(16);
        $second = AbilityScore::fromInt(15);

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testCanIncreaseByOneByDefault(): void
    {
        $score = AbilityScore::fromInt(15);

        $increased = $score->increase();

        self::assertSame(
            16,
            $increased->value()
        );
    }

    public function testCanIncreaseByASuppliedAmount(): void
    {
        $score = AbilityScore::fromInt(15);

        $increased = $score->increase(3);

        self::assertSame(
            18,
            $increased->value()
        );
    }

    public function testIncreasingDoesNotModifyTheOriginalScore(): void
    {
        $score = AbilityScore::fromInt(15);

        $score->increase(2);

        self::assertSame(
            15,
            $score->value()
        );
    }

    public function testCanDecreaseByOneByDefault(): void
    {
        $score = AbilityScore::fromInt(15);

        $decreased = $score->decrease();

        self::assertSame(
            14,
            $decreased->value()
        );
    }

    public function testCanDecreaseByASuppliedAmount(): void
    {
        $score = AbilityScore::fromInt(15);

        $decreased = $score->decrease(3);

        self::assertSame(
            12,
            $decreased->value()
        );
    }

    public function testDecreasingDoesNotModifyTheOriginalScore(): void
    {
        $score = AbilityScore::fromInt(15);

        $score->decrease(2);

        self::assertSame(
            15,
            $score->value()
        );
    }

    public function testMinimumScoreIsRecognised(): void
    {
        self::assertTrue(
            AbilityScore::fromInt(1)->isMinimum()
        );
    }

    public function testHigherScoreIsNotMinimum(): void
    {
        self::assertFalse(
            AbilityScore::fromInt(2)->isMinimum()
        );
    }

    public function testMaximumScoreIsRecognised(): void
    {
        self::assertTrue(
            AbilityScore::fromInt(30)->isMaximum()
        );
    }

    public function testLowerScoreIsNotMaximum(): void
    {
        self::assertFalse(
            AbilityScore::fromInt(29)->isMaximum()
        );
    }

    public function testRejectsScoresBelowOne(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        AbilityScore::fromInt(0);
    }

    public function testRejectsNegativeScores(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        AbilityScore::fromInt(-1);
    }

    public function testRejectsScoresAboveThirty(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        AbilityScore::fromInt(31);
    }

    public function testRejectsNegativeIncreaseAmounts(): void
    {
        $score = AbilityScore::fromInt(15);

        $this->expectException(
            InvalidArgumentException::class
        );

        $score->increase(-1);
    }

    public function testRejectsNegativeDecreaseAmounts(): void
    {
        $score = AbilityScore::fromInt(15);

        $this->expectException(
            InvalidArgumentException::class
        );

        $score->decrease(-1);
    }

    public function testCannotIncreaseBeyondThirty(): void
    {
        $score = AbilityScore::fromInt(30);

        $this->expectException(
            LogicException::class
        );

        $score->increase();
    }

    public function testCannotDecreaseBelowOne(): void
    {
        $score = AbilityScore::fromInt(1);

        $this->expectException(
            LogicException::class
        );

        $score->decrease();
    }

    public function testIncreasingByZeroReturnsAnEqualScore(): void
    {
        $score = AbilityScore::fromInt(15);

        $updated = $score->increase(0);

        self::assertTrue(
            $updated->equals($score)
        );
    }

    public function testDecreasingByZeroReturnsAnEqualScore(): void
    {
        $score = AbilityScore::fromInt(15);

        $updated = $score->decrease(0);

        self::assertTrue(
            $updated->equals($score)
        );
    }

    #[DataProvider('modifierProvider')]
    public function testReturnsTheCorrectModifier(
        int $scoreValue,
        int $expectedModifier
    ): void {
        self::assertSame(
            $expectedModifier,
            AbilityScore::fromInt(
                $scoreValue
            )->modifier()
        );
    }

    /**
     * @return array<string, array{int,int}>
     */
    public static function modifierProvider(): array
    {
        return [
            'score 1' => [1, -5],
            'score 2' => [2, -4],
            'score 3' => [3, -4],
            'score 4' => [4, -3],
            'score 5' => [5, -3],
            'score 6' => [6, -2],
            'score 7' => [7, -2],
            'score 8' => [8, -1],
            'score 9' => [9, -1],
            'score 10' => [10, 0],
            'score 11' => [11, 0],
            'score 12' => [12, 1],
            'score 13' => [13, 1],
            'score 14' => [14, 2],
            'score 15' => [15, 2],
            'score 16' => [16, 3],
            'score 17' => [17, 3],
            'score 18' => [18, 4],
            'score 19' => [19, 4],
            'score 20' => [20, 5],
            'score 21' => [21, 5],
            'score 22' => [22, 6],
            'score 23' => [23, 6],
            'score 24' => [24, 7],
            'score 25' => [25, 7],
            'score 26' => [26, 8],
            'score 27' => [27, 8],
            'score 28' => [28, 9],
            'score 29' => [29, 9],
            'score 30' => [30, 10],
        ];
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Speed;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SpeedTest extends TestCase
{
    #[DataProvider('validSpeedProvider')]
    public function testCreatesASpeedFromFeet(
        int $feet
    ): void {
        $speed = Speed::fromFeet(
            $feet
        );

        self::assertSame(
            $feet,
            $speed->feet()
        );

        self::assertSame(
            $feet,
            $speed->value()
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function validSpeedProvider(): array
    {
        return [
            'stationary' => [0],
            'very slow' => [5],
            'slow' => [15],
            'standard' => [30],
            'fast' => [40],
            'very fast' => [60],
            'maximum' => [120],
        ];
    }

    public function testCreatesTheStandardWalkingSpeed(): void
    {
        self::assertTrue(
            Speed::standard()->equals(
                Speed::fromFeet(30)
            )
        );
    }

    public function testCreatesAStationarySpeed(): void
    {
        self::assertTrue(
            Speed::stationary()->equals(
                Speed::fromFeet(0)
            )
        );
    }

    #[DataProvider('outOfRangeSpeedProvider')]
    public function testRejectsAnOutOfRangeSpeed(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed must be between 0 and 120 feet; received %d.',
                $feet
            )
        );

        Speed::fromFeet($feet);
    }

    /**
     * @return array<string,array{int}>
     */
    public static function outOfRangeSpeedProvider(): array
    {
        return [
            'below minimum' => [-5],
            'far below minimum' => [-100],
            'above maximum' => [125],
            'far above maximum' => [1000],
        ];
    }

    #[DataProvider('invalidIncrementProvider')]
    public function testRejectsASpeedOutsideFiveFootIncrements(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed must use increments of 5 feet; received %d.',
                $feet
            )
        );

        Speed::fromFeet($feet);
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidIncrementProvider(): array
    {
        return [
            'one foot' => [1],
            'twelve feet' => [12],
            'twenty eight feet' => [28],
            'thirty one feet' => [31],
            'one hundred and nineteen feet' => [119],
        ];
    }

    public function testIncreasesSpeed(): void
    {
        $increased = Speed::fromFeet(30)
            ->increase(10);

        self::assertSame(
            40,
            $increased->feet()
        );
    }

    public function testIncreasingSpeedReturnsANewValueObject(): void
    {
        $original = Speed::fromFeet(30);

        $increased = $original->increase(10);

        self::assertNotSame(
            $original,
            $increased
        );

        self::assertSame(
            30,
            $original->feet()
        );

        self::assertSame(
            40,
            $increased->feet()
        );
    }

    public function testRejectsAnIncreaseBeyondTheMaximum(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Speed must be between 0 and 120 feet; received 125.'
        );

        Speed::fromFeet(120)
            ->increase(5);
    }

    public function testReducesSpeed(): void
    {
        $reduced = Speed::fromFeet(30)
            ->reduce(10);

        self::assertSame(
            20,
            $reduced->feet()
        );
    }

    public function testReducingSpeedReturnsANewValueObject(): void
    {
        $original = Speed::fromFeet(30);

        $reduced = $original->reduce(10);

        self::assertNotSame(
            $original,
            $reduced
        );

        self::assertSame(
            30,
            $original->feet()
        );

        self::assertSame(
            20,
            $reduced->feet()
        );
    }

    public function testReductionCannotTakeSpeedBelowZero(): void
    {
        self::assertSame(
            0,
            Speed::fromFeet(20)
                ->reduce(30)
                ->feet()
        );
    }

    #[DataProvider('negativeAdjustmentProvider')]
    public function testRejectsANegativeIncrease(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed adjustments cannot be negative; received %d.',
                $feet
            )
        );

        Speed::standard()->increase(
            $feet
        );
    }

    #[DataProvider('negativeAdjustmentProvider')]
    public function testRejectsANegativeReduction(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed adjustments cannot be negative; received %d.',
                $feet
            )
        );

        Speed::standard()->reduce(
            $feet
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function negativeAdjustmentProvider(): array
    {
        return [
            'minus five' => [-5],
            'minus ten' => [-10],
            'minus one hundred' => [-100],
        ];
    }

    #[DataProvider('invalidAdjustmentIncrementProvider')]
    public function testRejectsAnIncreaseOutsideFiveFootIncrements(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed adjustments must use increments of 5 feet; received %d.',
                $feet
            )
        );

        Speed::standard()->increase(
            $feet
        );
    }

    #[DataProvider('invalidAdjustmentIncrementProvider')]
    public function testRejectsAReductionOutsideFiveFootIncrements(
        int $feet
    ): void {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            sprintf(
                'Speed adjustments must use increments of 5 feet; received %d.',
                $feet
            )
        );

        Speed::standard()->reduce(
            $feet
        );
    }

    /**
     * @return array<string,array{int}>
     */
    public static function invalidAdjustmentIncrementProvider(): array
    {
        return [
            'one foot' => [1],
            'four feet' => [4],
            'six feet' => [6],
            'twelve feet' => [12],
        ];
    }

    public function testMovingSpeedCanMove(): void
    {
        self::assertTrue(
            Speed::fromFeet(30)->canMove()
        );
    }

    public function testStationarySpeedCannotMove(): void
    {
        self::assertFalse(
            Speed::stationary()->canMove()
        );
    }

    public function testEqualSpeedsAreEqual(): void
    {
        self::assertTrue(
            Speed::fromFeet(30)->equals(
                Speed::fromFeet(30)
            )
        );
    }

    public function testDifferentSpeedsAreNotEqual(): void
    {
        self::assertFalse(
            Speed::fromFeet(30)->equals(
                Speed::fromFeet(25)
            )
        );
    }

    public function testReturnsTheMinimumSupportedSpeed(): void
    {
        self::assertSame(
            0,
            Speed::minimum()
        );
    }

    public function testReturnsTheMaximumSupportedSpeed(): void
    {
        self::assertSame(
            120,
            Speed::maximum()
        );
    }

    public function testReturnsTheRequiredIncrement(): void
    {
        self::assertSame(
            5,
            Speed::increment()
        );
    }

    #[DataProvider('formattedSpeedProvider')]
    public function testFormatsSpeedForDisplay(
        int $feet,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            Speed::fromFeet($feet)
                ->formatted()
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function formattedSpeedProvider(): array
    {
        return [
            'stationary' => [
                0,
                '0 ft',
            ],
            'slow' => [
                15,
                '15 ft',
            ],
            'standard' => [
                30,
                '30 ft',
            ],
            'fast' => [
                60,
                '60 ft',
            ],
        ];
    }

    #[DataProvider('stringSpeedProvider')]
    public function testConvertsToAString(
        int $feet,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            (string) Speed::fromFeet(
                $feet
            )
        );
    }

    /**
     * @return array<string,array{int,string}>
     */
    public static function stringSpeedProvider(): array
    {
        return [
            'stationary' => [
                0,
                '0',
            ],
            'standard' => [
                30,
                '30',
            ],
            'maximum' => [
                120,
                '120',
            ],
        ];
    }

    public function testSpeedIsImmutable(): void
    {
        $original = Speed::fromFeet(30);

        $faster = $original->increase(10);

        $slower = $original->reduce(10);

        self::assertSame(
            30,
            $original->feet()
        );

        self::assertSame(
            40,
            $faster->feet()
        );

        self::assertSame(
            20,
            $slower->feet()
        );

        self::assertNotSame(
            $original,
            $faster
        );

        self::assertNotSame(
            $original,
            $slower
        );
    }
}

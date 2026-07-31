<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ExperienceTest extends TestCase
{
    public function testCanCreateZeroExperience(): void
    {
        $experience = Experience::zero();

        self::assertSame(
            0,
            $experience->value()
        );
    }

    public function testCanCreateExperienceFromInteger(): void
    {
        $experience = Experience::fromInt(12345);

        self::assertSame(
            12345,
            $experience->value()
        );
    }

    public function testRejectsNegativeExperience(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Experience::fromInt(-1);
    }

    public function testCanGainExperience(): void
    {
        $experience = Experience::fromInt(500);

        $updated = $experience->gain(250);

        self::assertSame(
            750,
            $updated->value()
        );
    }

    public function testGainingExperienceReturnsANewInstance(): void
    {
        $experience = Experience::fromInt(100);

        $updated = $experience->gain(50);

        self::assertNotSame(
            $experience,
            $updated
        );
    }

    public function testOriginalExperienceIsNotModified(): void
    {
        $experience = Experience::fromInt(100);

        $experience->gain(250);

        self::assertSame(
            100,
            $experience->value()
        );
    }

    public function testRejectsNegativeExperienceGain(): void
    {
        $experience = Experience::zero();

        $this->expectException(
            InvalidArgumentException::class
        );

        $experience->gain(-10);
    }

    public function testEqualExperienceValuesAreEqual(): void
    {
        $first = Experience::fromInt(1000);
        $second = Experience::fromInt(1000);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentExperienceValuesAreNotEqual(): void
    {
        $first = Experience::fromInt(1000);
        $second = Experience::fromInt(1001);

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $experience = Experience::fromInt(1234);

        self::assertSame(
            '1234',
            (string) $experience
        );
    }

    public function testReturnsCurrentLevel(): void
    {
        $experience = Experience::fromInt(6500);

        self::assertTrue(
            $experience
                ->currentLevel()
                ->equals(Level::fromInt(5))
        );
    }

    public function testCanLevelUpWhenEnoughExperienceHasBeenEarned(): void
    {
        $experience = Experience::fromInt(6500);

        self::assertTrue(
            $experience->canLevelUp(
                Level::fromInt(4)
            )
        );
    }

    public function testCannotLevelUpWhenInsufficientExperienceHasBeenEarned(): void
    {
        $experience = Experience::fromInt(6499);

        self::assertFalse(
            $experience->canLevelUp(
                Level::fromInt(4)
            )
        );
    }

    public function testCannotLevelUpBeyondMaximumLevel(): void
    {
        $experience = Experience::fromInt(999999);

        self::assertFalse(
            $experience->canLevelUp(
                Level::fromInt(20)
            )
        );
    }
}

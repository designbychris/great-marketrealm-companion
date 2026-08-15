<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HitPointsTest extends TestCase
{
    public function testCanCreateFullHitPoints(): void
    {
        $hitPoints = HitPoints::full(12);

        self::assertSame(
            12,
            $hitPoints->current()
        );

        self::assertSame(
            12,
            $hitPoints->maximum()
        );

        self::assertSame(
            0,
            $hitPoints->temporary()
        );
    }

    public function testCanBeCreatedFromExistingValues(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 7,
            maximum: 12,
            temporary: 3
        );

        self::assertSame(
            7,
            $hitPoints->current()
        );

        self::assertSame(
            12,
            $hitPoints->maximum()
        );

        self::assertSame(
            3,
            $hitPoints->temporary()
        );
    }

    public function testRejectsMaximumHitPointsBelowOne(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        HitPoints::full(0);
    }

    public function testRejectsNegativeCurrentHitPoints(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        HitPoints::fromValues(
            current: -1,
            maximum: 12
        );
    }

    public function testRejectsCurrentHitPointsAboveMaximum(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        HitPoints::fromValues(
            current: 13,
            maximum: 12
        );
    }

    public function testRejectsNegativeTemporaryHitPoints(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        HitPoints::fromValues(
            current: 12,
            maximum: 12,
            temporary: -1
        );
    }

    public function testLiveStateCanChangeCurrentAndTemporaryWithoutChangingMaximum(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 10,
            maximum: 12,
            temporary: 5
        );

        $updated = $hitPoints->withLiveState(
            current: 7,
            temporary: 2
        );

        self::assertSame(7, $updated->current());
        self::assertSame(12, $updated->maximum());
        self::assertSame(2, $updated->temporary());
        self::assertSame(10, $hitPoints->current());
        self::assertSame(5, $hitPoints->temporary());
    }

    public function testCanTakeDamage(): void
    {
        $hitPoints = HitPoints::full(12);

        $damaged = $hitPoints->takeDamage(5);

        self::assertSame(
            7,
            $damaged->current()
        );
    }

    public function testDamageCannotReduceCurrentHitPointsBelowZero(): void
    {
        $hitPoints = HitPoints::full(12);

        $damaged = $hitPoints->takeDamage(20);

        self::assertSame(
            0,
            $damaged->current()
        );
    }

    public function testTemporaryHitPointsAbsorbDamageFirst(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 10,
            maximum: 12,
            temporary: 5
        );

        $damaged = $hitPoints->takeDamage(3);

        self::assertSame(
            10,
            $damaged->current()
        );

        self::assertSame(
            2,
            $damaged->temporary()
        );
    }

    public function testRemainingDamageIsAppliedAfterTemporaryHitPoints(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 10,
            maximum: 12,
            temporary: 3
        );

        $damaged = $hitPoints->takeDamage(5);

        self::assertSame(
            8,
            $damaged->current()
        );

        self::assertSame(
            0,
            $damaged->temporary()
        );
    }

    public function testTakingDamageDoesNotModifyTheOriginalInstance(): void
    {
        $hitPoints = HitPoints::full(12);

        $hitPoints->takeDamage(5);

        self::assertSame(
            12,
            $hitPoints->current()
        );
    }

    public function testRejectsNegativeDamage(): void
    {
        $hitPoints = HitPoints::full(12);

        $this->expectException(
            InvalidArgumentException::class
        );

        $hitPoints->takeDamage(-1);
    }

    public function testCanHealDamage(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 6,
            maximum: 12
        );

        $healed = $hitPoints->heal(4);

        self::assertSame(
            10,
            $healed->current()
        );
    }

    public function testHealingCannotExceedMaximumHitPoints(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 10,
            maximum: 12
        );

        $healed = $hitPoints->heal(10);

        self::assertSame(
            12,
            $healed->current()
        );
    }

    public function testHealingDoesNotRestoreTemporaryHitPoints(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 6,
            maximum: 12,
            temporary: 3
        );

        $healed = $hitPoints->heal(4);

        self::assertSame(
            3,
            $healed->temporary()
        );
    }

    public function testHealingDoesNotModifyTheOriginalInstance(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 6,
            maximum: 12
        );

        $hitPoints->heal(4);

        self::assertSame(
            6,
            $hitPoints->current()
        );
    }

    public function testRejectsNegativeHealing(): void
    {
        $hitPoints = HitPoints::full(12);

        $this->expectException(
            InvalidArgumentException::class
        );

        $hitPoints->heal(-1);
    }

    public function testCanGrantTemporaryHitPoints(): void
    {
        $hitPoints = HitPoints::full(12);

        $updated = $hitPoints->grantTemporary(4);

        self::assertSame(
            4,
            $updated->temporary()
        );
    }

    public function testHigherTemporaryHitPointValueReplacesExistingValue(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 12,
            maximum: 12,
            temporary: 3
        );

        $updated = $hitPoints->grantTemporary(5);

        self::assertSame(
            5,
            $updated->temporary()
        );
    }

    public function testLowerTemporaryHitPointValueDoesNotReplaceExistingValue(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 12,
            maximum: 12,
            temporary: 5
        );

        $updated = $hitPoints->grantTemporary(3);

        self::assertSame(
            5,
            $updated->temporary()
        );
    }

    public function testGrantingTemporaryHitPointsDoesNotModifyOriginalInstance(): void
    {
        $hitPoints = HitPoints::full(12);

        $hitPoints->grantTemporary(4);

        self::assertSame(
            0,
            $hitPoints->temporary()
        );
    }

    public function testRejectsNegativeTemporaryHitPointGrant(): void
    {
        $hitPoints = HitPoints::full(12);

        $this->expectException(
            InvalidArgumentException::class
        );

        $hitPoints->grantTemporary(-1);
    }

    public function testCharacterIsConsciousAboveZeroHitPoints(): void
    {
        self::assertTrue(
            HitPoints::fromValues(
                current: 1,
                maximum: 12
            )->isConscious()
        );
    }

    public function testCharacterIsNotConsciousAtZeroHitPoints(): void
    {
        self::assertFalse(
            HitPoints::fromValues(
                current: 0,
                maximum: 12
            )->isConscious()
        );
    }

    public function testFullHitPointsAreAtMaximum(): void
    {
        self::assertTrue(
            HitPoints::full(12)->isAtMaximum()
        );
    }

    public function testDamagedHitPointsAreNotAtMaximum(): void
    {
        self::assertFalse(
            HitPoints::fromValues(
                current: 11,
                maximum: 12
            )->isAtMaximum()
        );
    }

    public function testEqualHitPointStatesAreEqual(): void
    {
        $first = HitPoints::fromValues(
            current: 8,
            maximum: 12,
            temporary: 3
        );

        $second = HitPoints::fromValues(
            current: 8,
            maximum: 12,
            temporary: 3
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentHitPointStatesAreNotEqual(): void
    {
        $first = HitPoints::fromValues(
            current: 8,
            maximum: 12,
            temporary: 3
        );

        $second = HitPoints::fromValues(
            current: 7,
            maximum: 12,
            temporary: 3
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testCanBeConvertedToAStringWithoutTemporaryHitPoints(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 8,
            maximum: 12
        );

        self::assertSame(
            '8/12',
            (string) $hitPoints
        );
    }

    public function testCanBeConvertedToAStringWithTemporaryHitPoints(): void
    {
        $hitPoints = HitPoints::fromValues(
            current: 8,
            maximum: 12,
            temporary: 3
        );

        self::assertSame(
            '8/12 (+3 temporary)',
            (string) $hitPoints
        );
    }
}

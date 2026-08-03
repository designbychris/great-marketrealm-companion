<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Condition;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Conditions;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConditionsTest extends TestCase
{
    public function testCreatesAnEmptyCollection(): void
    {
        $conditions = Conditions::none();

        self::assertSame(
            [],
            $conditions->all()
        );

        self::assertSame(
            [],
            $conditions->values()
        );

        self::assertTrue(
            $conditions->isEmpty()
        );

        self::assertSame(
            0,
            $conditions->count()
        );
    }

    public function testCreatesCollectionFromConditionValues(): void
    {
        $poisoned = Condition::fromString(
            'poisoned'
        );

        $prone = Condition::fromString(
            'prone'
        );

        $conditions = Conditions::fromConditions(
            $poisoned,
            $prone
        );

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $conditions->values()
        );

        self::assertCount(
            2,
            $conditions->all()
        );

        self::assertContainsOnlyInstancesOf(
            Condition::class,
            $conditions->all()
        );
    }

    public function testCreatesCollectionFromStringIdentifiers(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $conditions->values()
        );
    }

    public function testNormalisesStringIdentifiers(): void
    {
        $conditions = Conditions::fromStrings([
            ' POISONED ',
            'PrOnE',
        ]);

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $conditions->values()
        );
    }

    public function testRemovesDuplicateConditions(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
            'POISONED',
            ' poisoned ',
        ]);

        self::assertSame(
            ['poisoned'],
            $conditions->values()
        );

        self::assertSame(
            1,
            $conditions->count()
        );
    }

    public function testReturnsConditionsInCanonicalOrder(): void
    {
        $conditions = Conditions::fromStrings([
            'unconscious',
            'blinded',
            'prone',
            'charmed',
            'poisoned',
        ]);

        self::assertSame(
            [
                'blinded',
                'charmed',
                'poisoned',
                'prone',
                'unconscious',
            ],
            $conditions->values()
        );
    }

    public function testDeterminesWhetherConditionExistsUsingString(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        self::assertTrue(
            $conditions->has(
                'poisoned'
            )
        );

        self::assertFalse(
            $conditions->has(
                'stunned'
            )
        );
    }

    public function testDeterminesWhetherConditionExistsUsingValueObject(): void
    {
        $conditions = Conditions::fromStrings([
            'restrained',
        ]);

        self::assertTrue(
            $conditions->has(
                Condition::fromString(
                    'restrained'
                )
            )
        );

        self::assertFalse(
            $conditions->has(
                Condition::fromString(
                    'frightened'
                )
            )
        );
    }

    public function testConditionLookupNormalisesInput(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
        ]);

        self::assertTrue(
            $conditions->has(
                ' POISONED '
            )
        );
    }

    public function testAddsConditionImmutablyUsingString(): void
    {
        $original = Conditions::fromStrings([
            'poisoned',
        ]);

        $updated = $original->add(
            'prone'
        );

        self::assertSame(
            ['poisoned'],
            $original->values()
        );

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testAddsConditionImmutablyUsingValueObject(): void
    {
        $original = Conditions::none();

        $updated = $original->add(
            Condition::fromString(
                'stunned'
            )
        );

        self::assertTrue(
            $original->isEmpty()
        );

        self::assertSame(
            ['stunned'],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testAddingExistingConditionReturnsSameCollection(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
        ]);

        $updated = $conditions->add(
            'POISONED'
        );

        self::assertSame(
            $conditions,
            $updated
        );

        self::assertSame(
            ['poisoned'],
            $updated->values()
        );
    }

    public function testAddedConditionsRemainInCanonicalOrder(): void
    {
        $conditions = Conditions::fromStrings([
            'unconscious',
            'poisoned',
        ]);

        $updated = $conditions->add(
            'blinded'
        );

        self::assertSame(
            [
                'blinded',
                'poisoned',
                'unconscious',
            ],
            $updated->values()
        );
    }

    public function testRemovesConditionImmutablyUsingString(): void
    {
        $original = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        $updated = $original->remove(
            'poisoned'
        );

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $original->values()
        );

        self::assertSame(
            ['prone'],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testRemovesConditionImmutablyUsingValueObject(): void
    {
        $original = Conditions::fromStrings([
            'restrained',
            'stunned',
        ]);

        $updated = $original->remove(
            Condition::fromString(
                'restrained'
            )
        );

        self::assertSame(
            ['stunned'],
            $updated->values()
        );
    }

    public function testRemovingMissingConditionReturnsSameCollection(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
        ]);

        $updated = $conditions->remove(
            'prone'
        );

        self::assertSame(
            $conditions,
            $updated
        );
    }

    public function testRemovingLastConditionCreatesEmptyCollection(): void
    {
        $updated = Conditions::fromStrings([
            'poisoned',
        ])->remove(
            'poisoned'
        );

        self::assertTrue(
            $updated->isEmpty()
        );

        self::assertSame(
            0,
            $updated->count()
        );
    }

    public function testMergesConditionCollections(): void
    {
        $first = Conditions::fromStrings([
            'blinded',
            'poisoned',
        ]);

        $second = Conditions::fromStrings([
            'prone',
            'stunned',
        ]);

        $merged = $first->merge(
            $second
        );

        self::assertSame(
            [
                'blinded',
                'poisoned',
                'prone',
                'stunned',
            ],
            $merged->values()
        );
    }

    public function testMergeRemovesDuplicates(): void
    {
        $first = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        $second = Conditions::fromStrings([
            'poisoned',
            'stunned',
        ]);

        $merged = $first->merge(
            $second
        );

        self::assertSame(
            [
                'poisoned',
                'prone',
                'stunned',
            ],
            $merged->values()
        );
    }

    public function testMergeDoesNotMutateEitherSource(): void
    {
        $first = Conditions::fromStrings([
            'poisoned',
        ]);

        $second = Conditions::fromStrings([
            'prone',
        ]);

        $first->merge($second);

        self::assertSame(
            ['poisoned'],
            $first->values()
        );

        self::assertSame(
            ['prone'],
            $second->values()
        );
    }

    public function testMergingWithEmptyCollectionPreservesValues(): void
    {
        $conditions = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        $merged = $conditions->merge(
            Conditions::none()
        );

        self::assertTrue(
            $conditions->equals(
                $merged
            )
        );
    }

    public function testCountsActiveConditions(): void
    {
        self::assertSame(
            3,
            Conditions::fromStrings([
                'poisoned',
                'prone',
                'stunned',
            ])->count()
        );
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first = Conditions::fromStrings([
            'poisoned',
            'prone',
        ]);

        $second = Conditions::fromStrings([
            'prone',
            'poisoned',
        ]);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCollectionsAreNotEqual(): void
    {
        $first = Conditions::fromStrings([
            'poisoned',
        ]);

        $second = Conditions::fromStrings([
            'prone',
        ]);

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testEmptyCollectionsAreEqual(): void
    {
        self::assertTrue(
            Conditions::none()->equals(
                Conditions::none()
            )
        );
    }

    public function testRejectsNonStringIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Character condition identifiers must be strings.'
        );

        Conditions::fromStrings([
            123,
        ]);
    }

    public function testRejectsUnsupportedConditionIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character condition "hungry" is not supported.'
        );

        Conditions::fromStrings([
            'hungry',
        ]);
    }

    public function testHasRejectsUnsupportedConditionIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Conditions::none()->has(
            'hungry'
        );
    }

    public function testAddRejectsUnsupportedConditionIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Conditions::none()->add(
            'hungry'
        );
    }

    public function testRemoveRejectsUnsupportedConditionIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Conditions::none()->remove(
            'hungry'
        );
    }

    public function testCollectionIsImmutable(): void
    {
        $original = Conditions::fromStrings([
            'poisoned',
        ]);

        $withProne = $original->add(
            'prone'
        );

        $withoutPoisoned = $withProne->remove(
            'poisoned'
        );

        self::assertSame(
            ['poisoned'],
            $original->values()
        );

        self::assertSame(
            [
                'poisoned',
                'prone',
            ],
            $withProne->values()
        );

        self::assertSame(
            ['prone'],
            $withoutPoisoned->values()
        );
    }
}

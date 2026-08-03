<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Condition;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    #[DataProvider('supportedConditionProvider')]
    public function testCanBeCreatedFromSupportedCondition(
        string $condition,
        string $expectedValue
    ): void {
        self::assertSame(
            $expectedValue,
            Condition::fromString(
                $condition
            )->value()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function supportedConditionProvider(): array
    {
        return [
            'blinded' => [
                'blinded',
                'blinded',
            ],
            'charmed' => [
                'charmed',
                'charmed',
            ],
            'deafened' => [
                'deafened',
                'deafened',
            ],
            'frightened' => [
                'frightened',
                'frightened',
            ],
            'grappled' => [
                'grappled',
                'grappled',
            ],
            'incapacitated' => [
                'incapacitated',
                'incapacitated',
            ],
            'invisible' => [
                'invisible',
                'invisible',
            ],
            'paralyzed' => [
                'paralyzed',
                'paralyzed',
            ],
            'petrified' => [
                'petrified',
                'petrified',
            ],
            'poisoned' => [
                'poisoned',
                'poisoned',
            ],
            'prone' => [
                'prone',
                'prone',
            ],
            'restrained' => [
                'restrained',
                'restrained',
            ],
            'stunned' => [
                'stunned',
                'stunned',
            ],
            'unconscious' => [
                'unconscious',
                'unconscious',
            ],
        ];
    }

    public function testNormalisesUppercaseInput(): void
    {
        self::assertSame(
            'poisoned',
            Condition::fromString(
                'POISONED'
            )->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        self::assertSame(
            'restrained',
            Condition::fromString(
                '  restrained  '
            )->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        /*
         * No current condition identifier contains a hyphen,
         * but this confirms the shared canonical normalisation
         * behaviour for future multi-word conditions.
         */
        self::assertFalse(
            Condition::supports(
                'sandwich stunned'
            )
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        self::assertFalse(
            Condition::supports(
                'sandwich_stunned'
            )
        );
    }

    #[DataProvider('conditionLabelProvider')]
    public function testReturnsDisplayLabel(
        string $condition,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            Condition::fromString(
                $condition
            )->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function conditionLabelProvider(): array
    {
        return [
            'blinded' => [
                'blinded',
                'Blinded',
            ],
            'charmed' => [
                'charmed',
                'Charmed',
            ],
            'deafened' => [
                'deafened',
                'Deafened',
            ],
            'frightened' => [
                'frightened',
                'Frightened',
            ],
            'grappled' => [
                'grappled',
                'Grappled',
            ],
            'incapacitated' => [
                'incapacitated',
                'Incapacitated',
            ],
            'invisible' => [
                'invisible',
                'Invisible',
            ],
            'paralyzed' => [
                'paralyzed',
                'Paralyzed',
            ],
            'petrified' => [
                'petrified',
                'Petrified',
            ],
            'poisoned' => [
                'poisoned',
                'Poisoned',
            ],
            'prone' => [
                'prone',
                'Prone',
            ],
            'restrained' => [
                'restrained',
                'Restrained',
            ],
            'stunned' => [
                'stunned',
                'Stunned',
            ],
            'unconscious' => [
                'unconscious',
                'Unconscious',
            ],
        ];
    }

    #[DataProvider('supportedConditionIdentifierProvider')]
    public function testReportsSupportedConditions(
        string $condition
    ): void {
        self::assertTrue(
            Condition::supports(
                $condition
            )
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function supportedConditionIdentifierProvider(): array
    {
        return [
            'blinded' => ['blinded'],
            'charmed' => ['charmed'],
            'deafened' => ['deafened'],
            'frightened' => ['frightened'],
            'grappled' => ['grappled'],
            'incapacitated' => ['incapacitated'],
            'invisible' => ['invisible'],
            'paralyzed' => ['paralyzed'],
            'petrified' => ['petrified'],
            'poisoned' => ['poisoned'],
            'prone' => ['prone'],
            'restrained' => ['restrained'],
            'stunned' => ['stunned'],
            'unconscious' => ['unconscious'],
        ];
    }

    public function testSupportsNormalisedInput(): void
    {
        self::assertTrue(
            Condition::supports(
                ' POISONED '
            )
        );

        self::assertTrue(
            Condition::supports(
                'UnConScIoUs'
            )
        );
    }

    public function testDoesNotSupportUnknownCondition(): void
    {
        self::assertFalse(
            Condition::supports(
                'hungry'
            )
        );
    }

    public function testDoesNotSupportExhaustionAsBooleanCondition(): void
    {
        self::assertFalse(
            Condition::supports(
                'exhausted'
            )
        );
    }

    public function testRejectsEmptyCondition(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Character condition cannot be empty.'
        );

        Condition::fromString('');
    }

    public function testRejectsWhitespaceOnlyCondition(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Character condition cannot be empty.'
        );

        Condition::fromString('   ');
    }

    public function testRejectsUnsupportedCondition(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character condition "hungry" is not supported.'
        );

        Condition::fromString(
            'hungry'
        );
    }

    public function testEqualConditionsAreEqual(): void
    {
        $first = Condition::fromString(
            'poisoned'
        );

        $second = Condition::fromString(
            ' POISONED '
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentConditionsAreNotEqual(): void
    {
        self::assertFalse(
            Condition::fromString(
                'poisoned'
            )->equals(
                Condition::fromString(
                    'prone'
                )
            )
        );
    }

    public function testConvertsToCanonicalString(): void
    {
        self::assertSame(
            'frightened',
            (string) Condition::fromString(
                'Frightened'
            )
        );
    }

    public function testReturnsEverySupportedCondition(): void
    {
        $conditions = Condition::all();

        self::assertCount(
            14,
            $conditions
        );

        self::assertContainsOnlyInstancesOf(
            Condition::class,
            $conditions
        );
    }

    public function testAllReturnsEveryExpectedIdentifierInCanonicalOrder(): void
    {
        $values = array_map(
            static fn (
                Condition $condition
            ): string => $condition->value(),
            Condition::all()
        );

        self::assertSame(
            [
                'blinded',
                'charmed',
                'deafened',
                'frightened',
                'grappled',
                'incapacitated',
                'invisible',
                'paralyzed',
                'petrified',
                'poisoned',
                'prone',
                'restrained',
                'stunned',
                'unconscious',
            ],
            $values
        );
    }

    public function testAllConditionsHaveUniqueIdentifiers(): void
    {
        $values = array_map(
            static fn (
                Condition $condition
            ): string => $condition->value(),
            Condition::all()
        );

        self::assertSame(
            $values,
            array_values(
                array_unique($values)
            )
        );
    }

    public function testAllConditionsHaveNonEmptyLabels(): void
    {
        foreach (Condition::all() as $condition) {
            self::assertNotSame(
                '',
                $condition->label()
            );
        }
    }

    public function testConditionIsImmutable(): void
    {
        $poisoned = Condition::fromString(
            'poisoned'
        );

        $prone = Condition::fromString(
            'prone'
        );

        self::assertSame(
            'poisoned',
            $poisoned->value()
        );

        self::assertSame(
            'prone',
            $prone->value()
        );

        self::assertNotSame(
            $poisoned,
            $prone
        );
    }
}

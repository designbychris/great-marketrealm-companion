<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RaceTest extends TestCase
{
    public function testCanBeCreatedFromAString(): void
    {
        $race = Race::fromString(
            'fructan'
        );

        self::assertSame(
            'fructan',
            $race->value()
        );
    }

    public function testNormalisesUppercaseInput(): void
    {
        $race = Race::fromString(
            'FRUCTAN'
        );

        self::assertSame(
            'fructan',
            $race->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        $race = Race::fromString(
            '  fructan  '
        );

        self::assertSame(
            'fructan',
            $race->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        $race = Race::fromString(
            'Drink Folk'
        );

        self::assertSame(
            'drink-folk',
            $race->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        $race = Race::fromString(
            'drink_folk'
        );

        self::assertSame(
            'drink-folk',
            $race->value()
        );
    }

    public function testReturnsStandardRaceDisplayLabel(): void
    {
        self::assertSame(
            'Fructan',
            Race::fromString(
                'fructan'
            )->label()
        );
    }

    public function testReturnsHyphenatedRaceDisplayLabel(): void
    {
        self::assertSame(
            'Drinkfolk',
            Race::fromString(
                'drink-folk'
            )->label()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $race = Race::fromString(
            'vegfolk'
        );

        self::assertSame(
            'vegfolk',
            (string) $race
        );
    }

    public function testEqualRacesAreEqual(): void
    {
        $first = Race::fromString(
            'drink-folk'
        );

        $second = Race::fromString(
            'Drink Folk'
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentRacesAreNotEqual(): void
    {
        $fructan = Race::fromString(
            'fructan'
        );

        $vegfolk = Race::fromString(
            'vegfolk'
        );

        self::assertFalse(
            $fructan->equals($vegfolk)
        );
    }

    public function testRejectsEmptyRace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Race::fromString('');
    }

    public function testRejectsWhitespaceOnlyRace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Race::fromString('   ');
    }

    public function testRejectsUnsupportedRace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Race::fromString(
            'sandwich-person'
        );
    }

    public function testSupportsNormalisedRaceIdentifiers(): void
    {
        self::assertTrue(
            Race::supports(
                ' Drink Folk '
            )
        );

        self::assertTrue(
            Race::supports(
                'drink_folk'
            )
        );
    }

    public function testDoesNotSupportUnknownRaceIdentifiers(): void
    {
        self::assertFalse(
            Race::supports(
                'sandwich-person'
            )
        );
    }

    #[DataProvider('supportedRaceProvider')]
    public function testSupportsEveryRegisteredRace(
        string $race
    ): void {
        self::assertTrue(
            Race::supports($race)
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function supportedRaceProvider(): array
    {
        return [
            'boxfolk' => ['boxfolk'],
            'capsicumite' => ['capsicumite'],
            'dairyfolk' => ['dairyfolk'],
            'drink folk' => ['drink-folk'],
            'fluffling' => ['fluffling'],
            'fructan' => ['fructan'],
            'fungifolk' => ['fungifolk'],
            'herbfolk' => ['herbfolk'],
            'meatfolk' => ['meatfolk'],
            'meatkin' => ['meatkin'],
            'melonian' => ['melonian'],
            'rootkin' => ['rootkin'],
            'stalker' => ['stalker'],
            'sweetfolk' => ['sweetfolk'],
            'vegfolk' => ['vegfolk'],
        ];
    }

    #[DataProvider('unsupportedRaceProvider')]
    public function testRejectsUnsupportedRaceIdentifiers(
        string $race
    ): void {
        self::assertFalse(
            Race::supports($race)
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function unsupportedRaceProvider(): array
    {
        return [
            'empty string' => [''],
            'whitespace only' => ['   '],
            'unknown race' => ['unknown'],
            'unsupported food race' => [
                'sandwich-person',
            ],
            'partial race name' => ['fruit'],
        ];
    }

    #[DataProvider('labelProvider')]
    public function testReturnsTheCorrectLabel(
        string $race,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            Race::fromString($race)->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'boxfolk' => [
                'boxfolk',
                'Boxfolk',
            ],
            'capsicumite' => [
                'capsicumite',
                'Capsicumite',
            ],
            'dairyfolk' => [
                'dairyfolk',
                'Dairyfolk',
            ],
            'drink folk' => [
                'drink-folk',
                'Drinkfolk',
            ],
            'fluffling' => [
                'fluffling',
                'Fluffling',
            ],
            'fructan' => [
                'fructan',
                'Fructan',
            ],
            'fungifolk' => [
                'fungifolk',
                'Fungifolk',
            ],
            'herbfolk' => [
                'herbfolk',
                'Herbfolk',
            ],
            'meatfolk' => [
                'meatfolk',
                'Meatfolk',
            ],
            'meatkin' => [
                'meatkin',
                'Meatkin',
            ],
            'melonian' => [
                'melonian',
                'Melonian',
            ],
            'rootkin' => [
                'rootkin',
                'Rootkin',
            ],
            'stalker' => [
                'stalker',
                'Stalker',
            ],
            'sweetfolk' => [
                'sweetfolk',
                'Sweetfolk',
            ],
            'vegfolk' => [
                'vegfolk',
                'Vegfolk',
            ],
        ];
    }

    public function testReturnsEverySupportedRace(): void
    {
        $races = Race::all();

        self::assertCount(
            14,
            $races
        );

        self::assertContainsOnlyInstancesOf(
            Race::class,
            $races
        );
    }

    public function testAllReturnsEveryExpectedRaceIdentifier(): void
    {
        $values = array_map(
            static fn (
                Race $race
            ): string => $race->value(),
            Race::all()
        );

        self::assertSame(
            [
                'boxfolk',
                'capsicumite',
                'dairyfolk',
                'drink-folk',
                'fluffling',
                'fructan',
                'fungifolk',
                'herbfolk',
                'meatfolk',
                'melonian',
                'rootkin',
                'stalker',
                'sweetfolk',
                'vegfolk',
            ],
            $values
        );
    }

    public function testEveryRaceReturnedByAllIsSupported(): void
    {
        foreach (Race::all() as $race) {
            self::assertTrue(
                Race::supports(
                    $race->value()
                )
            );
        }
    }

    public function testAllReturnsFreshRaceInstances(): void
    {
        $first = Race::all();
        $second = Race::all();

        self::assertNotSame(
            $first[0],
            $second[0]
        );

        self::assertTrue(
            $first[0]->equals(
                $second[0]
            )
        );
    }
}

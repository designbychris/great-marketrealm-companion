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
        self::assertSame(
            'drink-folk',
            Race::fromString(
                'drink folk'
            )->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        self::assertSame(
            'drink-folk',
            Race::fromString(
                'drink_folk'
            )->value()
        );
    }

    public function testReturnsDisplayLabel(): void
    {
        self::assertSame(
            'Fructan',
            Race::fromString(
                'fructan'
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
            'fructan'
        );

        $second = Race::fromString(
            'FRUCTAN'
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
            'sandwich person'
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

    #[DataProvider('unsupportedRaceProvider')]
    public function testRejectsUnsupportedRaceIdentifiers(
        string $race
    ): void {
        self::assertFalse(
            Race::supports($race)
        );
    }

    #[DataProvider('labelProvider')]
    public function testReturnsTheCorrectLabel(
        string $value,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            Race::fromString($value)->label()
        );
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
                'drinkfolk',
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

    /**
     * @return array<string,array{string}>
     */
    public static function supportedRaceProvider(): array
    {
        return [
            'boxfolk' => ['boxfolk'],
            'capsicumite' => ['capsicumite'],
            'dairyfolk' => ['dairyfolk'],
            'drinkfolk' => ['drinkfolk'],
            'fluffling' => ['fluffling'],
            'fructan' => ['fructan'],
            'fungifolk' => ['fungifolk'],
            'herbfolk' => ['herbfolk'],
            'meatfolk' => ['meatfolk'],
            'melonian' => ['melonian'],
            'rootkin' => ['rootkin'],
            'stalker' => ['stalker'],
            'sweetfolk' => ['sweetfolk'],
            'vegfolk' => ['vegfolk'],
        ];
    }

    /**
     * @return array<string,array{string}>
     */
    public static function unsupportedRaceProvider(): array
    {
        return [
            'empty string' => [''],
            'unknown race' => ['unknown'],
            'unsupported food race' => ['sandwich-person'],
            'partial race name' => ['fruit'],
        ];
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'boxfolk' => ['boxfolk', 'Boxfolk'],
            'capsicumite' => ['capsicumite', 'Capsicumite'],
            'dairyfolk' => ['dairyfolk', 'Dairyfolk'],
            'drinkfolk' => ['drinkfolk', 'Drinkfolk'],
            'fluffling' => ['fluffling', 'Fluffling'],
            'fructan' => ['fructan', 'Fructan'],
            'fungifolk' => ['fungifolk', 'Fungifolk'],
            'herbfolk' => ['herbfolk', 'Herbfolk'],
            'meatfolk' => ['meatfolk', 'Meatfolk'],
            'melonian' => ['melonian', 'Melonian'],
            'rootkin' => ['rootkin', 'Rootkin'],
            'stalker' => ['stalker', 'Stalker'],
            'sweetfolk' => ['sweetfolk', 'Sweetfolk'],
            'vegfolk' => ['vegfolk', 'Vegfolk'],
        ];
    }
}

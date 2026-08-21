<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BackgroundTest extends TestCase
{
    #[DataProvider('supportedBackgroundProvider')]
    public function testCanBeCreatedFromSupportedBackground(
        string $input,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            Background::fromString(
                $input
            )->value()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function supportedBackgroundProvider(): array
    {
        return [
            'market runner' => [
                'market-runner',
                'market-runner',
            ],
            'shelf scholar' => [
                'shelf-scholar',
                'shelf-scholar',
            ],
            'waste warden' => [
                'waste-warden',
                'crateborn-noble',
                'backshelf-forager',
                'discount-bin-survivor',
                'cleaners-acolyte',
                'cart-ranger',
                'waste-warden',
            ],
            'guild artisan' => [
                'guild-artisan',
                'guild-artisan',
            ],
            'folk hero' => [
                'folk-hero',
                'folk-hero',
            ],
            'sage' => [
                'sage',
                'sage',
            ],
            'soldier' => [
                'soldier',
                'soldier',
            ],
            'criminal' => [
                'criminal',
                'criminal',
            ],
        ];
    }

    public function testNormalisesUppercaseInput(): void
    {
        self::assertSame(
            'market-runner',
            Background::fromString(
                'MARKET-RUNNER'
            )->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        self::assertSame(
            'shelf-scholar',
            Background::fromString(
                '  shelf-scholar  '
            )->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        self::assertSame(
            'waste-warden',
            Background::fromString(
                'Waste Warden'
            )->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        self::assertSame(
            'guild-artisan',
            Background::fromString(
                'guild_artisan'
            )->value()
        );
    }

    #[DataProvider('labelProvider')]
    public function testReturnsCorrectDisplayLabel(
        string $background,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            Background::fromString(
                $background
            )->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'market runner' => [
                'market-runner',
                'Market Runner',
            ],
            'shelf scholar' => [
                'shelf-scholar',
                'Shelf Scholar',
            ],
            'waste warden' => [
                'waste-warden',
                'Waste-Warden',
            ],
            'guild artisan' => [
                'guild-artisan',
                'Guild Artisan',
            ],
            'folk hero' => [
                'folk-hero',
                'Folk Hero',
            ],
            'sage' => [
                'sage',
                'Sage',
            ],
            'soldier' => [
                'soldier',
                'Soldier',
            ],
            'criminal' => [
                'criminal',
                'Criminal',
            ],
        ];
    }

    #[DataProvider('skillProficiencyProvider')]
    public function testReturnsCorrectSkillProficiencies(
        string $background,
        array $expectedSkills
    ): void {
        $proficiencies = Background::fromString(
            $background
        )->skillProficiencies();

        self::assertInstanceOf(
            SkillProficiencies::class,
            $proficiencies
        );

        self::assertSame(
            $expectedSkills,
            $proficiencies->proficiencies()
        );

        self::assertSame(
            [],
            $proficiencies->expertiseSkills()
        );
    }

    /**
     * @return array<string,array{
     *     0:string,
     *     1:array<int,string>
     * }>
     */
    public static function skillProficiencyProvider(): array
    {
        return [
            'market runner' => [
                'market-runner',
                [
                    'acrobatics',
                    'perception',
                ],
            ],
            'shelf scholar' => [
                'shelf-scholar',
                [
                    'arcana',
                    'history',
                ],
            ],
            'waste warden' => [
                'waste-warden',
                [
                    'nature',
                    'survival',
                ],
            ],
            'guild artisan' => [
                'guild-artisan',
                [
                    'insight',
                    'persuasion',
                ],
            ],
            'folk hero' => [
                'folk-hero',
                [
                    'animal-handling',
                    'survival',
                ],
            ],
            'sage' => [
                'sage',
                [
                    'arcana',
                    'history',
                ],
            ],
            'soldier' => [
                'soldier',
                [
                    'athletics',
                    'intimidation',
                ],
            ],
            'criminal' => [
                'criminal',
                [
                    'deception',
                    'stealth',
                ],
            ],
        ];
    }

    #[DataProvider('skillProficiencyProvider')]
    public function testEveryBackgroundGrantsTwoSkills(
        string $background,
        array $expectedSkills
    ): void {
        unset($expectedSkills);

        self::assertCount(
            2,
            Background::fromString(
                $background
            )->skillProficiencies()
                ->proficiencies()
        );
    }

    public function testDeterminesWhetherBackgroundGrantsSkill(): void
    {
        $background = Background::fromString(
            'market-runner'
        );

        self::assertTrue(
            $background->grantsSkillProficiency(
                'acrobatics'
            )
        );

        self::assertTrue(
            $background->grantsSkillProficiency(
                ' PERCEPTION '
            )
        );

        self::assertFalse(
            $background->grantsSkillProficiency(
                'history'
            )
        );
    }

    #[DataProvider('languageChoiceProvider')]
    public function testReturnsLanguageChoiceCount(
        string $background,
        int $expectedChoices
    ): void {
        self::assertSame(
            $expectedChoices,
            Background::fromString(
                $background
            )->languageChoices()
        );
    }

    /**
     * @return array<string,array{string,int}>
     */
    public static function languageChoiceProvider(): array
    {
        return [
            'market runner' => [
                'market-runner',
                1,
            ],
            'shelf scholar' => [
                'shelf-scholar',
                2,
            ],
            'waste warden' => [
                'waste-warden',
                1,
            ],
            'guild artisan' => [
                'guild-artisan',
                1,
            ],
            'folk hero' => [
                'folk-hero',
                0,
            ],
            'sage' => [
                'sage',
                2,
            ],
            'soldier' => [
                'soldier',
                0,
            ],
            'criminal' => [
                'criminal',
                0,
            ],
        ];
    }

    public function testBackgroundsCurrentlyGrantNoFixedLanguages(): void
    {
        foreach (Background::all() as $background) {
            self::assertSame(
                [],
                $background
                    ->fixedLanguageIdentifiers()
            );
        }
    }

    #[DataProvider('toolProficiencyProvider')]
    public function testReturnsToolProficiencyIdentifiers(
        string $background,
        array $expectedTools
    ): void {
        self::assertSame(
            $expectedTools,
            Background::fromString(
                $background
            )->toolProficiencyIdentifiers()
        );
    }

    /**
     * @return array<string,array{
     *     0:string,
     *     1:array<int,string>
     * }>
     */
    public static function toolProficiencyProvider(): array
    {
        return [
            'market runner' => [
                'market-runner',
                [
                    'land-vehicles',
                ],
            ],
            'shelf scholar' => [
                'shelf-scholar',
                [
                    'calligraphers-supplies',
                ],
            ],
            'waste warden' => [
                'waste-warden',
                [
                    'herbalism-kit',
                ],
            ],
            'guild artisan' => [
                'guild-artisan',
                [
                    'artisans-tools',
                ],
            ],
            'folk hero' => [
                'folk-hero',
                [
                    'artisans-tools',
                    'land-vehicles',
                ],
            ],
            'sage' => [
                'sage',
                [],
            ],
            'soldier' => [
                'soldier',
                [
                    'gaming-set',
                    'land-vehicles',
                ],
            ],
            'criminal' => [
                'criminal',
                [
                    'gaming-set',
                    'thieves-tools',
                ],
            ],
        ];
    }

    public function testDeterminesWhetherBackgroundGrantsTool(): void
    {
        $background = Background::fromString(
            'criminal'
        );

        self::assertTrue(
            $background->grantsToolProficiency(
                'gaming-set'
            )
        );

        self::assertTrue(
            $background->grantsToolProficiency(
                ' Thieves Tools '
            )
        );

        self::assertFalse(
            $background->grantsToolProficiency(
                'herbalism-kit'
            )
        );
    }

    public function testSageGrantsNoToolProficiencies(): void
    {
        $background = Background::fromString(
            'sage'
        );

        self::assertSame(
            [],
            $background
                ->toolProficiencyIdentifiers()
        );

        self::assertFalse(
            $background->grantsToolProficiency(
                'gaming-set'
            )
        );
    }

    public function testEqualBackgroundsAreEqual(): void
    {
        $first = Background::fromString(
            'market-runner'
        );

        $second = Background::fromString(
            ' Market Runner '
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentBackgroundsAreNotEqual(): void
    {
        self::assertFalse(
            Background::fromString(
                'market-runner'
            )->equals(
                Background::fromString(
                    'sage'
                )
            )
        );
    }

    public function testConvertsToCanonicalString(): void
    {
        self::assertSame(
            'guild-artisan',
            (string) Background::fromString(
                'Guild Artisan'
            )
        );
    }

    public function testReportsSupportedBackground(): void
    {
        self::assertTrue(
            Background::supports(
                'Market Runner'
            )
        );

        self::assertTrue(
            Background::supports(
                'guild_artisan'
            )
        );
    }

    public function testDoesNotReportUnsupportedBackground(): void
    {
        self::assertFalse(
            Background::supports(
                'sandwich-smuggler'
            )
        );
    }

    public function testRejectsEmptyBackground(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Character background cannot be empty.'
        );

        Background::fromString('');
    }

    public function testRejectsWhitespaceOnlyBackground(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Background::fromString(
            '   '
        );
    }

    public function testRejectsUnsupportedBackground(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character background "sandwich-smuggler" is not supported.'
        );

        Background::fromString(
            'Sandwich Smuggler'
        );
    }

    public function testReturnsEverySupportedBackground(): void
    {
        $backgrounds = Background::all();

        self::assertCount(
            13,
            $backgrounds
        );

        self::assertContainsOnlyInstancesOf(
            Background::class,
            $backgrounds
        );
    }

    public function testAllReturnsCanonicalBackgroundOrder(): void
    {
        $values = array_map(
            static fn (
                Background $background
            ): string => $background->value(),
            Background::all()
        );

        self::assertSame(
            [
                'market-runner',
                'shelf-scholar',
                'waste-warden',
                'guild-artisan',
                'folk-hero',
                'sage',
                'soldier',
                'criminal',
            ],
            $values
        );
    }

    public function testEveryBackgroundUsesSupportedSkills(): void
    {
        foreach (Background::all() as $background) {
            foreach (
                $background
                    ->skillProficiencies()
                    ->proficiencies()
                as $skill
            ) {
                self::assertTrue(
                    SkillProficiencies::supports(
                        $skill
                    )
                );
            }
        }
    }

    public function testBackgroundIsImmutable(): void
    {
        $marketRunner = Background::fromString(
            'market-runner'
        );

        $sage = Background::fromString(
            'sage'
        );

        self::assertSame(
            'market-runner',
            $marketRunner->value()
        );

        self::assertSame(
            'sage',
            $sage->value()
        );

        self::assertNotSame(
            $marketRunner,
            $sage
        );
    }
}

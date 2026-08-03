<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SkillProficienciesTest extends TestCase
{
    public function testCreatesAnEmptyCollection(): void
    {
        $proficiencies =
            SkillProficiencies::none();

        self::assertSame(
            [],
            $proficiencies->proficiencies()
        );

        self::assertSame(
            [],
            $proficiencies->expertiseSkills()
        );

        self::assertTrue(
            $proficiencies->isEmpty()
        );

        self::assertSame(
            0,
            $proficiencies->count()
        );
    }

    public function testCreatesProficienciesFromArrays(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'athletics',
                    'perception',
                ]
            );

        self::assertSame(
            [
                'athletics',
                'perception',
            ],
            $proficiencies->proficiencies()
        );

        self::assertSame(
            [],
            $proficiencies->expertiseSkills()
        );
    }

    public function testCreatesExpertiseFromArrays(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                expertise: [
                    'stealth',
                    'perception',
                ]
            );

        self::assertSame(
            [
                'perception',
                'stealth',
            ],
            $proficiencies->expertiseSkills()
        );
    }

    public function testExpertiseAutomaticallyImpliesProficiency(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                expertise: [
                    'stealth',
                ]
            );

        self::assertTrue(
            $proficiencies->isProficient(
                'stealth'
            )
        );

        self::assertTrue(
            $proficiencies->hasExpertise(
                'stealth'
            )
        );

        self::assertSame(
            ['stealth'],
            $proficiencies->proficiencies()
        );
    }

    public function testCreatesProficientCollectionUsingNamedConstructor(): void
    {
        $proficiencies =
            SkillProficiencies::proficient([
                'athletics',
                'survival',
            ]);

        self::assertSame(
            [
                'athletics',
                'survival',
            ],
            $proficiencies->proficiencies()
        );
    }

    public function testCreatesExpertiseCollectionUsingNamedConstructor(): void
    {
        $proficiencies =
            SkillProficiencies::expertise([
                'investigation',
            ]);

        self::assertSame(
            ['investigation'],
            $proficiencies->expertiseSkills()
        );

        self::assertSame(
            ['investigation'],
            $proficiencies->proficiencies()
        );
    }

    public function testNormalisesSkillIdentifiers(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    ' Animal Handling ',
                    'SLEIGHT_OF_HAND',
                ],
                expertise: [
                    '  PERCEPTION ',
                ]
            );

        self::assertSame(
            [
                'animal-handling',
                'perception',
                'sleight-of-hand',
            ],
            $proficiencies->proficiencies()
        );

        self::assertSame(
            ['perception'],
            $proficiencies->expertiseSkills()
        );
    }

    public function testRemovesDuplicateProficiencies(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'stealth',
                    'Stealth',
                    ' stealth ',
                ]
            );

        self::assertSame(
            ['stealth'],
            $proficiencies->proficiencies()
        );
    }

    public function testReturnsSkillsInCanonicalOrder(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'survival',
                    'acrobatics',
                    'history',
                    'athletics',
                ]
            );

        self::assertSame(
            [
                'acrobatics',
                'athletics',
                'history',
                'survival',
            ],
            $proficiencies->proficiencies()
        );
    }

    #[DataProvider('proficiencyLookupProvider')]
    public function testDeterminesWhetherASkillIsProficient(
        string $skill,
        bool $expected
    ): void {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'athletics',
                    'perception',
                ]
            );

        self::assertSame(
            $expected,
            $proficiencies->isProficient(
                $skill
            )
        );
    }

    /**
     * @return array<string,array{string,bool}>
     */
    public static function proficiencyLookupProvider(): array
    {
        return [
            'athletics is proficient' => [
                'athletics',
                true,
            ],
            'normalised perception is proficient' => [
                ' PERCEPTION ',
                true,
            ],
            'stealth is not proficient' => [
                'stealth',
                false,
            ],
        ];
    }

    #[DataProvider('expertiseLookupProvider')]
    public function testDeterminesWhetherASkillHasExpertise(
        string $skill,
        bool $expected
    ): void {
        $proficiencies =
            SkillProficiencies::fromArrays(
                expertise: [
                    'investigation',
                ]
            );

        self::assertSame(
            $expected,
            $proficiencies->hasExpertise(
                $skill
            )
        );
    }

    /**
     * @return array<string,array{string,bool}>
     */
    public static function expertiseLookupProvider(): array
    {
        return [
            'investigation has expertise' => [
                'investigation',
                true,
            ],
            'normalised investigation has expertise' => [
                ' INVESTIGATION ',
                true,
            ],
            'perception has no expertise' => [
                'perception',
                false,
            ],
        ];
    }

    public function testAddsProficiencyImmutably(): void
    {
        $original =
            SkillProficiencies::proficient([
                'athletics',
            ]);

        $updated = $original->withProficiency(
            'perception'
        );

        self::assertSame(
            ['athletics'],
            $original->proficiencies()
        );

        self::assertSame(
            [
                'athletics',
                'perception',
            ],
            $updated->proficiencies()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testAddingExistingProficiencyDoesNotDuplicateIt(): void
    {
        $updated =
            SkillProficiencies::proficient([
                'athletics',
            ])->withProficiency(
                'ATHLETICS'
            );

        self::assertSame(
            ['athletics'],
            $updated->proficiencies()
        );
    }

    public function testAddsExpertiseImmutably(): void
    {
        $original =
            SkillProficiencies::none();

        $updated = $original->withExpertise(
            'stealth'
        );

        self::assertTrue(
            $updated->isProficient(
                'stealth'
            )
        );

        self::assertTrue(
            $updated->hasExpertise(
                'stealth'
            )
        );

        self::assertTrue(
            $original->isEmpty()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testRemovesProficiencyAndExpertise(): void
    {
        $original =
            SkillProficiencies::fromArrays(
                proficient: [
                    'athletics',
                    'perception',
                ],
                expertise: [
                    'perception',
                ]
            );

        $updated = $original->without(
            'perception'
        );

        self::assertSame(
            ['athletics'],
            $updated->proficiencies()
        );

        self::assertSame(
            [],
            $updated->expertiseSkills()
        );

        self::assertTrue(
            $original->hasExpertise(
                'perception'
            )
        );
    }

    public function testRemovesExpertiseButPreservesProficiency(): void
    {
        $updated =
            SkillProficiencies::fromArrays(
                expertise: [
                    'stealth',
                ]
            )->withoutExpertise(
                'stealth'
            );

        self::assertTrue(
            $updated->isProficient(
                'stealth'
            )
        );

        self::assertFalse(
            $updated->hasExpertise(
                'stealth'
            )
        );
    }

    public function testMergesMultipleProficiencySources(): void
    {
        $classProficiencies =
            SkillProficiencies::proficient([
                'athletics',
                'survival',
            ]);

        $backgroundProficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'perception',
                ],
                expertise: [
                    'stealth',
                ]
            );

        $merged = $classProficiencies->merge(
            $backgroundProficiencies
        );

        self::assertSame(
            [
                'athletics',
                'perception',
                'stealth',
                'survival',
            ],
            $merged->proficiencies()
        );

        self::assertSame(
            ['stealth'],
            $merged->expertiseSkills()
        );
    }

    public function testMergeDoesNotChangeEitherSource(): void
    {
        $first =
            SkillProficiencies::proficient([
                'athletics',
            ]);

        $second =
            SkillProficiencies::expertise([
                'stealth',
            ]);

        $first->merge($second);

        self::assertSame(
            ['athletics'],
            $first->proficiencies()
        );

        self::assertSame(
            ['stealth'],
            $second->expertiseSkills()
        );
    }

    public function testCountsProficientSkillsOnce(): void
    {
        $proficiencies =
            SkillProficiencies::fromArrays(
                proficient: [
                    'athletics',
                ],
                expertise: [
                    'perception',
                ]
            );

        self::assertSame(
            2,
            $proficiencies->count()
        );
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first =
            SkillProficiencies::fromArrays(
                proficient: [
                    'athletics',
                    'perception',
                ],
                expertise: [
                    'stealth',
                ]
            );

        $second =
            SkillProficiencies::fromArrays(
                proficient: [
                    'perception',
                    'athletics',
                ],
                expertise: [
                    'stealth',
                ]
            );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentProficienciesAreNotEqual(): void
    {
        $first =
            SkillProficiencies::proficient([
                'athletics',
            ]);

        $second =
            SkillProficiencies::proficient([
                'perception',
            ]);

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testDifferentExpertiseIsNotEqual(): void
    {
        $first =
            SkillProficiencies::fromArrays(
                proficient: [
                    'stealth',
                ]
            );

        $second =
            SkillProficiencies::fromArrays(
                expertise: [
                    'stealth',
                ]
            );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testRejectsUnsupportedProficiency(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character skill "sandwich-making" is not supported.'
        );

        SkillProficiencies::proficient([
            'sandwich-making',
        ]);
    }

    public function testRejectsUnsupportedExpertise(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        SkillProficiencies::expertise([
            'stock-rotation',
        ]);
    }

    public function testRejectsNonStringProficiencyIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Skill proficiency identifiers must be strings.'
        );

        SkillProficiencies::proficient([
            123,
        ]);
    }

    public function testRejectsUnsupportedLookup(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        SkillProficiencies::none()
            ->isProficient(
                'sandwich-making'
            );
    }

    public function testReturnsEverySupportedSkill(): void
    {
        self::assertSame(
            [
                'acrobatics',
                'animal-handling',
                'arcana',
                'athletics',
                'deception',
                'history',
                'insight',
                'intimidation',
                'investigation',
                'medicine',
                'nature',
                'perception',
                'performance',
                'persuasion',
                'religion',
                'sleight-of-hand',
                'stealth',
                'survival',
            ],
            SkillProficiencies::supportedSkills()
        );
    }

    public function testReportsSupportedSkillIdentifiers(): void
    {
        self::assertTrue(
            SkillProficiencies::supports(
                'Animal Handling'
            )
        );

        self::assertTrue(
            SkillProficiencies::supports(
                'sleight_of_hand'
            )
        );

        self::assertFalse(
            SkillProficiencies::supports(
                'sandwich-making'
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ProficiencyBonus;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skill;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skills;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SkillsTest extends TestCase
{
    public function testCreatesAllSkillsFromAbilityScores(): void
    {
        $skills = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );

        self::assertCount(
            18,
            $skills->all()
        );

        self::assertContainsOnlyInstancesOf(
            Skill::class,
            $skills->all()
        );
    }

    public function testReturnsSkillsInCanonicalOrder(): void
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
            array_keys(
                $this->skills()->all()
            )
        );
    }

    #[DataProvider('skillAbilityProvider')]
    public function testUsesTheCorrectGoverningAbility(
        string $skill,
        string $expectedAbility
    ): void {
        self::assertSame(
            $expectedAbility,
            Skills::governingAbility(
                $skill
            )
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function skillAbilityProvider(): array
    {
        return [
            'acrobatics' => [
                'acrobatics',
                'dexterity',
            ],
            'animal handling' => [
                'animal-handling',
                'wisdom',
            ],
            'arcana' => [
                'arcana',
                'intelligence',
            ],
            'athletics' => [
                'athletics',
                'strength',
            ],
            'deception' => [
                'deception',
                'charisma',
            ],
            'history' => [
                'history',
                'intelligence',
            ],
            'insight' => [
                'insight',
                'wisdom',
            ],
            'intimidation' => [
                'intimidation',
                'charisma',
            ],
            'investigation' => [
                'investigation',
                'intelligence',
            ],
            'medicine' => [
                'medicine',
                'wisdom',
            ],
            'nature' => [
                'nature',
                'intelligence',
            ],
            'perception' => [
                'perception',
                'wisdom',
            ],
            'performance' => [
                'performance',
                'charisma',
            ],
            'persuasion' => [
                'persuasion',
                'charisma',
            ],
            'religion' => [
                'religion',
                'intelligence',
            ],
            'sleight of hand' => [
                'sleight-of-hand',
                'dexterity',
            ],
            'stealth' => [
                'stealth',
                'dexterity',
            ],
            'survival' => [
                'survival',
                'wisdom',
            ],
        ];
    }

    public function testCalculatesUntrainedSkillModifiers(): void
    {
        $skills = $this->skills();

        self::assertSame(
            2,
            $skills->athletics()->modifier()
        );

        self::assertSame(
            1,
            $skills->acrobatics()->modifier()
        );

        self::assertSame(
            0,
            $skills->arcana()->modifier()
        );

        self::assertSame(
            -1,
            $skills->perception()->modifier()
        );

        self::assertSame(
            -2,
            $skills->persuasion()->modifier()
        );
    }

    public function testAddsProficiencyToSelectedSkills(): void
    {
        $skills = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'athletics',
                'perception',
            ]
        );

        self::assertSame(
            4,
            $skills->athletics()->modifier()
        );

        self::assertSame(
            1,
            $skills->perception()->modifier()
        );

        self::assertSame(
            [
                'athletics',
                'perception',
            ],
            $skills->proficiencies()
        );
    }

    public function testExpertiseAddsDoubleProficiency(): void
    {
        $skills = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(3),
            [],
            [
                'stealth',
            ]
        );

        self::assertSame(
            7,
            $skills->stealth()->modifier()
        );

        self::assertTrue(
            $skills->stealth()->isProficient()
        );

        self::assertTrue(
            $skills->stealth()->hasExpertise()
        );

        self::assertSame(
            ['stealth'],
            $skills->expertise()
        );

        self::assertSame(
            ['stealth'],
            $skills->proficiencies()
        );
    }

    public function testNormalisesSkillIdentifiers(): void
    {
        $skills = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                ' Animal Handling ',
                'SLEIGHT_OF_HAND',
            ]
        );

        self::assertSame(
            [
                'animal-handling',
                'sleight-of-hand',
            ],
            $skills->proficiencies()
        );
    }

    public function testRemovesDuplicateProficiencies(): void
    {
        $skills = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [
                'stealth',
                'Stealth',
                ' stealth ',
            ]
        );

        self::assertSame(
            ['stealth'],
            $skills->proficiencies()
        );
    }

    public function testRetrievesSkillsByNormalisedName(): void
    {
        $skills = $this->skills();

        self::assertSame(
            $skills->animalHandling(),
            $skills->get(
                ' Animal Handling '
            )
        );

        self::assertSame(
            $skills->sleightOfHand(),
            $skills->get(
                'sleight_of_hand'
            )
        );
    }

    public function testRejectsUnsupportedSkill(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character skill "sandwich-making" is not supported.'
        );

        $this->skills()->get(
            'sandwich-making'
        );
    }

    public function testRejectsNonStringProficiencyIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Skill proficiency identifiers must be strings.'
        );

        Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            [123]
        );
    }

    public function testReturnsAllNamedSkillAccessors(): void
    {
        $skills = $this->skills();

        self::assertSame(
            $skills->get('acrobatics'),
            $skills->acrobatics()
        );

        self::assertSame(
            $skills->get('animal-handling'),
            $skills->animalHandling()
        );

        self::assertSame(
            $skills->get('arcana'),
            $skills->arcana()
        );

        self::assertSame(
            $skills->get('athletics'),
            $skills->athletics()
        );

        self::assertSame(
            $skills->get('deception'),
            $skills->deception()
        );

        self::assertSame(
            $skills->get('history'),
            $skills->history()
        );

        self::assertSame(
            $skills->get('insight'),
            $skills->insight()
        );

        self::assertSame(
            $skills->get('intimidation'),
            $skills->intimidation()
        );

        self::assertSame(
            $skills->get('investigation'),
            $skills->investigation()
        );

        self::assertSame(
            $skills->get('medicine'),
            $skills->medicine()
        );

        self::assertSame(
            $skills->get('nature'),
            $skills->nature()
        );

        self::assertSame(
            $skills->get('perception'),
            $skills->perception()
        );

        self::assertSame(
            $skills->get('performance'),
            $skills->performance()
        );

        self::assertSame(
            $skills->get('persuasion'),
            $skills->persuasion()
        );

        self::assertSame(
            $skills->get('religion'),
            $skills->religion()
        );

        self::assertSame(
            $skills->get('sleight-of-hand'),
            $skills->sleightOfHand()
        );

        self::assertSame(
            $skills->get('stealth'),
            $skills->stealth()
        );

        self::assertSame(
            $skills->get('survival'),
            $skills->survival()
        );
    }

    public function testCreatesCollectionFromExistingSkills(): void
    {
        $skills = [];

        foreach (
            array_keys($this->skills()->all())
            as $name
        ) {
            $skills[$name] = Skill::fromModifier(
                1
            );
        }

        $collection = Skills::fromSkills(
            $skills
        );

        self::assertCount(
            18,
            $collection->all()
        );

        self::assertSame(
            1,
            $collection->perception()->modifier()
        );
    }

    public function testRejectsIncompleteExistingSkillCollection(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Skills collection must contain every supported Character skill in canonical order.'
        );

        Skills::fromSkills([
            'athletics' => Skill::fromModifier(2),
        ]);
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['athletics'],
            ['stealth']
        );

        $second = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['athletics'],
            ['stealth']
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCollectionsAreNotEqual(): void
    {
        $first = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['athletics']
        );

        $second = Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2),
            ['perception']
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    private function skills(): Skills
    {
        return Skills::fromAbilityScores(
            $this->abilityScores(),
            ProficiencyBonus::fromInt(2)
        );
    }

    private function abilityScores(): AbilityScores
    {
        return AbilityScores::fromScores(
            strength: AbilityScore::fromInt(14),
            dexterity: AbilityScore::fromInt(12),
            constitution: AbilityScore::fromInt(13),
            intelligence: AbilityScore::fromInt(10),
            wisdom: AbilityScore::fromInt(8),
            charisma: AbilityScore::fromInt(6),
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Skills Value Object.
 *
 * Represents all Character skill modifiers.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Skills
{
    /**
     * Supported skills and their governing abilities.
     *
     * @var array<string,string>
     */
    private const SKILLS = [
        'acrobatics' => 'dexterity',
        'animal-handling' => 'wisdom',
        'arcana' => 'intelligence',
        'athletics' => 'strength',
        'deception' => 'charisma',
        'history' => 'intelligence',
        'insight' => 'wisdom',
        'intimidation' => 'charisma',
        'investigation' => 'intelligence',
        'medicine' => 'wisdom',
        'nature' => 'intelligence',
        'perception' => 'wisdom',
        'performance' => 'charisma',
        'persuasion' => 'charisma',
        'religion' => 'intelligence',
        'sleight-of-hand' => 'dexterity',
        'stealth' => 'dexterity',
        'survival' => 'wisdom',
    ];

    /**
     * Create the Skills collection.
     *
     * @param array<string,Skill> $skills
     */
    private function __construct(
        private array $skills
    ) {
        $this->guardAgainstInvalidCollection(
            $skills
        );
    }

    /**
     * Build Skills from Character values.
     */
    public static function fromAbilityScores(
        AbilityScores $abilityScores,
        ProficiencyBonus $proficiencyBonus,
        ?SkillProficiencies $proficiencies = null
    ): self {
        $proficiencies ??=
            SkillProficiencies::none();
    
        $skills = [];
    
        foreach (
            self::SKILLS
            as $skill => $ability
        ) {
            $skills[$skill] = Skill::fromAbility(
                self::abilityScore(
                    $abilityScores,
                    $ability
                ),
                $proficiencyBonus,
                $proficiencies->isProficient(
                    $skill
                ),
                $proficiencies->hasExpertise(
                    $skill
                )
            );
        }
    
        return new self($skills);
    }

    /**
     * Build a collection from existing Skill values.
     *
     * @param array<string,Skill> $skills
     */
    public static function fromSkills(
        array $skills
    ): self {
        $normalised = [];

        foreach ($skills as $name => $skill) {
            if (! is_string($name)) {
                throw new InvalidArgumentException(
                    'Skill collection keys must be strings.'
                );
            }

            if (! $skill instanceof Skill) {
                throw new InvalidArgumentException(
                    'Skill collections may contain only Skill values.'
                );
            }

            $normalised[
                self::normaliseSkill($name)
            ] = $skill;
        }

        return new self($normalised);
    }

    public function acrobatics(): Skill
    {
        return $this->get('acrobatics');
    }

    public function animalHandling(): Skill
    {
        return $this->get('animal-handling');
    }

    public function arcana(): Skill
    {
        return $this->get('arcana');
    }

    public function athletics(): Skill
    {
        return $this->get('athletics');
    }

    public function deception(): Skill
    {
        return $this->get('deception');
    }

    public function history(): Skill
    {
        return $this->get('history');
    }

    public function insight(): Skill
    {
        return $this->get('insight');
    }

    public function intimidation(): Skill
    {
        return $this->get('intimidation');
    }

    public function investigation(): Skill
    {
        return $this->get('investigation');
    }

    public function medicine(): Skill
    {
        return $this->get('medicine');
    }

    public function nature(): Skill
    {
        return $this->get('nature');
    }

    public function perception(): Skill
    {
        return $this->get('perception');
    }

    public function performance(): Skill
    {
        return $this->get('performance');
    }

    public function persuasion(): Skill
    {
        return $this->get('persuasion');
    }

    public function religion(): Skill
    {
        return $this->get('religion');
    }

    public function sleightOfHand(): Skill
    {
        return $this->get('sleight-of-hand');
    }

    public function stealth(): Skill
    {
        return $this->get('stealth');
    }

    public function survival(): Skill
    {
        return $this->get('survival');
    }

    /**
     * Retrieve a Skill by its identifier.
     */
    public function get(
        string $skill
    ): Skill {
        return $this->skills[
            self::normaliseSkill($skill)
        ];
    }

    /**
     * Return all Skills.
     *
     * @return array<string,Skill>
     */
    public function all(): array
    {
        return $this->skills;
    }

    /**
     * Return proficient skill identifiers.
     *
     * @return string[]
     */
    public function proficiencies(): array
    {
        return array_keys(
            array_filter(
                $this->skills,
                static fn (
                    Skill $skill
                ): bool => $skill->isProficient()
            )
        );
    }

    /**
     * Return expertise skill identifiers.
     *
     * @return string[]
     */
    public function expertise(): array
    {
        return array_keys(
            array_filter(
                $this->skills,
                static fn (
                    Skill $skill
                ): bool => $skill->hasExpertise()
            )
        );
    }

    /**
     * Return the governing ability for a skill.
     */
    public static function governingAbility(
        string $skill
    ): string {
        return self::SKILLS[
            self::normaliseSkill($skill)
        ];
    }

    /**
     * Determine whether this collection equals another.
     */
    public function equals(
        self $other
    ): bool {
        foreach ($this->skills as $name => $skill) {
            if (
                ! $skill->equals(
                    $other->get($name)
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve an Ability Score by canonical identifier.
     */
    private static function abilityScore(
        AbilityScores $abilityScores,
        string $ability
    ): AbilityScore {
        return match ($ability) {
            'strength' => $abilityScores->strength(),
            'dexterity' => $abilityScores->dexterity(),
            'constitution' => $abilityScores->constitution(),
            'intelligence' => $abilityScores->intelligence(),
            'wisdom' => $abilityScores->wisdom(),
            'charisma' => $abilityScores->charisma(),
        };
    }

    /**
     * Normalise and validate a skill identifier.
     */
    private static function normaliseSkill(
        string $skill
    ): string {
        $skill = strtolower(
            trim($skill)
        );

        $skill = preg_replace(
            '/[\s_]+/',
            '-',
            $skill
        );

        $skill = is_string($skill)
            ? trim($skill, '-')
            : '';

        if (
            ! array_key_exists(
                $skill,
                self::SKILLS
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character skill "%s" is not supported.',
                    $skill
                )
            );
        }

        return $skill;
    }

    /**
     * Ensure the collection contains every supported skill.
     *
     * @param array<string,Skill> $skills
     */
    private function guardAgainstInvalidCollection(
        array $skills
    ): void {
        if (
            array_keys($skills)
            !== array_keys(self::SKILLS)
        ) {
            throw new InvalidArgumentException(
                'A Skills collection must contain every supported Character skill in canonical order.'
            );
        }

        foreach ($skills as $skill) {
            if (! $skill instanceof Skill) {
                throw new InvalidArgumentException(
                    'Skill collections may contain only Skill values.'
                );
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Skill Proficiencies Value Object.
 *
 * Represents the Character's proficient skills and
 * skills in which they possess expertise.
 *
 * Expertise always implies proficiency.
 *
 * This object can combine proficiency sources from
 * classes, races, backgrounds, feats and other rules
 * without exposing primitive arrays throughout the domain.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class SkillProficiencies
{
    /**
     * Supported Character skill identifiers.
     *
     * @var array<int,string>
     */
    private const SKILLS = [
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
    ];

    /**
     * Create a Skill Proficiencies collection.
     *
     * @param array<int,string> $proficient
     * @param array<int,string> $expertise
     */
    private function __construct(
        private array $proficient,
        private array $expertise
    ) {
    }

    /**
     * Create an empty proficiency collection.
     */
    public static function none(): self
    {
        return new self(
            [],
            []
        );
    }

    /**
     * Create a collection from primitive arrays.
     *
     * Expertise automatically implies proficiency.
     *
     * @param array<int,mixed> $proficient
     * @param array<int,mixed> $expertise
     */
    public static function fromArrays(
        array $proficient = [],
        array $expertise = []
    ): self {
        $normalisedProficiencies =
            self::normaliseSkillList(
                $proficient
            );

        $normalisedExpertise =
            self::normaliseSkillList(
                $expertise
            );

        $normalisedProficiencies =
            self::canonicalOrder(
                array_merge(
                    $normalisedProficiencies,
                    $normalisedExpertise
                )
            );

        return new self(
            proficient: $normalisedProficiencies,
            expertise: self::canonicalOrder(
                $normalisedExpertise
            )
        );
    }

    /**
     * Create a collection containing proficient skills.
     *
     * @param array<int,mixed> $skills
     */
    public static function proficient(
        array $skills
    ): self {
        return self::fromArrays(
            proficient: $skills
        );
    }

    /**
     * Create a collection containing expertise skills.
     *
     * Expertise automatically implies proficiency.
     *
     * @param array<int,mixed> $skills
     */
    public static function expertise(
        array $skills
    ): self {
        return self::fromArrays(
            expertise: $skills
        );
    }

    /**
     * Return all proficient skill identifiers.
     *
     * Expertise skills are included in this collection.
     *
     * @return array<int,string>
     */
    public function proficiencies(): array
    {
        return $this->proficient;
    }

    /**
     * Return all expertise skill identifiers.
     *
     * @return array<int,string>
     */
    public function expertiseSkills(): array
    {
        return $this->expertise;
    }

    /**
     * Determine whether the Character is proficient
     * in a particular skill.
     */
    public function isProficient(
        string $skill
    ): bool {
        return in_array(
            self::normaliseSkill($skill),
            $this->proficient,
            true
        );
    }

    /**
     * Determine whether the Character possesses
     * expertise in a particular skill.
     */
    public function hasExpertise(
        string $skill
    ): bool {
        return in_array(
            self::normaliseSkill($skill),
            $this->expertise,
            true
        );
    }

    /**
     * Add a skill proficiency.
     */
    public function withProficiency(
        string $skill
    ): self {
        $skill = self::normaliseSkill(
            $skill
        );

        return new self(
            proficient: self::canonicalOrder(
                array_merge(
                    $this->proficient,
                    [$skill]
                )
            ),
            expertise: $this->expertise
        );
    }

    /**
     * Add expertise in a skill.
     *
     * Expertise automatically adds proficiency.
     */
    public function withExpertise(
        string $skill
    ): self {
        $skill = self::normaliseSkill(
            $skill
        );

        return new self(
            proficient: self::canonicalOrder(
                array_merge(
                    $this->proficient,
                    [$skill]
                )
            ),
            expertise: self::canonicalOrder(
                array_merge(
                    $this->expertise,
                    [$skill]
                )
            )
        );
    }

    /**
     * Remove proficiency and expertise in a skill.
     */
    public function without(
        string $skill
    ): self {
        $skill = self::normaliseSkill(
            $skill
        );

        return new self(
            proficient: array_values(
                array_filter(
                    $this->proficient,
                    static fn (
                        string $proficiency
                    ): bool => $proficiency !== $skill
                )
            ),
            expertise: array_values(
                array_filter(
                    $this->expertise,
                    static fn (
                        string $expertise
                    ): bool => $expertise !== $skill
                )
            )
        );
    }

    /**
     * Remove expertise while preserving proficiency.
     */
    public function withoutExpertise(
        string $skill
    ): self {
        $skill = self::normaliseSkill(
            $skill
        );

        return new self(
            proficient: $this->proficient,
            expertise: array_values(
                array_filter(
                    $this->expertise,
                    static fn (
                        string $expertise
                    ): bool => $expertise !== $skill
                )
            )
        );
    }

    /**
     * Merge this collection with another source.
     */
    public function merge(
        self $other
    ): self {
        return self::fromArrays(
            proficient: array_merge(
                $this->proficient,
                $other->proficiencies()
            ),
            expertise: array_merge(
                $this->expertise,
                $other->expertiseSkills()
            )
        );
    }

    /**
     * Determine whether the collection has no
     * proficiency or expertise entries.
     */
    public function isEmpty(): bool
    {
        return $this->proficient === []
            && $this->expertise === [];
    }

    /**
     * Return the number of proficient skills.
     *
     * Expertise skills are counted once.
     */
    public function count(): int
    {
        return count(
            $this->proficient
        );
    }

    /**
     * Determine whether this collection equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->proficient
                === $other->proficient
            && $this->expertise
                === $other->expertise;
    }

    /**
     * Return every supported skill identifier.
     *
     * @return array<int,string>
     */
    public static function supportedSkills(): array
    {
        return self::SKILLS;
    }

    /**
     * Determine whether a skill identifier is supported.
     */
    public static function supports(
        string $skill
    ): bool {
        $normalised = self::normaliseInput(
            $skill
        );

        return in_array(
            $normalised,
            self::SKILLS,
            true
        );
    }

    /**
     * Normalise a collection of skill identifiers.
     *
     * @param array<int,mixed> $skills
     *
     * @return array<int,string>
     */
    private static function normaliseSkillList(
        array $skills
    ): array {
        $normalised = [];

        foreach ($skills as $skill) {
            if (! is_string($skill)) {
                throw new InvalidArgumentException(
                    'Skill proficiency identifiers must be strings.'
                );
            }

            $normalised[] =
                self::normaliseSkill(
                    $skill
                );
        }

        return self::canonicalOrder(
            $normalised
        );
    }

    /**
     * Normalise and validate a skill identifier.
     */
    private static function normaliseSkill(
        string $skill
    ): string {
        $normalised = self::normaliseInput(
            $skill
        );

        if (
            ! in_array(
                $normalised,
                self::SKILLS,
                true
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character skill "%s" is not supported.',
                    $normalised
                )
            );
        }

        return $normalised;
    }

    /**
     * Normalise primitive skill input.
     */
    private static function normaliseInput(
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

        return is_string($skill)
            ? trim($skill, '-')
            : '';
    }

    /**
     * Remove duplicates and return skills in
     * canonical Character-sheet order.
     *
     * @param array<int,string> $skills
     *
     * @return array<int,string>
     */
    private static function canonicalOrder(
        array $skills
    ): array {
        $skills = array_values(
            array_unique($skills)
        );

        return array_values(
            array_filter(
                self::SKILLS,
                static fn (
                    string $supportedSkill
                ): bool => in_array(
                    $supportedSkill,
                    $skills,
                    true
                )
            )
        );
    }
}

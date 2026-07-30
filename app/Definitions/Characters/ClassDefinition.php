<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions\Characters;

defined('ABSPATH') || exit;

/**
 * Character Class Definition.
 *
 * Describes a playable character class within the Great Marketrealm.
 *
 * @since 0.3.0
 */
final class ClassDefinition extends CharacterDefinition
{
    /**
     * Set the class hit die.
     *
     * The value should be the numeric die size, such as 6, 8, 10 or 12.
     */
    public function hitDie(int $hitDie): static
    {
        $this->setAttribute(
            'hit_die',
            $hitDie
        );

        return $this;
    }

    /**
     * Add a primary ability.
     */
    public function primaryAbility(string $ability): static
    {
        $this->addToAttribute(
            'primary_abilities',
            $ability
        );

        return $this;
    }

    /**
     * Add a saving throw proficiency.
     */
    public function savingThrow(string $ability): static
    {
        $this->addToAttribute(
            'saving_throws',
            $ability
        );

        return $this;
    }

    /**
     * Set the class spellcasting ability.
     */
    public function spellcastingAbility(string $ability): static
    {
        $this->setAttribute(
            'spellcasting_ability',
            $ability
        );

        return $this;
    }

    /**
     * Add an armour proficiency.
     */
    public function armourProficiency(string $armour): static
    {
        $this->addToAttribute(
            'armour_proficiencies',
            $armour
        );

        return $this;
    }

    /**
     * Add a weapon proficiency.
     */
    public function weaponProficiency(string $weapon): static
    {
        $this->addToAttribute(
            'weapon_proficiencies',
            $weapon
        );

        return $this;
    }

    /**
     * Add a tool proficiency.
     */
    public function toolProficiency(string $tool): static
    {
        $this->addToAttribute(
            'tool_proficiencies',
            $tool
        );

        return $this;
    }

    /**
     * Add a multiclass requirement.
     */
    public function multiclassRequirement(string $requirement): static
    {
        $this->addToAttribute(
            'multiclass_requirements',
            $requirement
        );

        return $this;
    }

    /**
     * Add a subclass available to this class.
     */
    public function subclass(string $subclass): static
    {
        $this->addToAttribute(
            'subclasses',
            $subclass
        );

        return $this;
    }
}

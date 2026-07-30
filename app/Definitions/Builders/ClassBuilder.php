<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions\Builders;

use GreatMarketrealmCompanion\Definitions\Characters\ClassDefinition;
use GreatMarketrealmCompanion\Services\Definitions\Scriptorium;

defined('ABSPATH') || exit;

/**
 * Character Class Definition Builder.
 *
 * Provides the fluent authoring API for playable classes.
 *
 * @since 0.3.0
 */
final class ClassBuilder extends CharacterBuilder
{
    /**
     * Create the class builder.
     */
    public function __construct(
        private ClassDefinition $characterClass,
        Scriptorium $scriptorium
    ) {
        parent::__construct(
            $characterClass,
            $scriptorium
        );
    }

    /**
     * Set the class hit die.
     */
    public function hitDie(int $hitDie): self
    {
        $this->characterClass->hitDie($hitDie);

        return $this;
    }

    /**
     * Add a primary ability.
     */
    public function primaryAbility(string $ability): self
    {
        $this->characterClass->primaryAbility($ability);

        return $this;
    }

    /**
     * Add a saving throw proficiency.
     */
    public function savingThrow(string $ability): self
    {
        $this->characterClass->savingThrow($ability);

        return $this;
    }

    /**
     * Set the spellcasting ability.
     */
    public function spellcastingAbility(string $ability): self
    {
        $this->characterClass->spellcastingAbility($ability);

        return $this;
    }

    /**
     * Add an armour proficiency.
     */
    public function armourProficiency(string $armour): self
    {
        $this->characterClass->armourProficiency($armour);

        return $this;
    }

    /**
     * Add a weapon proficiency.
     */
    public function weaponProficiency(string $weapon): self
    {
        $this->characterClass->weaponProficiency($weapon);

        return $this;
    }

    /**
     * Add a tool proficiency.
     */
    public function toolProficiency(string $tool): self
    {
        $this->characterClass->toolProficiency($tool);

        return $this;
    }

    /**
     * Add a multiclass requirement.
     */
    public function multiclassRequirement(string $requirement): self
    {
        $this->characterClass->multiclassRequirement($requirement);

        return $this;
    }

    /**
     * Add an available subclass.
     */
    public function subclass(string $subclass): self
    {
        $this->characterClass->subclass($subclass);

        return $this;
    }

    /**
     * Return the class definition being authored.
     */
    public function classDefinition(): ClassDefinition
    {
        return $this->characterClass;
    }
}

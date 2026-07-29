<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions\Characters;

use GreatMarketrealmCompanion\Definitions\Definition;

defined('ABSPATH') || exit;

/**
 * Base Character Definition.
 *
 * Provides attributes shared by races, classes and other
 * character-related game content.
 *
 * @since 0.3.0
 */
abstract class CharacterDefinition extends Definition
{
    /**
     * Add a language.
     */
    public function language(string $language): static
    {
        $this->addToAttribute(
            'languages',
            $language
        );

        return $this;
    }

    /**
     * Add a character trait.
     */
    public function trait(string $trait): static
    {
        $this->addToAttribute(
            'traits',
            $trait
        );

        return $this;
    }

    /**
     * Add a character feature.
     */
    public function feature(string $feature): static
    {
        $this->addToAttribute(
            'features',
            $feature
        );

        return $this;
    }

    /**
     * Add a general proficiency.
     */
    public function proficiency(string $proficiency): static
    {
        $this->addToAttribute(
            'proficiencies',
            $proficiency
        );

        return $this;
    }

    /**
     * Add starting equipment.
     */
    public function startingEquipment(string $equipment): static
    {
        $this->addToAttribute(
            'starting_equipment',
            $equipment
        );

        return $this;
    }

    /**
     * Add a prerequisite.
     */
    public function prerequisite(string $prerequisite): static
    {
        $this->addToAttribute(
            'prerequisites',
            $prerequisite
        );

        return $this;
    }

    /**
     * Add a resistance.
     */
    public function resistance(string $resistance): static
    {
        $this->addToAttribute(
            'resistances',
            $resistance
        );

        return $this;
    }

    /**
     * Set the source book or source document.
     */
    public function source(string $source): static
    {
        $this->setAttribute(
            'source',
            $source
        );

        return $this;
    }

    /**
     * Set the expansion containing this definition.
     */
    public function expansion(string $expansion): static
    {
        $this->setAttribute(
            'expansion',
            $expansion
        );

        return $this;
    }

    /**
     * Add a searchable or organisational tag.
     */
    public function tag(string $tag): static
    {
        $this->addToAttribute(
            'tags',
            $tag
        );

        return $this;
    }
}

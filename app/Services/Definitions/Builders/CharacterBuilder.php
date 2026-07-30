<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions\Builders;

use GreatMarketrealmCompanion\Definitions\Characters\CharacterDefinition;
use GreatMarketrealmCompanion\Services\Definitions\Scriptorium;

defined('ABSPATH') || exit;

/**
 * Base Character Definition Builder.
 *
 * Provides the shared authoring API for races, classes
 * and other character-related definitions.
 *
 * @since 0.3.0
 */
abstract class CharacterBuilder extends Builder
{
    /**
     * Create the character builder.
     */
    public function __construct(
        protected CharacterDefinition $character,
        Scriptorium $scriptorium
    ) {
        parent::__construct(
            $character,
            $scriptorium
        );
    }

    /**
     * Add a language.
     */
    public function language(string $language): static
    {
        $this->character->language($language);

        return $this;
    }

    /**
     * Add a character trait.
     */
    public function trait(string $trait): static
    {
        $this->character->trait($trait);

        return $this;
    }

    /**
     * Add a character feature.
     */
    public function feature(string $feature): static
    {
        $this->character->feature($feature);

        return $this;
    }

    /**
     * Add a general proficiency.
     */
    public function proficiency(string $proficiency): static
    {
        $this->character->proficiency($proficiency);

        return $this;
    }

    /**
     * Add starting equipment.
     */
    public function startingEquipment(string $equipment): static
    {
        $this->character->startingEquipment($equipment);

        return $this;
    }

    /**
     * Add a prerequisite.
     */
    public function prerequisite(string $prerequisite): static
    {
        $this->character->prerequisite($prerequisite);

        return $this;
    }

    /**
     * Add a resistance.
     */
    public function resistance(string $resistance): static
    {
        $this->character->resistance($resistance);

        return $this;
    }

    /**
     * Set the source book or source document.
     */
    public function source(string $source): static
    {
        $this->character->source($source);

        return $this;
    }

    /**
     * Set the expansion containing this definition.
     */
    public function expansion(string $expansion): static
    {
        $this->character->expansion($expansion);

        return $this;
    }

    /**
     * Add an organisational tag.
     */
    public function tag(string $tag): static
    {
        $this->character->tag($tag);

        return $this;
    }

    /**
     * Return the character definition being authored.
     */
    public function characterDefinition(): CharacterDefinition
    {
        return $this->character;
    }
}

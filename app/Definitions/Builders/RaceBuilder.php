<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Definitions\Builders;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;
use GreatMarketrealmCompanion\Services\Definitions\Scriptorium;

defined('ABSPATH') || exit;

/**
 * Race Definition Builder.
 *
 * Provides the fluent authoring API for playable races.
 *
 * @since 0.3.0
 */
final class RaceBuilder extends CharacterBuilder
{
    /**
     * Create the race builder.
     */
    public function __construct(
        private RaceDefinition $race,
        Scriptorium $scriptorium
    ) {
        parent::__construct(
            $race,
            $scriptorium
        );
    }

    /**
     * Set the race's walking speed.
     */
    public function speed(int $speed): self
    {
        $this->race->speed($speed);

        return $this;
    }

    /**
     * Set the race's size.
     */
    public function size(string $size): self
    {
        $this->race->size($size);

        return $this;
    }

    /**
     * Set the race's typical lifespan.
     */
    public function lifespan(string $lifespan): self
    {
        $this->race->lifespan($lifespan);

        return $this;
    }

    /**
     * Set the race's creature type.
     */
    public function creatureType(string $creatureType): self
    {
        $this->race->creatureType($creatureType);

        return $this;
    }

    /**
     * Set the race's darkvision distance.
     */
    public function darkvision(int $distance): self
    {
        $this->race->darkvision($distance);

        return $this;
    }

    /**
     * Return the race definition being authored.
     */
    public function raceDefinition(): RaceDefinition
    {
        return $this->race;
    }
}

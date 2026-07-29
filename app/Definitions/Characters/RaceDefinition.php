<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions\Characters;

defined('ABSPATH') || exit;

/**
 * Race Definition.
 *
 * Describes a playable race within the Great Marketrealm.
 *
 * @since 0.3.0
 */
final class RaceDefinition extends CharacterDefinition
{
    /**
     * Set the race's walking speed.
     */
    public function speed(int $speed): static
    {
        $this->setAttribute(
            'speed',
            $speed
        );

        return $this;
    }

    /**
     * Set the race's size.
     */
    public function size(string $size): static
    {
        $this->setAttribute(
            'size',
            $size
        );

        return $this;
    }

    /**
     * Set the race's typical lifespan.
     */
    public function lifespan(string $lifespan): static
    {
        $this->setAttribute(
            'lifespan',
            $lifespan
        );

        return $this;
    }

    /**
     * Set the race's creature type.
     */
    public function creatureType(string $creatureType): static
    {
        $this->setAttribute(
            'creature_type',
            $creatureType
        );

        return $this;
    }

    /**
     * Set the race's darkvision distance.
     */
    public function darkvision(int $distance): static
    {
        $this->setAttribute(
            'darkvision',
            $distance
        );

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Definitions\Characters;

use GreatMarketrealmCompanion\Definitions\Definition;

/**
 * Fluent definition for a playable race.
 */
final class RaceDefinition extends Definition
{
    public function speed(int $speed): static
    {
        return $this->setAttribute(
            'speed',
            $speed
        );
    }

    public function size(string $size): static
    {
        return $this->setAttribute(
            'size',
            $size
        );
    }

    public function language(string $language): static
    {
        return $this->addToAttribute(
            'languages',
            $language
        );
    }

    public function trait(string $trait): static
    {
        return $this->addToAttribute(
            'traits',
            $trait
        );
    }

    public function resistance(string $damageType): static
    {
        return $this->addToAttribute(
            'resistances',
            $damageType
        );
    }

    public function proficiency(string $proficiency): static
    {
        return $this->addToAttribute(
            'proficiencies',
            $proficiency
        );
    }

    public function lifespan(int $years): static
    {
        return $this->setAttribute(
            'lifespan',
            $years
        );
    }

    public function creatureType(string $type): static
    {
        return $this->setAttribute(
            'creature_type',
            $type
        );
    }

    public function darkvision(int $range = 60): static
    {
        return $this->setAttribute(
            'darkvision',
            $range
        );
    }
}

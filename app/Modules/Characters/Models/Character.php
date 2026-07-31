<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;

defined('ABSPATH') || exit;

/**
 * Character Entity.
 *
 * Represents a playable character within the
 * Great Marketrealm.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Character
{
    /**
     * Character constructor.
     */
    private function __construct(
        private CharacterId $id,
        private CharacterName $name,
        private Level $level,
        private Experience $experience,
    ) {
    }

    /**
     * Create a brand new Character.
     */
    public static function create(
        CharacterId $id,
        CharacterName $name,
    ): self {
        return new self(
            $id,
            $name,
            Level::one(),
            Experience::zero(),
        );
    }

    /**
     * Reconstitute an existing Character.
     *
     * Used by repositories when rebuilding an entity
     * from persistence.
     */
    public static function reconstitute(
        CharacterId $id,
        CharacterName $name,
        Level $level,
        Experience $experience,
    ): self {
        return new self(
            $id,
            $name,
            $level,
            $experience,
        );
    }

    /**
     * Get the Character identifier.
     */
    public function id(): CharacterId
    {
        return $this->id;
    }

    /**
     * Get the Character name.
     */
    public function name(): CharacterName
    {
        return $this->name;
    }

    /**
     * Get the Character level.
     */
    public function level(): Level
    {
        return $this->level;
    }

    /**
     * Get the Character experience.
     */
    public function experience(): Experience
    {
        return $this->experience;
    }

    /**
     * Rename the Character.
     */
    public function rename(
        CharacterName $name
    ): void {
        $this->name = $name;
    }

    /**
     * Award experience to the Character.
     */
    public function gainExperience(
        Experience $experience
    ): void {

        $this->experience = $this->experience->gain(
            $experience->value()
        );

        while (
            $this->experience->canLevelUp(
                $this->level
            )
        ) {
            $this->level = $this->level->next();
        }
    }
}

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
}

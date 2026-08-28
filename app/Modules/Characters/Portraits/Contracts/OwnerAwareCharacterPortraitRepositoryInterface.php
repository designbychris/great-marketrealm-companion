<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;

defined('ABSPATH') || exit;

/**
 * Owner-aware portrait lookup used by trusted cross-plugin projections.
 *
 * Ordinary Companion screens continue to use the current-user scoped
 * CharacterPortraitRepositoryInterface. The Tabletop bridge needs to render
 * another seated user's character while preserving that character's owner.
 */
interface OwnerAwareCharacterPortraitRepositoryInterface
{
    public function findForOwner(
        CharacterId $characterId,
        int $ownerId
    ): ?CharacterPortrait;
}

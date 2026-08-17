<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;

defined('ABSPATH') || exit;

/**
 * A Character's membership of a Party.
 *
 * The membership references a Character; it never owns the Character entity.
 */
final class PartyMembership
{
    private function __construct(
        private CharacterId $characterId,
        private PartyMembershipRole $role
    ) {
    }

    public static function member(
        CharacterId $characterId
    ): self {
        return new self(
            $characterId,
            PartyMembershipRole::member()
        );
    }

    public static function withRole(
        CharacterId $characterId,
        PartyMembershipRole $role
    ): self {
        return new self(
            $characterId,
            $role
        );
    }

    public function characterId(): CharacterId
    {
        return $this->characterId;
    }

    public function role(): PartyMembershipRole
    {
        return $this->role;
    }

    public function changeRole(
        PartyMembershipRole $role
    ): void {
        $this->role = $role;
    }
}

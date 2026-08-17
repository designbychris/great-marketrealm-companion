<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOffice;

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
        private PartyMembershipRole $role,
        private PartyOffice $office
    ) {
    }

    public static function member(
        CharacterId $characterId
    ): self {
        return new self(
            $characterId,
            PartyMembershipRole::member(),
            PartyOffice::none()
        );
    }

    public static function withRole(
        CharacterId $characterId,
        PartyMembershipRole $role,
        ?PartyOffice $office = null
    ): self {
        return new self(
            $characterId,
            $role,
            $office ?? PartyOffice::none()
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

    public function office(): PartyOffice
    {
        return $this->office;
    }

    public function changeRole(
        PartyMembershipRole $role
    ): void {
        $this->role = $role;
    }


    public function changeOffice(
        PartyOffice $office
    ): void {
        $this->office = $office;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyStandard;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Adventuring Party aggregate.
 *
 * A Party contains Character references through memberships. It does not
 * own Character entities and must never delete or mutate them implicitly.
 */
final class Party
{
    /**
     * @param array<string,PartyMembership> $memberships
     */
    private function __construct(
        private PartyId $id,
        private PartyName $name,
        private PartyOwnerId $ownerId,
        private array $memberships,
        private PartyStandard $standard
    ) {
    }

    public static function create(
        PartyId $id,
        PartyName $name,
        PartyOwnerId $ownerId
    ): self {
        return new self(
            $id,
            $name,
            $ownerId,
            [],
            PartyStandard::default()
        );
    }

    /**
     * @param PartyMembership[] $memberships
     */
    public static function reconstitute(
        PartyId $id,
        PartyName $name,
        PartyOwnerId $ownerId,
        array $memberships,
        ?PartyStandard $standard = null
    ): self {
        $party = self::create(
            $id,
            $name,
            $ownerId
        );

        $party->changeStandard(
            $standard ?? PartyStandard::default()
        );

        foreach ($memberships as $membership) {
            if (! $membership instanceof PartyMembership) {
                throw new InvalidArgumentException(
                    'A Party may only be reconstituted with Party memberships.'
                );
            }

            $party->addMembership($membership);
        }

        return $party;
    }

    public function id(): PartyId
    {
        return $this->id;
    }

    public function name(): PartyName
    {
        return $this->name;
    }

    public function ownerId(): PartyOwnerId
    {
        return $this->ownerId;
    }

    public function rename(
        PartyName $name
    ): void {
        $this->name = $name;
    }

    public function standard(): PartyStandard
    {
        return $this->standard;
    }

    public function changeStandard(
        PartyStandard $standard
    ): void {
        $this->standard = $standard;
    }

    public function addMember(
        CharacterId $characterId,
        ?PartyMembershipRole $role = null
    ): void {
        $this->addMembership(
            PartyMembership::withRole(
                $characterId,
                $role ?? PartyMembershipRole::member()
            )
        );
    }

    public function removeMember(
        CharacterId $characterId
    ): void {
        unset(
            $this->memberships[
                $characterId->value()
            ]
        );
    }

    public function changeMemberRole(
        CharacterId $characterId,
        PartyMembershipRole $role
    ): void {
        $membership = $this->membership(
            $characterId
        );

        if (! $membership instanceof PartyMembership) {
            throw new InvalidArgumentException(
                'The Character is not a member of this Party.'
            );
        }

        $membership->changeRole($role);
    }

    public function hasMember(
        CharacterId $characterId
    ): bool {
        return isset(
            $this->memberships[
                $characterId->value()
            ]
        );
    }

    public function membership(
        CharacterId $characterId
    ): ?PartyMembership {
        return $this->memberships[
            $characterId->value()
        ] ?? null;
    }

    /**
     * @return PartyMembership[]
     */
    public function memberships(): array
    {
        return array_values(
            $this->memberships
        );
    }

    public function memberCount(): int
    {
        return count(
            $this->memberships
        );
    }

    private function addMembership(
        PartyMembership $membership
    ): void {
        $key = $membership
            ->characterId()
            ->value();

        if (isset($this->memberships[$key])) {
            throw new InvalidArgumentException(
                'The Character is already a member of this Party.'
            );
        }

        $this->memberships[$key] = $membership;
    }
}

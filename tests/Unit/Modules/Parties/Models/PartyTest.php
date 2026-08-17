<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PartyTest extends TestCase
{
    public function testNewPartyStartsWithoutCharacterOwnershipAssumptions(): void
    {
        $party = $this->party();

        self::assertSame(
            0,
            $party->memberCount()
        );

        self::assertSame(
            42,
            $party->ownerId()->value()
        );
    }

    public function testCharacterCanJoinAsMember(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember(
            $characterId
        );

        self::assertTrue(
            $party->hasMember($characterId)
        );

        self::assertSame(
            'member',
            $party->membership($characterId)
                ?->role()
                ->value()
        );
    }

    public function testCharacterCanJoinWithExplicitPartyRole(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember(
            $characterId,
            PartyMembershipRole::leader()
        );

        self::assertTrue(
            $party->membership($characterId)
                ?->role()
                ->isLeader()
        );
    }

    public function testDuplicateCharacterMembershipIsRejected(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember($characterId);

        $this->expectException(
            InvalidArgumentException::class
        );

        $party->addMember($characterId);
    }

    public function testRemovingMembershipDoesNotTouchCharacterEntity(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember($characterId);
        $party->removeMember($characterId);

        self::assertFalse(
            $party->hasMember($characterId)
        );

        self::assertSame(
            $characterId->value(),
            (string) $characterId
        );
    }

    public function testMemberRoleCanChangeWithoutChangingCharacterIdentity(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember($characterId);

        $party->changeMemberRole(
            $characterId,
            PartyMembershipRole::leader()
        );

        self::assertSame(
            $characterId->value(),
            $party->membership($characterId)
                ?->characterId()
                ->value()
        );

        self::assertTrue(
            $party->membership($characterId)
                ?->role()
                ->isLeader()
        );
    }

    public function testChangingRoleForNonMemberIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->party()->changeMemberRole(
            CharacterId::generate(),
            PartyMembershipRole::leader()
        );
    }

    public function testPartyCanBeReconstitutedFromMembershipReferences(): void
    {
        $first = CharacterId::generate();
        $second = CharacterId::generate();

        $party = Party::reconstitute(
            PartyId::generate(),
            PartyName::fromString(
                'The Gilded Grocery Cart'
            ),
            PartyOwnerId::fromInt(42),
            [
                PartyMembership::withRole(
                    $first,
                    PartyMembershipRole::leader()
                ),
                PartyMembership::member(
                    $second
                ),
            ]
        );

        self::assertSame(
            2,
            $party->memberCount()
        );

        self::assertTrue(
            $party->hasMember($first)
        );

        self::assertTrue(
            $party->hasMember($second)
        );
    }

    public function testPartyCanBeRenamedWithoutChangingIdentity(): void
    {
        $party = $this->party();
        $id = $party->id();

        $party->rename(
            PartyName::fromString(
                'The Heroic Trolley'
            )
        );

        self::assertSame(
            'The Heroic Trolley',
            $party->name()->value()
        );

        self::assertTrue(
            $party->id()->equals($id)
        );
    }

    private function party(): Party
    {
        return Party::create(
            PartyId::generate(),
            PartyName::fromString(
                'The Pantry Fellowship'
            ),
            PartyOwnerId::fromInt(42)
        );
    }
}

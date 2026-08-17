<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PartyValueObjectsTest extends TestCase
{
    public function testPartyIdUsesStableUlidIdentity(): void
    {
        $id = PartyId::generate();

        self::assertSame(
            26,
            strlen($id->value())
        );

        self::assertTrue(
            PartyId::fromString(
                $id->value()
            )->equals($id)
        );
    }

    public function testPartyNameValidatesAndPreservesGuildName(): void
    {
        $name = PartyName::fromString(
            'The Pantry Fellowship'
        );

        self::assertSame(
            'The Pantry Fellowship',
            $name->value()
        );

        self::assertSame(
            'The Pantry Fellowship',
            (string) $name
        );
    }

    public function testPartyNameRejectsBoundaryWhitespace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyName::fromString(
            ' The Pantry Fellowship'
        );
    }

    public function testPartyOwnerMustBePositive(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyOwnerId::fromInt(0);
    }

    public function testMembershipRolesAreExplicit(): void
    {
        self::assertTrue(
            PartyMembershipRole::leader()
                ->isLeader()
        );

        self::assertFalse(
            PartyMembershipRole::member()
                ->isLeader()
        );

        self::assertSame(
            'member',
            PartyMembershipRole::member()
                ->value()
        );
    }

    public function testUnknownMembershipRoleIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyMembershipRole::fromString(
            'dungeon-overlord'
        );
    }
}

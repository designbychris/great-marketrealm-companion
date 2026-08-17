<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Offices;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOffice;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FellowshipCompanyOfficesRegressionTest extends TestCase
{
    public function testMembershipRoleAndCompanyOfficeAreIndependent(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember(
            $characterId,
            PartyMembershipRole::leader()
        );

        $party->changeMemberOffice(
            $characterId,
            PartyOffice::fromString(
                PartyOffice::QUARTERMASTER
            )
        );

        $membership = $party->membership($characterId);

        self::assertNotNull($membership);
        self::assertTrue(
            $membership->role()->isLeader()
        );
        self::assertSame(
            'quartermaster',
            $membership->office()->value()
        );
        self::assertSame(
            'Quartermaster',
            $membership->office()->label()
        );
    }

    public function testNewMembershipHasNoCompanyOfficeByDefault(): void
    {
        $party = $this->party();
        $characterId = CharacterId::generate();

        $party->addMember($characterId);

        $membership = $party->membership($characterId);

        self::assertNotNull($membership);
        self::assertSame(
            PartyOffice::NONE,
            $membership->office()->value()
        );
        self::assertFalse(
            $membership->office()->isAssigned()
        );
    }

    public function testSupportedCompanyOfficeCatalogueIsSealed(): void
    {
        self::assertSame(
            [
                'none',
                'quartermaster',
                'chronicler',
                'pathfinder',
                'standard-bearer',
            ],
            PartyOffice::supported()
        );

        self::assertSame(
            'Chronicler',
            PartyOffice::fromString(
                PartyOffice::CHRONICLER
            )->label()
        );

        self::assertSame(
            'Standard Bearer',
            PartyOffice::fromString(
                PartyOffice::STANDARD_BEARER
            )->label()
        );
    }

    public function testUnknownCompanyOfficeIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyOffice::fromString(
            'chief-snack-inspector'
        );
    }

    public function testAssignedCompanyOfficeHasOnlyOneHolder(): void
    {
        $party = $this->party();
        $first = CharacterId::generate();
        $second = CharacterId::generate();
        $quartermaster = PartyOffice::fromString(
            PartyOffice::QUARTERMASTER
        );

        $party->addMember($first);
        $party->addMember($second);

        $party->changeMemberOffice(
            $first,
            $quartermaster
        );

        self::assertSame(
            $first->value(),
            $party
                ->officeHolder($quartermaster)
                ?->characterId()
                ->value()
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $party->changeMemberOffice(
            $second,
            $quartermaster
        );
    }

    public function testOfficeCanBeVacatedAndReassigned(): void
    {
        $party = $this->party();
        $first = CharacterId::generate();
        $second = CharacterId::generate();
        $pathfinder = PartyOffice::fromString(
            PartyOffice::PATHFINDER
        );

        $party->addMember($first);
        $party->addMember($second);

        $party->changeMemberOffice(
            $first,
            $pathfinder
        );

        $party->changeMemberOffice(
            $first,
            PartyOffice::none()
        );

        $party->changeMemberOffice(
            $second,
            $pathfinder
        );

        self::assertSame(
            $second->value(),
            $party
                ->officeHolder($pathfinder)
                ?->characterId()
                ->value()
        );
    }

    public function testRepositoryPersistsOfficeAndDefaultsLegacyRowsToNone(): void
    {
        $root = dirname(__DIR__, 5);
        $repository = file_get_contents(
            $root
            . '/app/Modules/Parties/Repositories/'
            . 'PartyRepository.php'
        );

        self::assertIsString($repository);
        self::assertStringContainsString(
            "'office' => \$membership->office()->value()",
            $repository
        );
        self::assertStringContainsString(
            "PartyOffice::NONE",
            $repository
        );
        self::assertStringContainsString(
            'PartyOffice::fromString(',
            $repository
        );
    }

    public function testOfficeUsesDedicatedOwnerScopedMembershipRoute(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
        );
        $controller = file_get_contents(
            $root
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($routes);
        self::assertIsString($controller);
        self::assertIsString($provider);

        self::assertStringContainsString(
            "'/parties/{id}/members/{character}/office'",
            $routes
        );
        self::assertStringContainsString(
            "'updateMemberOffice'",
            $routes
        );
        self::assertStringContainsString(
            '$this->changeOffice->handle(',
            $controller
        );
        self::assertStringContainsString(
            '(?:role|office)',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_members_'",
            $provider
        );
    }

    public function testOfficeRequestAcceptsOnlySealedOfficeCatalogue(): void
    {
        $root = dirname(__DIR__, 5);
        $request = file_get_contents(
            $root
            . '/app/Modules/Parties/Requests/'
            . 'UpdatePartyMemberOfficeRequest.php'
        );

        self::assertIsString($request);
        self::assertStringContainsString(
            "'in:none,quartermaster,chronicler,pathfinder,standard-bearer'",
            $request
        );
        self::assertStringContainsString(
            'return is_user_logged_in();',
            $request
        );
    }

    public function testRosterKeepsRoleAndOfficeControlsSeparate(): void
    {
        $root = dirname(__DIR__, 5);
        $member = file_get_contents(
            $root
            . '/app/Views/components/entries/'
            . 'fellowship-member.php'
        );

        self::assertIsString($member);
        self::assertStringContainsString(
            'name="role"',
            $member
        );
        self::assertStringContainsString(
            'name="office"',
            $member
        );
        self::assertStringContainsString(
            'Save role',
            $member
        );
        self::assertStringContainsString(
            'Save office',
            $member
        );
        self::assertStringContainsString(
            'gmrc-fellowship-member__office',
            $member
        );
    }

    public function testFellowshipHallShowsAssignedCompanyOfficesAtAGlance(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString(
            'Company Offices',
            $show
        );
        self::assertStringContainsString(
            '$officeHolders',
            $show
        );
        self::assertStringContainsString(
            'gmrc-fellowship-office-card',
            $show
        );
        self::assertStringContainsString(
            '->office()',
            $show
        );
        self::assertStringContainsString(
            '->glyph()',
            $show
        );
        self::assertStringContainsString(
            '->label()',
            $show
        );
    }

    public function testCompanyOfficesDoNotAlterCharacterClassIdentity(): void
    {
        $root = dirname(__DIR__, 5);
        $office = file_get_contents(
            $root
            . '/app/Modules/Parties/Models/ValueObjects/'
            . 'PartyOffice.php'
        );
        $action = file_get_contents(
            $root
            . '/app/Modules/Parties/Actions/'
            . 'ChangePartyMemberOfficeAction.php'
        );

        self::assertIsString($office);
        self::assertIsString($action);
        self::assertStringNotContainsString(
            'CharacterClass',
            $office
        );
        self::assertStringNotContainsString(
            'CharacterClass',
            $action
        );
        self::assertStringNotContainsString(
            '->save($character',
            $action
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

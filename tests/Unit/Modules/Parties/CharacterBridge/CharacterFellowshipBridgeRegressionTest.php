<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\CharacterBridge;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyMembershipRole;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Presenters\CharacterFellowshipPresenter;
use PHPUnit\Framework\TestCase;

final class CharacterFellowshipBridgeRegressionTest extends TestCase
{
    public function testPresenterReturnsOnlyFellowshipsContainingCharacter(): void
    {
        $characterId = CharacterId::generate();
        $otherId = CharacterId::generate();
        $owner = PartyOwnerId::fromInt(42);

        $memberParty = Party::create(
            PartyId::generate(),
            PartyName::fromString('Member Fellowship'),
            $owner
        );
        $memberParty->addMember(
            $characterId,
            PartyMembershipRole::leader()
        );

        $otherParty = Party::create(
            PartyId::generate(),
            PartyName::fromString('Other Fellowship'),
            $owner
        );
        $otherParty->addMember($otherId);

        $presenter = new CharacterFellowshipPresenter(
            new CharacterFellowshipPartyRepositoryStub([
                $memberParty,
                $otherParty,
            ])
        );

        $result = $presenter->present(
            $characterId,
            $owner
        );

        self::assertCount(1, $result);
        self::assertSame(
            'Member Fellowship',
            $result[0]['party']->name()->value()
        );
        self::assertTrue(
            $result[0]['membership']->role()->isLeader()
        );
    }

    public function testPresenterPreservesMembershipOfficeData(): void
    {
        $characterId = CharacterId::generate();
        $owner = PartyOwnerId::fromInt(42);
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Office Fellowship'),
            $owner
        );

        $party->addMember($characterId);
        $party->changeMemberOffice(
            $characterId,
            \GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOffice::fromString(
                'chronicler'
            )
        );

        $presenter = new CharacterFellowshipPresenter(
            new CharacterFellowshipPartyRepositoryStub([$party])
        );

        $result = $presenter->present(
            $characterId,
            $owner
        );

        self::assertCount(1, $result);
        self::assertSame(
            'Chronicler',
            $result[0]['membership']->office()->label()
        );
    }

    public function testPresenterUsesOwnerScopedPartyRepository(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Presenters/'
            . 'CharacterFellowshipPresenter.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            '$this->parties->allForOwner($ownerId)',
            $source
        );
        self::assertStringContainsString(
            '$party->membership($characterId)',
            $source
        );
    }

    public function testCharacterControllerReceivesReadOnlyFellowshipPresenter(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            '?CharacterFellowshipPresenter $fellowships = null',
            $source
        );
        self::assertStringContainsString(
            "PartyOwnerId::fromInt(\$ownerUserId)",
            $source
        );
        self::assertStringContainsString(
            "'fellowships' => \$fellowships",
            $source
        );
    }

    public function testCharacterControllerDirectConstructionRemainsBackwardCompatible(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'private ?CharacterFellowshipPresenter $fellowships = null',
            $source
        );
    }

    public function testNotesTabDisplaysFellowshipMembershipCards(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'gmrc-character-fellowships-title',
            $view
        );
        self::assertStringContainsString(
            'Fellowships',
            $view
        );
        self::assertStringContainsString(
            'gmrc-character-fellowship-card',
            $view
        );
        self::assertStringContainsString(
            'Open Fellowship Hall',
            $view
        );
    }

    public function testFellowshipCardShowsRoleOfficeAndCompanyIdentity(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            '->role()',
            $view
        );
        self::assertStringContainsString(
            '->office()',
            $view
        );
        self::assertStringContainsString(
            '->standard()',
            $view
        );
        self::assertStringContainsString(
            '->emblemGlyph()',
            $view
        );
        self::assertStringContainsString(
            '->memberCount()',
            $view
        );
    }

    public function testFellowshipCardLinksBackToPartyRoute(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            "'parties/'",
            $view
        );
        self::assertStringContainsString(
            'rawurlencode(',
            $view
        );
        self::assertStringContainsString(
            "home_url('/companion/')",
            $view
        );
    }

    public function testCharacterWithoutFellowshipGetsDedicatedEmptyState(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'No Fellowship recorded yet',
            $view
        );
        self::assertStringContainsString(
            'not currently part of',
            $view
        );
    }

    public function testBridgeDoesNotMutatePartyOrCharacterData(): void
    {
        $presenter = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Presenters/'
            . 'CharacterFellowshipPresenter.php'
        );

        self::assertIsString($presenter);
        self::assertStringNotContainsString(
            '->save(',
            $presenter
        );
        self::assertStringNotContainsString(
            'addMember(',
            $presenter
        );
        self::assertStringNotContainsString(
            'removeMember(',
            $presenter
        );
        self::assertStringNotContainsString(
            'changeMember',
            $presenter
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}

final class CharacterFellowshipPartyRepositoryStub implements PartyRepositoryInterface
{
    /**
     * @param Party[] $parties
     */
    public function __construct(
        private array $parties
    ) {
    }

    public function allForOwner(
        PartyOwnerId $ownerId
    ): array {
        return $this->parties;
    }

    public function findForOwner(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?Party {
        return null;
    }

    public function save(Party $party): void
    {
    }

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void {
    }
}

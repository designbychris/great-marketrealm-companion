<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\DungeonMaster;

use PHPUnit\Framework\TestCase;

final class SharedCampaignFellowshipMembershipRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testSharedAccessIncludesOwnedAndCharacterMembershipFellowships(): void
    {
        $service = $this->source('app/Modules/Parties/Services/SharedFellowshipAccess.php');
        $repository = $this->source('app/Modules/Parties/Repositories/PartyRepository.php');

        self::assertStringContainsString('allForAccount(int $accountId)', $service);
        self::assertStringContainsString('allAcrossOwners()', $service);
        self::assertStringContainsString('containsAny($party, $characterIds)', $service);
        self::assertStringContainsString('findForAccount(PartyId $id, int $accountId)', $service);
        self::assertStringContainsString('allAcrossOwners(): array', $repository);
        self::assertStringContainsString('findAcrossOwners(PartyId $id)', $repository);
    }

    public function testFellowshipRegisterUsesSharedAccessWithoutTransferringOwnership(): void
    {
        $controller = $this->source('app/Modules/Parties/Controllers/PartyController.php');
        $view = $this->source('app/Modules/Parties/Views/index.php');

        self::assertStringContainsString('allForAccount($accountId)', $controller);
        self::assertStringContainsString("'can_manage'", $controller);
        self::assertStringContainsString('Shared Campaign Fellowship', $view);
        self::assertStringContainsString('through one of', $view);
        self::assertStringNotContainsString('ownerId = get_current_user_id()', $view);
    }

    public function testSharedFellowshipCanBeOpenedButManagementRemainsOwnerOnly(): void
    {
        $controller = $this->source('app/Modules/Parties/Controllers/PartyController.php');
        $view = $this->source('app/Modules/Parties/Views/show.php');
        $member = $this->source('app/Views/components/entries/fellowship-member.php');

        self::assertStringContainsString('findForAccount(', $controller);
        self::assertStringContainsString("\$presentation['can_manage']", $controller);
        self::assertStringContainsString('if ($canManage)', $view);
        self::assertStringContainsString('Shared Campaign Fellowship', $view);
        self::assertStringContainsString('if ($canManage)', $member);
        self::assertStringContainsString('$canOpenLedger', $member);
    }

    public function testSharedMembersCanMoveCoinOnlyThroughTheirOwnMemberCharacter(): void
    {
        $controller = $this->source('app/Modules/Parties/Controllers/PartyController.php');
        $presenter = $this->source('app/Modules/Parties/Presenters/FellowshipPresenter.php');
        $transfer = $this->source('app/Modules/Parties/Actions/TransferCoinBetweenCharacterAndPartyAction.php');
        $view = $this->source('app/Modules/Parties/Views/show.php');

        self::assertStringContainsString('$accessibleParty->ownerId()', $controller);
        self::assertStringContainsString("'owned_by_account'", $presenter);
        self::assertStringContainsString("'transferable'", $presenter);
        self::assertStringContainsString('$transferable', $view);
        self::assertStringContainsString('$this->characters->find(', $transfer);
        self::assertStringContainsString('Only a member of this Fellowship may transfer coin', $transfer);
    }

    public function testSharedMembersCannotUseCustodianOnlyTreasuryOrRosterControls(): void
    {
        $view = $this->source('app/Modules/Parties/Views/show.php');

        self::assertStringContainsString('Company-only Treasury adjustments', $view);
        self::assertStringContainsString('<?php if ($canManage) : ?>', $view);
        self::assertStringContainsString('Add an Adventurer', $view);
        self::assertStringContainsString('The Fellowship custodian keeps the permanent company record.', $view);
    }

    public function testSharedAccessServiceIsRegisteredWithPartyAndCharacterRepositories(): void
    {
        $provider = $this->source('app/Modules/Parties/PartiesServiceProvider.php');

        self::assertStringContainsString('SharedFellowshipAccess::class', $provider);
        self::assertStringContainsString('$container->make(PartyRepository::class)', $provider);
        self::assertStringContainsString('$container->make(CharacterRepository::class)', $provider);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}

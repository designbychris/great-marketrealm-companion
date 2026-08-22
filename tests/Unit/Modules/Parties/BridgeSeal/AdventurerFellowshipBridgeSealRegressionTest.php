<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\BridgeSeal;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Parties\Actions\TransferCoinBetweenCharacterAndPartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyTreasuryTransaction;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use PHPUnit\Framework\TestCase;

final class AdventurerFellowshipBridgeSealRegressionTest extends TestCase
{
    public function testDuplicateTransferIdCannotMoveCoinTwice(): void
    {
        [$party, $character, $action] = $this->fixture();

        $character->depositToPurse(
            CharacterPurse::fromCopper(500)
        );

        foreach ([1, 2] as $attempt) {
            $action->handle(
                $party->id(),
                $party->ownerId(),
                $character->id(),
                PartyCoinTransferDirection::toTreasury(),
                PartyTreasuryMoney::fromCopper(200),
                'seal-transfer-001',
                'Duplicate-submit guard'
            );
        }

        self::assertSame(
            300,
            $character->purse()->copper()
        );
        self::assertSame(
            200,
            $party->treasury()->balance()->copper()
        );
        self::assertCount(
            1,
            $party->treasury()->transactions()
        );
    }

    public function testTransferIdSurvivesTreasurySerialization(): void
    {
        [$party, $character, $action] = $this->fixture();

        $character->depositToPurse(
            CharacterPurse::fromCopper(100)
        );

        $action->handle(
            $party->id(),
            $party->ownerId(),
            $character->id(),
            PartyCoinTransferDirection::toTreasury(),
            PartyTreasuryMoney::fromCopper(50),
            'seal-transfer-serialization'
        );

        $data = $party->treasury()->transactions()[0]->toArray();

        self::assertSame(
            'seal-transfer-serialization',
            $data['transfer_id']
        );

        $restored = PartyTreasuryTransaction::fromArray(
            $data
        );

        self::assertSame(
            'seal-transfer-serialization',
            $restored->transferId()
        );
    }

    public function testLegacyTreasuryTransactionHydratesWithoutTransferId(): void
    {
        $transaction = PartyTreasuryTransaction::record(
            PartyTreasuryTransaction::DEPOSIT,
            PartyTreasuryMoney::fromCopper(10),
            'Legacy reward'
        );

        $data = $transaction->toArray();
        unset($data['transfer_id']);

        $restored = PartyTreasuryTransaction::fromArray(
            $data
        );

        self::assertNull(
            $restored->transferId()
        );
        self::assertFalse(
            $restored->isCharacterTransfer()
        );
    }

    public function testTransferRequestRequiresIdempotencyToken(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Requests/'
            . 'TransferPartyCoinRequest.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            "'transfer_id'",
            $source
        );
        self::assertStringContainsString(
            'public function transferId(): string',
            $source
        );
        self::assertStringContainsString(
            "'max:64'",
            $source
        );
    }

    public function testTransferFormCarriesUniqueTokenAndAccessibleHelp(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($view);
        self::assertStringContainsString(
            'wp_generate_uuid4()',
            $view
        );
        self::assertStringContainsString(
            'name="transfer_id"',
            $view
        );
        self::assertStringContainsString(
            'data-coin-transfer-form',
            $view
        );
        self::assertStringContainsString(
            'aria-describedby="gmrc-fellowship-coin-transfer-help"',
            $view
        );
        self::assertStringContainsString(
            '<legend class="screen-reader-text">',
            $view
        );
    }

    public function testBridgeKeepsPersonalAndSharedBalancesExplicit(): void
    {
        $characterView = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        $partyView = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($characterView);
        self::assertIsString($partyView);

        self::assertStringContainsString(
            'These coins belong to this adventurer personally.',
            $characterView
        );

        self::assertStringContainsString(
            'Treasury funds belong to the whole Fellowship.',
            $partyView
        );
    }

    public function testCharacterRepositoryLookupRemainsOwnerScoped(): void
    {
        $repository = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Repositories/'
            . 'CharacterRepository.php'
        );

        self::assertIsString($repository);

        $compactRepository = preg_replace(
            '/\s+/',
            ' ',
            $repository
        );

        self::assertIsString($compactRepository);
        self::assertStringContainsString(
            'return $this->allForOwner( get_current_user_id() );',
            $compactRepository
        );
        self::assertStringContainsString(
            'return $this->findForOwner( $id, get_current_user_id() );',
            $compactRepository
        );
        self::assertStringContainsString(
            "'author' => \$ownerId",
            $compactRepository
        );
    }

    public function testPartyLookupRemainsOwnerScopedBeforeTransfer(): void
    {
        $action = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Actions/'
            . 'TransferCoinBetweenCharacterAndPartyAction.php'
        );

        self::assertIsString($action);
        self::assertStringContainsString(
            '$this->finder->find(',
            $action
        );
        self::assertStringContainsString(
            '$ownerId',
            $action
        );
        self::assertStringContainsString(
            '$party->hasMember($characterId)',
            $action
        );
    }

    public function testTransferStillUsesCompensatingRollback(): void
    {
        $action = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Actions/'
            . 'TransferCoinBetweenCharacterAndPartyAction.php'
        );

        self::assertIsString($action);
        self::assertStringContainsString(
            'private function compensate(',
            $action
        );
        self::assertStringContainsString(
            '$character->replacePurse(',
            $action
        );
        self::assertStringContainsString(
            '$party->replaceTreasury(',
            $action
        );
        self::assertStringContainsString(
            'Both balances were restored.',
            $action
        );
    }

    public function testSuccessfulAndFailedTransfersReturnToTreasuryTab(): void
    {
        $controller = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );

        self::assertIsString($controller);
        self::assertGreaterThanOrEqual(
            3,
            substr_count(
                $controller,
                "'treasury'"
            )
        );
        self::assertStringContainsString(
            "'gmrc_fellowship_tab'",
            $controller
        );
    }

    public function testCharacterLedgerRuntimeRoleContractRemainsSealed(): void
    {
        $role = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Models/ValueObjects/'
            . 'PartyMembershipRole.php'
        );

        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($role);
        self::assertIsString($view);
        self::assertStringContainsString(
            'public function label(): string',
            $role
        );
        self::assertStringContainsString(
            'gmrc-character-fellowship-card',
            $view
        );
        self::assertStringNotContainsString(
            'data-character-ledger-boundary',
            $view
        );
    }

    public function testBridgeSealKeepsOrdinaryTreasuryActionsIndependent(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Routes.php'
        );

        self::assertIsString($routes);
        self::assertStringContainsString(
            "'/parties/{id}/treasury/deposit'",
            $routes
        );
        self::assertStringContainsString(
            "'/parties/{id}/treasury/withdraw'",
            $routes
        );
        self::assertStringContainsString(
            "'/parties/{id}/treasury/transfer'",
            $routes
        );
    }

    /**
     * @return array{
     *   Party,
     *   Character,
     *   TransferCoinBetweenCharacterAndPartyAction
     * }
     */
    private function fixture(): array
    {
        $owner = PartyOwnerId::fromInt(42);

        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Seal Fellowship'),
            $owner
        );

        $character = Character::create(
            CharacterId::generate(),
            CharacterName::fromString('Copper Carrot'),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );

        $party->addMember(
            $character->id()
        );

        $characters = new BridgeSealCharacterRepositoryStub(
            $character
        );

        $parties = new BridgeSealPartyRepositoryStub(
            $party
        );

        return [
            $party,
            $character,
            new TransferCoinBetweenCharacterAndPartyAction(
                $characters,
                $parties,
                new PartyFinder($parties)
            ),
        ];
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}

final class BridgeSealCharacterRepositoryStub implements CharacterRepositoryInterface
{
    public function __construct(
        private Character $character
    ) {
    }

    public function all(): array
    {
        return [$this->character];
    }

    public function find(
        CharacterId $id
    ): ?Character {
        return $id->equals(
            $this->character->id()
        )
            ? $this->character
            : null;
    }

    public function save(
        Character $character
    ): void {
        $this->character = $character;
    }

    public function delete(
        CharacterId $id
    ): void {
    }
}

final class BridgeSealPartyRepositoryStub implements PartyRepositoryInterface
{
    public function __construct(
        private Party $party
    ) {
    }

    public function allForOwner(
        PartyOwnerId $ownerId
    ): array {
        return [$this->party];
    }

    public function findForOwner(
        PartyId $id,
        PartyOwnerId $ownerId
    ): ?Party {
        return (
            $id->value() === $this->party->id()->value()
            && $ownerId->value()
                === $this->party->ownerId()->value()
        )
            ? $this->party
            : null;
    }

    public function save(
        Party $party
    ): void {
        $this->party = $party;
    }

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void {
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Transfers;

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
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CoinBetweenCompanionsRegressionTest extends TestCase
{
    public function testTransferFromCharacterMovesExactlyOneAmountToTreasury(): void
    {
        [$party, $character, $action] = $this->fixture();

        $character->depositToPurse(
            CharacterPurse::fromCoins(10, 0, 0)
        );

        $action->handle(
            $party->id(),
            $party->ownerId(),
            $character->id(),
            PartyCoinTransferDirection::toTreasury(),
            PartyTreasuryMoney::fromCoins(2, 5, 0),
            'transfer-deposit-1',
            'Shared expedition fund'
        );

        self::assertSame(
            750,
            $character->purse()->copper()
        );
        self::assertSame(
            250,
            $party->treasury()->balance()->copper()
        );

        $transaction = $party->treasury()->transactions()[0];

        self::assertTrue($transaction->isCharacterTransfer());
        self::assertSame(
            $character->id()->value(),
            $transaction->characterId()
        );
        self::assertSame(
            'to-treasury',
            $transaction->transferDirection()
        );
        self::assertStringContainsString(
            $character->name()->value(),
            $transaction->note()
        );
    }

    public function testTransferToCharacterMovesExactlyOneAmountFromTreasury(): void
    {
        [$party, $character, $action] = $this->fixture();

        $party->depositTreasury(
            PartyTreasuryMoney::fromCoins(12, 0, 0),
            'Initial company funds'
        );

        $action->handle(
            $party->id(),
            $party->ownerId(),
            $character->id(),
            PartyCoinTransferDirection::toCharacter(),
            PartyTreasuryMoney::fromCoins(3, 0, 0),
            'transfer-withdraw-1',
            'Personal share'
        );

        self::assertSame(
            300,
            $character->purse()->copper()
        );
        self::assertSame(
            900,
            $party->treasury()->balance()->copper()
        );

        $transaction = $party->treasury()->transactions()[1];

        self::assertFalse($transaction->isDeposit());
        self::assertSame(
            'to-character',
            $transaction->transferDirection()
        );
        self::assertSame(
            $character->id()->value(),
            $transaction->characterId()
        );
    }

    public function testNonMemberCannotTransferWithFellowshipTreasury(): void
    {
        $owner = PartyOwnerId::fromInt(42);
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Closed Fellowship'),
            $owner
        );
        $character = $this->character();

        $characters = new CoinTransferCharacterRepositoryStub($character);
        $parties = new CoinTransferPartyRepositoryStub($party);
        $action = new TransferCoinBetweenCharacterAndPartyAction(
            $characters,
            $parties,
            new PartyFinder($parties)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('member');

        $action->handle(
            $party->id(),
            $owner,
            $character->id(),
            PartyCoinTransferDirection::toTreasury(),
            PartyTreasuryMoney::fromCopper(1),
            'transfer-non-member'
        );
    }

    public function testCharacterCannotTransferMoreThanPersonalPurse(): void
    {
        [$party, $character, $action] = $this->fixture();

        $character->depositToPurse(
            CharacterPurse::fromCopper(100)
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $action->handle(
            $party->id(),
            $party->ownerId(),
            $character->id(),
            PartyCoinTransferDirection::toTreasury(),
            PartyTreasuryMoney::fromCopper(101),
            'transfer-overdraw'
        );
    }

    public function testTreasuryCannotTransferMoreThanSharedCoffers(): void
    {
        [$party, $character, $action] = $this->fixture();

        $party->depositTreasury(
            PartyTreasuryMoney::fromCopper(100)
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $action->handle(
            $party->id(),
            $party->ownerId(),
            $character->id(),
            PartyCoinTransferDirection::toCharacter(),
            PartyTreasuryMoney::fromCopper(101),
            'transfer-overdraw'
        );
    }

    public function testTransferDirectionCatalogueIsSealed(): void
    {
        self::assertSame(
            'to-treasury',
            PartyCoinTransferDirection::toTreasury()->value()
        );
        self::assertSame(
            'to-character',
            PartyCoinTransferDirection::toCharacter()->value()
        );
        self::assertTrue(
            PartyCoinTransferDirection::toTreasury()->isToTreasury()
        );
        self::assertFalse(
            PartyCoinTransferDirection::toCharacter()->isToTreasury()
        );
    }

    public function testInvalidTransferDirectionIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        PartyCoinTransferDirection::fromString(
            'into-a-mysterious-pocket'
        );
    }

    public function testTreasuryTransactionPersistsTransferProvenance(): void
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
            'transfer-provenance'
        );

        $data = $party
            ->treasury()
            ->transactions()[0]
            ->toArray();

        self::assertSame(
            $character->id()->value(),
            $data['character_id']
        );
        self::assertSame(
            'to-treasury',
            $data['transfer_direction']
        );
    }

    public function testPersistenceFailureRestoresBothBalances(): void
    {
        $owner = PartyOwnerId::fromInt(42);
        $party = Party::create(
            PartyId::generate(),
            PartyName::fromString('Rollback Fellowship'),
            $owner
        );
        $character = $this->character();

        $party->addMember($character->id());
        $character->depositToPurse(
            CharacterPurse::fromCopper(500)
        );

        $characters = new CoinTransferCharacterRepositoryStub($character);
        $parties = new CoinTransferPartyRepositoryStub(
            $party,
            true
        );
        $action = new TransferCoinBetweenCharacterAndPartyAction(
            $characters,
            $parties,
            new PartyFinder($parties)
        );

        try {
            $action->handle(
                $party->id(),
                $owner,
                $character->id(),
                PartyCoinTransferDirection::toTreasury(),
                PartyTreasuryMoney::fromCopper(200),
                'transfer-rollback'
            );

            self::fail(
                'The simulated persistence failure should escape.'
            );
        } catch (RuntimeException $exception) {
            self::assertStringContainsString(
                'Both balances were restored',
                $exception->getMessage()
            );
        }

        self::assertSame(
            500,
            $character->purse()->copper()
        );
        self::assertSame(
            0,
            $party->treasury()->balance()->copper()
        );
        self::assertSame(
            [],
            $party->treasury()->transactions()
        );
        self::assertGreaterThanOrEqual(
            2,
            $characters->saveCount
        );
        self::assertGreaterThanOrEqual(
            2,
            $parties->saveCount
        );
    }

    public function testTransferRequestValidatesMemberDirectionCoinsAndNote(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Requests/'
            . 'TransferPartyCoinRequest.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            "'character_id'",
            $source
        );
        self::assertStringContainsString(
            "'in:to-treasury,to-character'",
            $source
        );
        self::assertStringContainsString(
            "'max:999999'",
            $source
        );
        self::assertStringContainsString(
            "'max:120'",
            $source
        );
    }

    public function testTransferUsesDedicatedRouteAndNonceContract(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Routes.php'
        );
        $provider = file_get_contents(
            $this->root()
            . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($routes);
        self::assertIsString($provider);
        self::assertStringContainsString(
            "'/parties/{id}/treasury/transfer'",
            $routes
        );
        self::assertStringContainsString(
            "'transferCoin'",
            $routes
        );
        self::assertStringContainsString(
            '#^parties/([^/]+)/treasury/transfer$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_coin_transfer_'",
            $provider
        );
    }

    public function testFellowshipHallShowsMemberPursesAndTransferDirection(): void
    {
        $show = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString(
            'Coin Between Companions',
            $show
        );
        self::assertStringContainsString(
            'Transfer with an Adventurer',
            $show
        );
        self::assertStringContainsString(
            '->purse()',
            $show
        );
        self::assertStringContainsString(
            'Adventurer → Fellowship Treasury',
            $show
        );
        self::assertStringContainsString(
            'Fellowship Treasury → Adventurer',
            $show
        );
        self::assertStringContainsString(
            'Move Coin Between Purses',
            $show
        );
    }

    public function testTreasuryLedgerShowsCharacterTransferProvenance(): void
    {
        $show = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString(
            'isCharacterTransfer()',
            $show
        );
        self::assertStringContainsString(
            'transferDirection()',
            $show
        );
        self::assertStringContainsString(
            'characterId()',
            $show
        );
        self::assertStringContainsString(
            '$memberNamesById',
            $show
        );
    }

    public function testSuccessfulTransferReturnsToTreasuryTab(): void
    {
        $controller = file_get_contents(
            $this->root()
            . '/app/Modules/Parties/Controllers/'
            . 'PartyController.php'
        );

        self::assertIsString($controller);
        self::assertStringContainsString(
            "'gmrc_fellowship_tab'",
            $controller
        );
        self::assertStringContainsString(
            "'treasury'",
            $controller
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
            PartyName::fromString('Pantry Fellowship'),
            $owner
        );
        $character = $this->character();

        $party->addMember(
            $character->id()
        );

        $characters = new CoinTransferCharacterRepositoryStub(
            $character
        );
        $parties = new CoinTransferPartyRepositoryStub(
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

    private function character(): Character
    {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString(
                'Penny Parsnip'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}

final class CoinTransferCharacterRepositoryStub implements CharacterRepositoryInterface
{
    public int $saveCount = 0;

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
        return $id->equals($this->character->id())
            ? $this->character
            : null;
    }

    public function save(
        Character $character
    ): void {
        $this->saveCount++;
        $this->character = $character;
    }

    public function delete(
        CharacterId $id
    ): void {
    }
}

final class CoinTransferPartyRepositoryStub implements PartyRepositoryInterface
{
    public int $saveCount = 0;

    private bool $hasFailed = false;

    public function __construct(
        private Party $party,
        private bool $failFirstSave = false
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
        return $id->value() === $this->party->id()->value()
            ? $this->party
            : null;
    }

    public function save(
        Party $party
    ): void {
        $this->saveCount++;

        if (
            $this->failFirstSave
            && ! $this->hasFailed
        ) {
            $this->hasFailed = true;

            throw new RuntimeException(
                'Simulated Party persistence failure.'
            );
        }

        $this->party = $party;
    }

    public function delete(
        PartyId $id,
        PartyOwnerId $ownerId
    ): void {
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Treasury;

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyTreasury;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyTreasuryTransaction;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyName;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FellowshipTreasuryRegressionTest extends TestCase
{
    public function testNewFellowshipStartsWithEmptyTreasury(): void
    {
        $party = $this->party();

        self::assertSame(0, $party->treasury()->balance()->copper());
        self::assertSame([], $party->treasury()->transactions());
    }

    public function testCoinValuesCanonicaliseToCopper(): void
    {
        $money = PartyTreasuryMoney::fromCoins(3, 4, 5);

        self::assertSame(345, $money->copper());
        self::assertSame(
            ['gold' => 3, 'silver' => 4, 'copper' => 5],
            $money->coins()
        );
    }

    public function testMoneyFormatsGuildCoinBreakdown(): void
    {
        self::assertSame(
            '12 gp · 3 sp · 4 cp',
            PartyTreasuryMoney::fromCopper(1234)->formatted()
        );
    }

    public function testNegativeCoinAmountIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PartyTreasuryMoney::fromCoins(-1, 0, 0);
    }

    public function testDepositIncreasesBalanceAndRecordsTransaction(): void
    {
        $treasury = PartyTreasury::empty();

        $transaction = $treasury->deposit(
            PartyTreasuryMoney::fromCoins(2, 5, 0),
            'Guild contract reward'
        );

        self::assertSame(250, $treasury->balance()->copper());
        self::assertTrue($transaction->isDeposit());
        self::assertSame('Guild contract reward', $transaction->note());
        self::assertCount(1, $treasury->transactions());
    }

    public function testWithdrawalDecreasesBalanceAndRecordsTransaction(): void
    {
        $treasury = PartyTreasury::empty();

        $treasury->deposit(
            PartyTreasuryMoney::fromCoins(5, 0, 0)
        );

        $transaction = $treasury->withdraw(
            PartyTreasuryMoney::fromCoins(1, 2, 5),
            'Expedition supplies'
        );

        self::assertSame(375, $treasury->balance()->copper());
        self::assertFalse($transaction->isDeposit());
        self::assertSame('Expedition supplies', $transaction->note());
        self::assertCount(2, $treasury->transactions());
    }

    public function testTreasuryCannotBeOverdrawn(): void
    {
        $treasury = PartyTreasury::empty();

        $treasury->deposit(
            PartyTreasuryMoney::fromCoins(1, 0, 0)
        );

        $this->expectException(InvalidArgumentException::class);

        $treasury->withdraw(
            PartyTreasuryMoney::fromCoins(1, 0, 1)
        );
    }

    public function testZeroDepositIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PartyTreasury::empty()->deposit(
            PartyTreasuryMoney::zero()
        );
    }

    public function testZeroWithdrawalIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PartyTreasury::empty()->withdraw(
            PartyTreasuryMoney::zero()
        );
    }

    public function testRecentLedgerReturnsNewestTransactionsFirst(): void
    {
        $treasury = PartyTreasury::empty();

        $treasury->deposit(
            PartyTreasuryMoney::fromCopper(100),
            'First'
        );
        $treasury->deposit(
            PartyTreasuryMoney::fromCopper(200),
            'Second'
        );

        $recent = $treasury->recent(1);

        self::assertCount(1, $recent);
        self::assertSame('Second', $recent[0]->note());
    }

    public function testPartyDelegatesTreasuryMutationsWithoutTouchingCharacters(): void
    {
        $party = $this->party();

        $party->depositTreasury(
            PartyTreasuryMoney::fromCoins(4, 0, 0),
            'Shared loot'
        );

        $party->withdrawTreasury(
            PartyTreasuryMoney::fromCoins(1, 0, 0),
            'Shared expense'
        );

        self::assertSame(300, $party->treasury()->balance()->copper());
        self::assertCount(2, $party->treasury()->transactions());
    }

    public function testRepositoryPersistsTreasurySeparatelyAndLegacyPartiesDefaultEmpty(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root . '/app/Modules/Parties/Repositories/PartyRepository.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString("'_gmrc_party_treasury'", $source);
        self::assertStringContainsString("'balance_copper'", $source);
        self::assertStringContainsString("'transactions'", $source);
        self::assertStringContainsString('PartyTreasury::empty()', $source);
        self::assertStringContainsString('$this->treasury($post->ID)', $source);
    }

    public function testTreasuryHasOwnerScopedDepositAndWithdrawalActions(): void
    {
        $root = dirname(__DIR__, 5);

        foreach ([
            'DepositPartyTreasuryAction.php',
            'WithdrawPartyTreasuryAction.php',
        ] as $file) {
            $source = file_get_contents(
                $root . '/app/Modules/Parties/Actions/' . $file
            );

            self::assertIsString($source);
            self::assertStringContainsString('$this->finder->find(', $source);
            self::assertStringContainsString('$this->parties->save($party)', $source);
        }
    }

    public function testTreasuryHttpRoutesAreSeparateFromCharacterVitals(): void
    {
        $root = dirname(__DIR__, 5);
        $routes = file_get_contents(
            $root . '/app/Modules/Parties/Routes.php'
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
        self::assertStringNotContainsString(
            'vital-measures',
            $routes
        );
    }

    public function testTreasuryRequestsConstrainCoinsAndLedgerNote(): void
    {
        $root = dirname(__DIR__, 5);

        foreach ([
            'DepositPartyTreasuryRequest.php',
            'WithdrawPartyTreasuryRequest.php',
        ] as $file) {
            $source = file_get_contents(
                $root . '/app/Modules/Parties/Requests/' . $file
            );

            self::assertIsString($source);
            self::assertStringContainsString("'min:0'", $source);
            self::assertStringContainsString("'max:160'", $source);
            self::assertStringContainsString('return is_user_logged_in();', $source);
        }
    }

    public function testTreasuryUsesDedicatedNonceContract(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents(
            $root . '/app/Providers/FrontendServiceProvider.php'
        );

        self::assertIsString($provider);
        self::assertStringContainsString(
            '#^parties/([^/]+)/treasury/(?:deposit|withdraw)$#',
            $provider
        );
        self::assertStringContainsString(
            "'gmrc_party_treasury_'",
            $provider
        );
    }

    public function testFellowshipHallShowsBalanceTransactionsAndQuartermasterAwareness(): void
    {
        $root = dirname(__DIR__, 5);
        $show = file_get_contents(
            $root . '/app/Modules/Parties/Views/show.php'
        );

        self::assertIsString($show);
        self::assertStringContainsString('Fellowship Treasury', $show);
        self::assertStringContainsString('Current company purse', $show);
        self::assertStringContainsString('Record External Income', $show);
        self::assertStringContainsString('Record Company Expense', $show);
        self::assertStringContainsString('Recent Treasury Ledger', $show);
        self::assertStringContainsString('$quartermasterName', $show);
        self::assertStringContainsString('Auby’s Treasury Note', $show);
    }

    private function party(): Party
    {
        return Party::create(
            PartyId::generate(),
            PartyName::fromString('The Pantry Fellowship'),
            PartyOwnerId::fromInt(42)
        );
    }
}

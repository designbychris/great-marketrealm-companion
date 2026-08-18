<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class PartyTreasury
{
    /**
     * @param PartyTreasuryTransaction[] $transactions
     */
    private function __construct(
        private PartyTreasuryMoney $balance,
        private array $transactions
    ) {
    }

    public static function empty(): self
    {
        return new self(
            PartyTreasuryMoney::zero(),
            []
        );
    }

    /**
     * @param PartyTreasuryTransaction[] $transactions
     */
    public static function reconstitute(
        PartyTreasuryMoney $balance,
        array $transactions
    ): self {
        foreach ($transactions as $transaction) {
            if (! $transaction instanceof PartyTreasuryTransaction) {
                throw new InvalidArgumentException(
                    'A Treasury may only contain Treasury transactions.'
                );
            }
        }

        return new self(
            $balance,
            array_values($transactions)
        );
    }

    public function balance(): PartyTreasuryMoney
    {
        return $this->balance;
    }

    public function hasTransferId(
        string $transferId
    ): bool {
        foreach ($this->transactions as $transaction) {
            if (
                $transaction->transferId()
                === $transferId
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return PartyTreasuryTransaction[]
     */
    public function transactions(): array
    {
        return $this->transactions;
    }

    /**
     * @return PartyTreasuryTransaction[]
     */
    public function recent(int $limit = 6): array
    {
        return array_slice(
            array_reverse($this->transactions),
            0,
            max(1, $limit)
        );
    }

    public function deposit(
        PartyTreasuryMoney $amount,
        string $note = ''
    ): PartyTreasuryTransaction {
        if ($amount->isZero()) {
            throw new InvalidArgumentException(
                'A Treasury deposit must contain at least one copper piece.'
            );
        }

        $transaction = PartyTreasuryTransaction::record(
            PartyTreasuryTransaction::DEPOSIT,
            $amount,
            $note
        );

        $this->balance = $this->balance->plus($amount);
        $this->transactions[] = $transaction;

        return $transaction;
    }

    public function depositFromCharacter(
        PartyTreasuryMoney $amount,
        string $note,
        string $characterId,
        string $transferId
    ): PartyTreasuryTransaction {
        if ($amount->isZero()) {
            throw new InvalidArgumentException(
                'A Treasury transfer must contain at least one copper piece.'
            );
        }

        $transaction = PartyTreasuryTransaction::record(
            PartyTreasuryTransaction::DEPOSIT,
            $amount,
            $note,
            null,
            $characterId,
            PartyCoinTransferDirection::TO_TREASURY,
            $transferId
        );

        $this->balance = $this->balance->plus($amount);
        $this->transactions[] = $transaction;

        return $transaction;
    }

    public function withdraw(
        PartyTreasuryMoney $amount,
        string $note = ''
    ): PartyTreasuryTransaction {
        if ($amount->isZero()) {
            throw new InvalidArgumentException(
                'A Treasury withdrawal must contain at least one copper piece.'
            );
        }

        $nextBalance = $this->balance->minus($amount);

        $transaction = PartyTreasuryTransaction::record(
            PartyTreasuryTransaction::WITHDRAWAL,
            $amount,
            $note
        );

        $this->balance = $nextBalance;
        $this->transactions[] = $transaction;

        return $transaction;
    }
    public function withdrawToCharacter(
        PartyTreasuryMoney $amount,
        string $note,
        string $characterId,
        string $transferId
    ): PartyTreasuryTransaction {
        if ($amount->isZero()) {
            throw new InvalidArgumentException(
                'A Treasury transfer must contain at least one copper piece.'
            );
        }

        $nextBalance = $this->balance->minus($amount);

        $transaction = PartyTreasuryTransaction::record(
            PartyTreasuryTransaction::WITHDRAWAL,
            $amount,
            $note,
            null,
            $characterId,
            PartyCoinTransferDirection::TO_CHARACTER,
            $transferId
        );

        $this->balance = $nextBalance;
        $this->transactions[] = $transaction;

        return $transaction;
    }

}

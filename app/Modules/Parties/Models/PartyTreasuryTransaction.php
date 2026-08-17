<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use DateTimeImmutable;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryTransactionId;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class PartyTreasuryTransaction
{
    public const DEPOSIT = 'deposit';
    public const WITHDRAWAL = 'withdrawal';

    private function __construct(
        private PartyTreasuryTransactionId $id,
        private string $type,
        private PartyTreasuryMoney $amount,
        private string $note,
        private DateTimeImmutable $occurredAt
    ) {
        if (! in_array($type, [self::DEPOSIT, self::WITHDRAWAL], true)) {
            throw new InvalidArgumentException(
                'The Treasury transaction type is invalid.'
            );
        }

        if ($amount->isZero()) {
            throw new InvalidArgumentException(
                'A Treasury transaction must contain at least one copper piece.'
            );
        }

        if (mb_strlen($note) > 160) {
            throw new InvalidArgumentException(
                'A Treasury note cannot contain more than 160 characters.'
            );
        }
    }

    public static function record(
        string $type,
        PartyTreasuryMoney $amount,
        string $note,
        ?DateTimeImmutable $occurredAt = null
    ): self {
        return new self(
            PartyTreasuryTransactionId::generate(),
            $type,
            $amount,
            trim($note),
            $occurredAt ?? new DateTimeImmutable('now')
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            PartyTreasuryTransactionId::fromString(
                (string) ($data['id'] ?? '')
            ),
            (string) ($data['type'] ?? ''),
            PartyTreasuryMoney::fromCopper(
                (int) ($data['amount_copper'] ?? -1)
            ),
            trim((string) ($data['note'] ?? '')),
            new DateTimeImmutable(
                (string) ($data['occurred_at'] ?? '')
            )
        );
    }

    public function id(): PartyTreasuryTransactionId
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function amount(): PartyTreasuryMoney
    {
        return $this->amount;
    }

    public function note(): string
    {
        return $this->note;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function isDeposit(): bool
    {
        return $this->type === self::DEPOSIT;
    }

    /**
     * @return array{
     *   id:string,type:string,amount_copper:int,note:string,occurred_at:string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type,
            'amount_copper' => $this->amount->copper(),
            'note' => $this->note,
            'occurred_at' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
}

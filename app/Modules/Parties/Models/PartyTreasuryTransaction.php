<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use DateTimeImmutable;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryMoney;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyTreasuryTransactionId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyCoinTransferDirection;
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
        private DateTimeImmutable $occurredAt,
        private ?string $characterId,
        private ?string $transferDirection,
        private ?string $transferId
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


        if (
            ($characterId === null)
            !== ($transferDirection === null)
        ) {
            throw new InvalidArgumentException(
                'Treasury transfer provenance must contain both a Character and direction.'
            );
        }

        if (
            $transferDirection !== null
            && ! in_array(
                $transferDirection,
                [
                    PartyCoinTransferDirection::TO_TREASURY,
                    PartyCoinTransferDirection::TO_CHARACTER,
                ],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'The Treasury transfer direction is invalid.'
            );
        }

        if (
            $transferId !== null
            && (
                trim($transferId) === ''
                || mb_strlen($transferId) > 64
            )
        ) {
            throw new InvalidArgumentException(
                'The Treasury transfer identifier is invalid.'
            );
        }

        if (
            $transferId !== null
            && ! $this->isCharacterTransfer()
        ) {
            throw new InvalidArgumentException(
                'A Treasury transfer identifier requires Character transfer provenance.'
            );
        }
    }

    public static function record(
        string $type,
        PartyTreasuryMoney $amount,
        string $note,
        ?DateTimeImmutable $occurredAt = null,
        ?string $characterId = null,
        ?string $transferDirection = null,
        ?string $transferId = null
    ): self {
        return new self(
            PartyTreasuryTransactionId::generate(),
            $type,
            $amount,
            trim($note),
            $occurredAt ?? new DateTimeImmutable('now'),
            $characterId,
            $transferDirection,
            $transferId
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
            ),
            isset($data['character_id'])
                ? (string) $data['character_id']
                : null,
            isset($data['transfer_direction'])
                ? (string) $data['transfer_direction']
                : null,
            isset($data['transfer_id'])
                ? (string) $data['transfer_id']
                : null
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

    public function characterId(): ?string
    {
        return $this->characterId;
    }

    public function transferDirection(): ?string
    {
        return $this->transferDirection;
    }

    public function isCharacterTransfer(): bool
    {
        return $this->characterId !== null
            && $this->transferDirection !== null;
    }

    public function transferId(): ?string
    {
        return $this->transferId;
    }

    public function isDeposit(): bool
    {
        return $this->type === self::DEPOSIT;
    }

    /**
     * @return array{
     *   id:string,type:string,amount_copper:int,note:string,occurred_at:string,
     *   character_id:?string,transfer_direction:?string,transfer_id:?string
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
            'character_id' => $this->characterId,
            'transfer_direction' => $this->transferDirection,
            'transfer_id' => $this->transferId,
        ];
    }
}

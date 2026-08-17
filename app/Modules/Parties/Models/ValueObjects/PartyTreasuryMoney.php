<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Fellowship funds stored canonically as copper pieces.
 */
final class PartyTreasuryMoney
{
    private function __construct(
        private readonly int $copper
    ) {
        if ($copper < 0) {
            throw new InvalidArgumentException(
                'Fellowship funds cannot be negative.'
            );
        }
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromCopper(int $copper): self
    {
        return new self($copper);
    }

    public static function fromCoins(
        int $gold,
        int $silver,
        int $copper
    ): self {
        if ($gold < 0 || $silver < 0 || $copper < 0) {
            throw new InvalidArgumentException(
                'Coin amounts cannot be negative.'
            );
        }

        return new self(
            ($gold * 100)
            + ($silver * 10)
            + $copper
        );
    }

    public function copper(): int
    {
        return $this->copper;
    }

    public function isZero(): bool
    {
        return $this->copper === 0;
    }

    public function plus(self $amount): self
    {
        return new self(
            $this->copper + $amount->copper
        );
    }

    public function minus(self $amount): self
    {
        if ($amount->copper > $this->copper) {
            throw new InvalidArgumentException(
                'The Fellowship Treasury does not contain enough funds.'
            );
        }

        return new self(
            $this->copper - $amount->copper
        );
    }

    /**
     * @return array{gold:int,silver:int,copper:int}
     */
    public function coins(): array
    {
        $remaining = $this->copper;
        $gold = intdiv($remaining, 100);
        $remaining %= 100;
        $silver = intdiv($remaining, 10);

        return [
            'gold' => $gold,
            'silver' => $silver,
            'copper' => $remaining % 10,
        ];
    }

    public function formatted(): string
    {
        $coins = $this->coins();

        return sprintf(
            '%d gp · %d sp · %d cp',
            $coins['gold'],
            $coins['silver'],
            $coins['copper']
        );
    }
}

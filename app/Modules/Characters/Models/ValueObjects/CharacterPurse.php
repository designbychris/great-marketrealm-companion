<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Personal coin purse carried by an adventurer.
 *
 * Stored canonically as copper pieces so later Character ↔ Fellowship
 * transfers can operate on one exact monetary unit.
 */
final class CharacterPurse
{
    private function __construct(
        private readonly int $copper
    ) {
        if ($copper < 0) {
            throw new InvalidArgumentException(
                'An adventurer’s purse cannot contain negative funds.'
            );
        }
    }

    public static function empty(): self
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

    public function isEmpty(): bool
    {
        return $this->copper === 0;
    }

    public function deposit(self $amount): self
    {
        if ($amount->isEmpty()) {
            throw new InvalidArgumentException(
                'A purse deposit must contain at least one copper piece.'
            );
        }

        return new self(
            $this->copper + $amount->copper
        );
    }

    public function withdraw(self $amount): self
    {
        if ($amount->isEmpty()) {
            throw new InvalidArgumentException(
                'A purse withdrawal must contain at least one copper piece.'
            );
        }

        if ($amount->copper > $this->copper) {
            throw new InvalidArgumentException(
                'The adventurer does not have enough coin in their purse.'
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

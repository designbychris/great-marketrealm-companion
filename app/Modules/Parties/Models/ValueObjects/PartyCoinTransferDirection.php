<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Direction of coin moving across the Character ↔ Fellowship bridge.
 */
final class PartyCoinTransferDirection implements Stringable
{
    public const TO_TREASURY = 'to-treasury';
    public const TO_CHARACTER = 'to-character';

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array(
            $value,
            [self::TO_TREASURY, self::TO_CHARACTER],
            true
        )) {
            throw new InvalidArgumentException(
                'The supplied Fellowship coin transfer direction is invalid.'
            );
        }
    }

    public static function toTreasury(): self
    {
        return new self(self::TO_TREASURY);
    }

    public static function toCharacter(): self
    {
        return new self(self::TO_CHARACTER);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isToTreasury(): bool
    {
        return $this->value === self::TO_TREASURY;
    }

    public function label(): string
    {
        return $this->isToTreasury()
            ? 'Adventurer → Fellowship'
            : 'Fellowship → Adventurer';
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

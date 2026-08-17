<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Account identity responsible for administering a Party.
 *
 * This is deliberately separate from Character ownership.
 */
final class PartyOwnerId
{
    private function __construct(
        private readonly int $value
    ) {
        if ($value < 1) {
            throw new InvalidArgumentException(
                'A Party owner identifier must be a positive integer.'
            );
        }
    }

    public static function fromInt(
        int $value
    ): self {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }
}

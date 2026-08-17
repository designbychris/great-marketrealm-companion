<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable Party identifier.
 */
final class PartyId implements Stringable
{
    private function __construct(
        private readonly string $value
    ) {
        if (! Ulid::isValid($value)) {
            throw new InvalidArgumentException(
                'The supplied Party identifier is not a valid ULID.'
            );
        }
    }

    public static function generate(): self
    {
        return new self(
            Ulid::generate()
        );
    }

    public static function fromString(
        string $value
    ): self {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

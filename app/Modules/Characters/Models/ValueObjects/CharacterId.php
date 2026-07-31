<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character identifier.
 *
 * Every character within the Great Marketrealm is assigned
 * a unique ULID that never changes.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterId implements Stringable
{
    /**
     * Create a character identifier.
     *
     * @throws InvalidArgumentException
     */
    private function __construct(
        private readonly string $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Generate a new character identifier.
     */
    public static function generate(): self
    {
        return new self(
            Ulid::generate()
        );
    }

    /**
     * Create an identifier from an existing ULID.
     *
     * @throws InvalidArgumentException
     */
    public static function fromString(
        string $value
    ): self {
        return new self($value);
    }

    /**
     * Return the identifier value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Determine whether two identifiers are equal.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Convert the identifier to a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate the identifier.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if (! Ulid::isValid($value)) {
            throw new InvalidArgumentException(
                'The supplied character identifier is not a valid ULID.'
            );
        }
    }
}

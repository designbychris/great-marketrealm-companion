<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable character name value object.
 *
 * Ensures that every character name used throughout the
 * application is valid and consistently formatted.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class CharacterName implements Stringable
{
    /**
     * Minimum permitted character-name length.
     */
    private const MIN_LENGTH = 2;

    /**
     * Maximum permitted character-name length.
     */
    private const MAX_LENGTH = 80;

    /**
     * Create a character name.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly string $value
    ) {
        $this->guardAgainstInvalidValue($value);
    }

    /**
     * Create a character name from a string.
     *
     * @throws InvalidArgumentException
     */
    public static function fromString(
        string $value
    ): self {
        return new self($value);
    }

    /**
     * Return the character name as a string.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Determine whether this name matches another.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Return the character name when treated as a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Validate the supplied character name.
     *
     * @throws InvalidArgumentException
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidArgumentException(
                'A character name cannot be empty.'
            );
        }

        if ($trimmed !== $value) {
            throw new InvalidArgumentException(
                'A character name cannot begin or end with whitespace.'
            );
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'A character name must contain at least %d characters.',
                    self::MIN_LENGTH
                )
            );
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'A character name cannot contain more than %d characters.',
                    self::MAX_LENGTH
                )
            );
        }

        if (preg_match('/[\x00-\x1F\x7F]/u', $value) === 1) {
            throw new InvalidArgumentException(
                'A character name cannot contain control characters.'
            );
        }
    }
}

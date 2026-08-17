<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Immutable Fellowship name.
 */
final class PartyName implements Stringable
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 80;

    private function __construct(
        private readonly string $value
    ) {
        $this->guard($value);
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

    private function guard(
        string $value
    ): void {
        if (trim($value) !== $value) {
            throw new InvalidArgumentException(
                'A Party name cannot begin or end with whitespace.'
            );
        }

        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'A Party name must contain at least %d characters.',
                    self::MIN_LENGTH
                )
            );
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'A Party name cannot contain more than %d characters.',
                    self::MAX_LENGTH
                )
            );
        }

        if (preg_match('/[\x00-\x1F\x7F]/u', $value) === 1) {
            throw new InvalidArgumentException(
                'A Party name cannot contain control characters.'
            );
        }
    }
}

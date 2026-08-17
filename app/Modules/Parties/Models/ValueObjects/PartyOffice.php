<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Company office held by an adventurer inside a Fellowship.
 *
 * Offices are deliberately separate from Leader/Member membership roles.
 */
final class PartyOffice implements Stringable
{
    public const NONE = 'none';
    public const QUARTERMASTER = 'quartermaster';
    public const CHRONICLER = 'chronicler';
    public const PATHFINDER = 'pathfinder';
    public const STANDARD_BEARER = 'standard-bearer';

    private const SUPPORTED = [
        self::NONE,
        self::QUARTERMASTER,
        self::CHRONICLER,
        self::PATHFINDER,
        self::STANDARD_BEARER,
    ];

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array($value, self::SUPPORTED, true)) {
            throw new InvalidArgumentException(
                'The supplied Fellowship office is not supported.'
            );
        }
    }

    public static function none(): self
    {
        return new self(self::NONE);
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

    public function isAssigned(): bool
    {
        return $this->value !== self::NONE;
    }

    public function label(): string
    {
        return match ($this->value) {
            self::QUARTERMASTER => 'Quartermaster',
            self::CHRONICLER => 'Chronicler',
            self::PATHFINDER => 'Pathfinder',
            self::STANDARD_BEARER => 'Standard Bearer',
            default => 'No Company Office',
        };
    }

    public function glyph(): string
    {
        return match ($this->value) {
            self::QUARTERMASTER => '⚖',
            self::CHRONICLER => '✎',
            self::PATHFINDER => '✧',
            self::STANDARD_BEARER => '⚑',
            default => '',
        };
    }

    /**
     * @return string[]
     */
    public static function supported(): array
    {
        return self::SUPPORTED;
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

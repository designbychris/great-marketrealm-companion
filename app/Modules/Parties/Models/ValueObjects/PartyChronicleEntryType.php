<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

final class PartyChronicleEntryType implements Stringable
{
    public const NOTE = 'adventure-note';
    public const DEED = 'company-deed';
    public const HONOUR = 'fellowship-honour';

    private const SUPPORTED = [
        self::NOTE,
        self::DEED,
        self::HONOUR,
    ];

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array($value, self::SUPPORTED, true)) {
            throw new InvalidArgumentException(
                'The supplied Chronicle entry type is invalid.'
            );
        }
    }

    public static function note(): self
    {
        return new self(self::NOTE);
    }

    public static function deed(): self
    {
        return new self(self::DEED);
    }

    public static function honour(): self
    {
        return new self(self::HONOUR);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this->value) {
            self::DEED => 'Company Deed',
            self::HONOUR => 'Fellowship Honour',
            default => 'Adventure Note',
        };
    }

    public function glyph(): string
    {
        return match ($this->value) {
            self::DEED => '⚔',
            self::HONOUR => '✦',
            default => '✎',
        };
    }

    public function isNote(): bool
    {
        return $this->value === self::NOTE;
    }

    public function requiresCertification(): bool
    {
        return in_array(
            $this->value,
            [self::DEED, self::HONOUR],
            true
        );
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

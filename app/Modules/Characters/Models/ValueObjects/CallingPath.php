<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

final class CallingPath
{
    private function __construct(
        private string $value
    ) {
    }

    public static function none(): self
    {
        return new self('');
    }

    public static function fromString(
        string $value
    ): self {
        $value = sanitize_key($value);

        if (strlen($value) > 150) {
            throw new InvalidArgumentException(
                'A Calling Path key cannot exceed 150 characters.'
            );
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isChosen(): bool
    {
        return $this->value !== '';
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

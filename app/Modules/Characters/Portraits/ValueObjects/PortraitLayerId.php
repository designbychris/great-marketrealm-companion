<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Portrait Layer Identifier.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitLayerId implements Stringable
{
    private function __construct(
        private readonly string $value
    ) {
        if (
            $value === ''
            || ! preg_match(
                '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $value
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid portrait layer identifier [%s].',
                    $value
                )
            );
        }
    }

    public static function fromString(
        string $value
    ): self {
        $value = strtolower(
            trim($value)
        );

        $value = preg_replace(
            '/[\s_]+/',
            '-',
            $value
        );

        return new self(
            is_string($value)
                ? trim($value, '-')
                : ''
        );
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(
        self $other
    ): bool {
        return $this->value
            === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

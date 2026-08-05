<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * WordPress Portrait Attachment Identifier.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitAttachmentId implements Stringable
{
    private function __construct(
        private readonly int $value
    ) {
        if ($value < 1) {
            throw new InvalidArgumentException(
                'A portrait attachment identifier must be positive.'
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
        return $this->value
            === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}

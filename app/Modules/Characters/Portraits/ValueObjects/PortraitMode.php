<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Portrait Mode Value Object.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitMode implements Stringable
{
    public const GENERATED = 'generated';
    public const CUSTOM = 'custom';
    public const NONE = 'none';

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array(
            $value,
            self::values(),
            true
        )) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported portrait mode [%s].',
                    $value
                )
            );
        }
    }

    public static function generated(): self
    {
        return new self(
            self::GENERATED
        );
    }

    public static function custom(): self
    {
        return new self(
            self::CUSTOM
        );
    }

    public static function none(): self
    {
        return new self(
            self::NONE
        );
    }

    public static function fromString(
        string $value
    ): self {
        return new self(
            strtolower(
                trim($value)
            )
        );
    }

    /**
     * @return array<int,string>
     */
    public static function values(): array
    {
        return [
            self::GENERATED,
            self::CUSTOM,
            self::NONE,
        ];
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isGenerated(): bool
    {
        return $this->value
            === self::GENERATED;
    }

    public function isCustom(): bool
    {
        return $this->value
            === self::CUSTOM;
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

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Portrait Seed Value Object.
 *
 * Provides a stable seed from which every generated
 * portrait layer can be reproduced.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitSeed implements Stringable
{
    private function __construct(
        private readonly string $value
    ) {
        if (! preg_match(
            '/^[a-f0-9]{16}$/',
            $value
        )) {
            throw new InvalidArgumentException(
                'A portrait seed must contain 16 hexadecimal characters.'
            );
        }
    }

    public static function fromCharacterId(
        CharacterId $characterId
    ): self {
        return new self(
            substr(
                hash(
                    'sha256',
                    'gmrc-portrait|'
                        . $characterId->value()
                ),
                0,
                16
            )
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

    public function value(): string
    {
        return $this->value;
    }

    public function numberFor(
        string $slot
    ): int {
        $hash = hash(
            'sha256',
            $this->value
                . '|'
                . trim($slot)
        );

        return (int) hexdec(
            substr(
                $hash,
                0,
                7
            )
        );
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

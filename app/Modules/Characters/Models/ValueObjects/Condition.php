<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Condition Value Object.
 *
 * Represents one canonical Character condition.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.8.0
 */
final class Condition implements Stringable
{
    /**
     * Supported Character conditions.
     *
     * @var array<string,string>
     */
    private const CONDITIONS = [
        'blinded' => 'Blinded',
        'charmed' => 'Charmed',
        'deafened' => 'Deafened',
        'frightened' => 'Frightened',
        'grappled' => 'Grappled',
        'incapacitated' => 'Incapacitated',
        'invisible' => 'Invisible',
        'paralyzed' => 'Paralyzed',
        'petrified' => 'Petrified',
        'poisoned' => 'Poisoned',
        'prone' => 'Prone',
        'restrained' => 'Restrained',
        'stunned' => 'Stunned',
        'unconscious' => 'Unconscious',
    ];

    /**
     * Create a Condition.
     */
    private function __construct(
        private readonly string $value
    ) {
        $this->guardAgainstInvalidValue(
            $value
        );
    }

    /**
     * Create a Condition from a string.
     */
    public static function fromString(
        string $value
    ): self {
        return new self(
            self::normalise($value)
        );
    }

    /**
     * Return the canonical condition identifier.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Return the display label.
     */
    public function label(): string
    {
        return self::CONDITIONS[
            $this->value
        ];
    }

    /**
     * Determine whether this Condition equals another.
     */
    public function equals(
        self $other
    ): bool {
        return $this->value === $other->value;
    }

    /**
     * Determine whether a condition identifier is supported.
     */
    public static function supports(
        string $value
    ): bool {
        return array_key_exists(
            self::normalise($value),
            self::CONDITIONS
        );
    }

    /**
     * Return every supported Condition.
     *
     * @return array<int,self>
     */
    public static function all(): array
    {
        return array_map(
            static fn (
                string $condition
            ): self => new self($condition),
            array_keys(self::CONDITIONS)
        );
    }

    /**
     * Convert the Condition to its canonical identifier.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Normalise condition input.
     */
    private static function normalise(
        string $value
    ): string {
        $value = strtolower(
            trim($value)
        );

        $value = preg_replace(
            '/[\s_]+/',
            '-',
            $value
        );

        return is_string($value)
            ? trim($value, '-')
            : '';
    }

    /**
     * Guard against an unsupported condition.
     */
    private function guardAgainstInvalidValue(
        string $value
    ): void {
        if ($value === '') {
            throw new InvalidArgumentException(
                'A Character condition cannot be empty.'
            );
        }

        if (! array_key_exists(
            $value,
            self::CONDITIONS
        )) {
            throw new InvalidArgumentException(
                sprintf(
                    'The Character condition "%s" is not supported.',
                    $value
                )
            );
        }
    }
}

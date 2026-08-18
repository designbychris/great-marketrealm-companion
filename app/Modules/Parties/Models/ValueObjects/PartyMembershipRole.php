<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

/**
 * Character-facing role inside a Fellowship.
 *
 * Party administration/ownership remains a separate concern.
 */
final class PartyMembershipRole implements Stringable
{
    public const LEADER = 'leader';
    public const MEMBER = 'member';

    private const SUPPORTED = [
        self::LEADER,
        self::MEMBER,
    ];

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array($value, self::SUPPORTED, true)) {
            throw new InvalidArgumentException(
                'The supplied Party membership role is not supported.'
            );
        }
    }

    public static function leader(): self
    {
        return new self(self::LEADER);
    }

    public static function member(): self
    {
        return new self(self::MEMBER);
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

    public function isLeader(): bool
    {
        return $this->value === self::LEADER;
    }

    public function label(): string
    {
        return $this->isLeader()
            ? 'Leader'
            : 'Member';
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

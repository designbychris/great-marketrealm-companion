<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;
use Stringable;

defined('ABSPATH') || exit;

final class PartyChronicleProvenance implements Stringable
{
    public const PLAYER = 'player';
    public const DUNGEON_MASTER = 'dungeon-master';

    private function __construct(
        private readonly string $value
    ) {
        if (! in_array(
            $value,
            [self::PLAYER, self::DUNGEON_MASTER],
            true
        )) {
            throw new InvalidArgumentException(
                'The supplied Chronicle provenance is invalid.'
            );
        }
    }

    public static function player(): self
    {
        return new self(self::PLAYER);
    }

    public static function dungeonMaster(): self
    {
        return new self(self::DUNGEON_MASTER);
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
        return $this->value === self::DUNGEON_MASTER
            ? 'Dungeon Master'
            : 'Fellowship record';
    }

    public function isDungeonMaster(): bool
    {
        return $this->value === self::DUNGEON_MASTER;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Models;

defined('ABSPATH') || exit;

/**
 * Stable target categories shared by Diceworks and future combat services.
 */
final class RollTargetKind
{
    public const SELF = 'self';
    public const ALLY = 'ally';
    public const PLAYER_CHARACTER = 'player-character';
    public const NPC = 'npc';
    public const HOSTILE_CREATURE = 'hostile-creature';

    /** @return array<string,string> */
    public static function labels(): array
    {
        return [
            self::SELF => 'Self',
            self::ALLY => 'Ally',
            self::PLAYER_CHARACTER => 'Player Character',
            self::NPC => 'NPC',
            self::HOSTILE_CREATURE => 'Hostile Creature',
        ];
    }

    public static function valid(string $kind): bool
    {
        return array_key_exists(
            $kind,
            self::labels()
        );
    }

    public static function label(string $kind): string
    {
        return self::labels()[$kind]
            ?? 'Unknown Target';
    }
}

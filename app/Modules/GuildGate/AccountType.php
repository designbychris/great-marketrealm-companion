<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate;

defined('ABSPATH') || exit;

final class AccountType
{
    public const PLAYER = 'player';
    public const DM = 'dm';

    /** @return array<int,string> */
    public static function values(): array
    {
        return [self::PLAYER, self::DM];
    }

    public static function role(string $type): string
    {
        return $type === self::DM
            ? 'gmrc_dm'
            : 'gmrc_player';
    }

    public static function label(string $type): string
    {
        return $type === self::DM
            ? 'Dungeon Master'
            : 'Player';
    }
}

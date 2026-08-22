<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

defined('ABSPATH') || exit;

final class GuildRoleRegistrar
{
    public const ACCESS = 'gmrc_access_companion';
    public const MANAGE_CAMPAIGNS = 'gmrc_manage_campaigns';

    public function register(): void
    {
        if (! function_exists('add_role') || ! function_exists('get_role')) {
            return;
        }

        add_role('gmrc_player', 'Marketrealm Player', [
            'read' => true,
            self::ACCESS => true,
        ]);

        add_role('gmrc_dm', 'Marketrealm Dungeon Master', [
            'read' => true,
            self::ACCESS => true,
            self::MANAGE_CAMPAIGNS => true,
        ]);

        $administrator = get_role('administrator');

        if ($administrator !== null) {
            $administrator->add_cap(self::ACCESS);
            $administrator->add_cap(self::MANAGE_CAMPAIGNS);
        }
    }
}

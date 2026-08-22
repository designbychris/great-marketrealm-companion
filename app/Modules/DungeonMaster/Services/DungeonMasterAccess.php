<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;

defined('ABSPATH') || exit;

/**
 * Dungeon Master access policy.
 *
 * Keeps the DM capability boundary in one place so later campaign,
 * session, encounter, and roster tools share the same server-side rule.
 */
final class DungeonMasterAccess
{
    public function allows(): bool
    {
        if (! function_exists('current_user_can')) {
            return false;
        }

        return current_user_can(GuildRoleRegistrar::MANAGE_CAMPAIGNS)
            || current_user_can('manage_options');
    }
}

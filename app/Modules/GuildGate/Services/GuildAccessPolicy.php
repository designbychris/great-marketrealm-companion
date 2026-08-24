<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

defined('ABSPATH') || exit;

/**
 * Canonical admission policy for the Companion application.
 *
 * A WordPress session alone is not a Guild membership. The signed-in account
 * must also hold the Companion access capability assigned to registered
 * Players, Dungeon Masters, and administrators.
 */
final class GuildAccessPolicy
{
    public function allows(int $userId): bool
    {
        return $userId > 0
            && user_can($userId, GuildRoleRegistrar::ACCESS);
    }

    public function allowsCurrentUser(): bool
    {
        return is_user_logged_in()
            && $this->allows(get_current_user_id());
    }
}

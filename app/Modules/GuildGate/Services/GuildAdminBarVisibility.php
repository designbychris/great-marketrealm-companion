<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

defined('ABSPATH') || exit;

final class GuildAdminBarVisibility
{
    public function filter(bool $show): bool
    {
        if (! function_exists('current_user_can')) {
            return $show;
        }

        return current_user_can('manage_options')
            ? $show
            : false;
    }
}

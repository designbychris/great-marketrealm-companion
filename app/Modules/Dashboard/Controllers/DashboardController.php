<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Dashboard\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Dashboard\Services\GuildHallDirectory;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;
use GreatMarketrealmCompanion\Services\Codex\Codex;

defined('ABSPATH') || exit;

final class DashboardController
{
    public function __construct(
        private ViewFactory $views,
        private Codex $codex,
        private GuildHallDirectory $directory
    ) {
    }

    public function index(): string
    {
        $userId = get_current_user_id();

        return $this->views->render(View::make('dashboard.index', [
            'races' => $this->codex->races(),
            'rooms' => $this->directory->forAccount(
                GuildProfile::accountType($userId),
                $userId > 0 && user_can($userId, GuildRoleRegistrar::MANAGE_CAMPAIGNS)
            ),
        ]));
    }
}

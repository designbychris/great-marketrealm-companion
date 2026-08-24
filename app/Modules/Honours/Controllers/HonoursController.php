<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Honours\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;
use GreatMarketrealmCompanion\Modules\Honours\Services\BookOfDeeds;

defined('ABSPATH') || exit;

final class HonoursController
{
    public function __construct(
        private ViewFactory $views,
        private BookOfDeeds $book
    ) {
    }

    public function index(): string
    {
        $accountId = get_current_user_id();

        return $this->views->render(View::make('honours.index', [
            'book' => $this->book->forAccount($accountId, GuildProfile::accountType($accountId)),
        ]));
    }
}

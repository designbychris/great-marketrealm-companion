<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Controllers;

use GreatMarketrealmCompanion\Http\Page;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;

defined('ABSPATH') || exit;

/**
 * Character Controller.
 *
 * Handles requests for the Characters Kingdom.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class CharacterController
{
    public function __construct(
        protected CharacterRepository $repository
    ) {
    }

    public function index(): string
    {
        return Page::make('characters.index')
            ->title(__('Characters', 'great-marketrealm-companion'))
            ->layout('app')
            ->with([
                'characters' => $this->repository->all(),
            ])
            ->render();
    }
}

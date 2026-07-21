<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
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
    protected CharacterRepository $characters;

    protected ViewFactory $views;

    public function __construct(
        CharacterRepository $characters,
        ViewFactory $views
    ) {
        $this->characters = $characters;
        $this->views = $views;
    }

    public function index(): string
    {
        return $this->views->render(
            View::make(
                'characters.index',
                [
                    'characters' => $this->characters->all(),
                ]
            )
        );
    }
}

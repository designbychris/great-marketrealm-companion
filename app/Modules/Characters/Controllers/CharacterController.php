<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
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
        protected CharacterRepository $characters,
        protected ViewFactory $views,
        protected CreateCharacterAction $createCharacter
    ) {
    }

    /**
     * Display all Characters.
     */
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

    /**
     * Store a new Character.
     */
    public function store(): Character
    {
        $character = new Character(
            name: $this->postString('name'),
            race: $this->postString('race'),
            class: $this->postString('class'),
            level: $this->postInteger('level', 1),
        );

        return $this->createCharacter->handle(
            $character
        );
    }

    /**
     * Retrieve and sanitise a string from the request.
     */
    protected function postString(
        string $key,
        string $default = ''
    ): string {
        if (! isset($_POST[$key])) {
            return $default;
        }

        return sanitize_text_field(
            wp_unslash($_POST[$key])
        );
    }

    /**
     * Retrieve an integer from the request.
     */
    protected function postInteger(
        string $key,
        int $default = 0
    ): int {
        if (! isset($_POST[$key])) {
            return $default;
        }

        return absint(
            wp_unslash($_POST[$key])
        );
    }
}

<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\RedirectResponse;

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
        protected CreateCharacterAction $createCharacter,
        protected UpdateCharacterAction $updateCharacter,
        protected DeleteCharacterAction $deleteCharacter,
        protected Request $request
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
    public function store(): RedirectResponse
    {
        $character = new Character(
            name: $this->request->string('name'),
            race: $this->request->string('race'),
            class: $this->request->string('class'),
            level: $this->request->integer(
                'level',
                1
            )
        );
    
        $this->createCharacter->handle(
            $character
        );
    
        return new RedirectResponse(
            home_url('/characters'),
            303
        );
    }

    /**
     * Update an existing Character.
     */
    public function update(
        string $id
    ): Character {
        $character = new Character(
            id: absint($id),
            name: $this->request->string('name'),
            race: $this->request->string('race'),
            class: $this->request->string('class'),
            level: $this->request->integer(
                'level',
                1
            ),
        );
    
        return $this->updateCharacter->handle(
            $character
        );
    }
    
    /**
     * Delete an existing Character.
     */
    public function destroy(string $id): bool
    {
        return $this->deleteCharacter->handle(
            absint($id)
        );
    }

}

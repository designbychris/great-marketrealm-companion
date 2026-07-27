<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequest;
use GreatMarketrealmCompanion\Services\Auby\Auby;
use GreatMarketrealmCompanion\Services\Auby\QuoteCategories;

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
        protected Request $request,
        protected ResponseFactory $responses,
        protected FlashStore $flash
        protected Auby $auby
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
    
                    'aubyQuote' => $this->auby->for(
                        QuoteCategories::REGISTER
                    ),
                ]
            )
        );
    }

    /**
     * Display the Character creation form.
     */
    public function create(): string
    {
        return $this->views->render(
            View::make(
                'characters.create'
            )
        );
    }

    /**
     * Store a new Character.
     */
    public function store(
        StoreCharacterRequest $request
    ): RedirectResponse {
        
        error_log('CharacterController::store() reached');
        
        $character = $this->createCharacter->handle(
            $request->toCharacter()
        );
    
        $this->flash->success(
            'Your character has entered the Marketrealm!'
        );
    
        return $this->responses->redirect(
            add_query_arg(
                'gmrc_route',
                'characters',
                home_url('/companion/')
            )
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

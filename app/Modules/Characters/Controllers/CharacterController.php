<?php

declare(strict_types=1);

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
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequest;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use GreatMarketrealmCompanion\Services\Auby\Auby;
use GreatMarketrealmCompanion\Services\Auby\QuoteCategories;
use GreatMarketrealmCompanion\Services\Characters\ClassRegistry;
use GreatMarketrealmCompanion\Services\Characters\RaceRegistry;
use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Character Controller.
 *
 * Handles HTTP requests for the Characters Kingdom.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
final class CharacterController
{
    /**
     * Create the Character controller.
     */
    public function __construct(
        private CharacterRepositoryInterface $characters,
        private ViewFactory $views,
        private CharacterFactory $characterFactory,
        private CreateCharacterAction $createCharacter,
        private UpdateCharacterAction $updateCharacter,
        private DeleteCharacterAction $deleteCharacter,
        private Request $request,
        private ResponseFactory $responses,
        private FlashStore $flash,
        private Auby $auby,
        private GuildSealRegistry $sealRegistry,
        private RaceRegistry $raceRegistry,
        private ClassRegistry $classRegistry
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
                    'sealRegistry' => $this->sealRegistry,
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
                'characters.create',
                [
                    'old' => [],
                    'errors' => [],
                    'flash' => [],
                    'raceOptions' => $this
                        ->raceRegistry
                        ->options(),
                    'classOptions' => $this
                        ->classRegistry
                        ->options(),
                ]
            )
        );
    }

    /**
     * Store a new Character.
     */
    public function store(
        StoreCharacterRequest $request
    ): RedirectResponse {
        $data = $request->characterData();

        $character = $this->characterFactory->fromInput(
            name: $data['name'],
            race: $data['race'],
            characterClass: $data['class']
        );

        $this->createCharacter->handle(
            $character
        );

        $this->flash->success(
            'Your character has entered the Marketrealm!'
        );

        return $this->responses->redirect(
            $this->charactersUrl()
        );
    }

    /**
     * Update an existing Character.
     */
    public function update(
        string $id
    ): Character {
        $characterId = CharacterId::fromString(
            $id
        );

        $character = $this->characters->find(
            $characterId
        );

        if (! $character instanceof Character) {
            throw new RuntimeException(
                'The requested character could not be found.'
            );
        }

        $name = $this->request->string(
            'name'
        );

        if ($name !== '') {
            $character->rename(
                CharacterName::fromString($name)
            );
        }

        return $this->updateCharacter->handle(
            $character
        );
    }

    /**
     * Delete an existing Character.
     */
    public function destroy(
        string $id
    ): bool {
        $this->deleteCharacter->handle(
            CharacterId::fromString($id)
        );

        return true;
    }

    /**
     * Build the Character index URL.
     */
    private function charactersUrl(): string
    {
        return add_query_arg(
            'gmrc_route',
            'characters',
            home_url('/companion/')
        );
    }
}

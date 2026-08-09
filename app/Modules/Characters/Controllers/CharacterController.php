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
use GreatMarketrealmCompanion\Modules\Characters\Requests\UpdateCharacterRequest;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\SubmittedPortraitRecipeFactory;
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
        private ClassRegistry $classRegistry,
        private PortraitRenderer $portraitRenderer,
        private SubmittedPortraitRecipeFactory $submittedPortraits
    ) {
    }

    /**
     * Display all Characters.
     */
    public function index(): string
    {
        $characters = $this->characters->all();
    
        return $this->views->render(
            View::make(
                'characters.index',
                [
                    'characters' => $characters,
                    'portraits' => $this
                        ->portraitRenderer
                        ->forCharacters(
                            $characters
                        ),
                    'aubyQuote' => $this->auby->for(
                        QuoteCategories::REGISTER
                    ),
                    'sealRegistry' => $this->sealRegistry,
                    'flash' => [
                        'success' => $this->flash->success(),
                        'error' => $this->flash->error(),
                    ],
                ]
            )
        );
    }

    /**
     * Display the Character creation form.
     */
    public function create(): string
    {
        $old = [];
    
        $name = '';
        $race = '';
        $characterClass = '';
    
        return $this->views->render(
            View::make(
                'characters.create',
                [
                    'old' => $old,
                    'errors' => [],
                    'flash' => [],
                    'raceOptions' => $this
                        ->raceRegistry
                        ->options(),
                    'classOptions' => $this
                        ->classRegistry
                        ->options(),
    
                    /*
                     * The provisional portrait uses the same rendering
                     * pipeline as persisted Character portraits.
                     */
                    'portrait' => $this
                        ->portraitRenderer
                        ->preview(
                            $name,
                            $race,
                            $characterClass
                        ),
    
                    /*
                     * Auby guidance used by the Living Desk.
                     */
                    'aubyNotes' => [
                        'start' => $this->auby->many(
                            QuoteCategories::CHARACTER_CREATOR
                        ),
                        'name' => $this->auby->many(
                            QuoteCategories::CHARACTER_NAME
                        ),
                        'race' => $this->auby->many(
                            QuoteCategories::CHARACTER_RACE
                        ),
                        'class' => $this->auby->many(
                            QuoteCategories::CHARACTER_CLASS
                        ),
                        'ready' => $this->auby->many(
                            QuoteCategories::CHARACTER_READY
                        ),
                    ],
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
        $registration = $request->registrationData();

        $abilities = $registration['abilities'];

        $character = $this->characterFactory->fromInput(
            name: $data['name'],
            race: $data['race'],
            characterClass: $data['class'],
            abilityScores: AbilityScores::fromScores(
                strength: AbilityScore::fromInt(
                    $abilities['strength']
                ),
                dexterity: AbilityScore::fromInt(
                    $abilities['dexterity']
                ),
                constitution: AbilityScore::fromInt(
                    $abilities['constitution']
                ),
                intelligence: AbilityScore::fromInt(
                    $abilities['intelligence']
                ),
                wisdom: AbilityScore::fromInt(
                    $abilities['wisdom']
                ),
                charisma: AbilityScore::fromInt(
                    $abilities['charisma']
                )
            ),
            background: Background::fromString(
                $registration['background']
            ),
            selectedLanguages: Languages::fromStrings(
                $registration['languages']
            ),
            selectedToolProficiencies:
                ToolProficiencies::fromStrings(
                    $registration['tools']
                )
        );

        $portraitRecipe = $this
            ->submittedPortraits
            ->create(
                $request->portraitData(),
                $data['race'],
                $data['class']
            );
    
        $this->createCharacter->handle(
            $character,
            $portraitRecipe
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
        string $id,
        UpdateCharacterRequest $request
    ): RedirectResponse {
        $character = $this->findCharacter(
            $id
        );
    
        $data = $request->characterData();
    
        $character->rename(
            CharacterName::fromString(
                $data['name']
            )
        );
    
        $background = Background::fromString(
            $data['background']
        );

        $registration = $request
            ->registrationChoicesFor(
                $background
            );

        if ($registration['confirmed']) {
            $character->completeRegistration(
                $background,
                Languages::fromStrings(
                    $registration['languages']
                ),
                ToolProficiencies::fromStrings(
                    $registration['tools']
                )
            );
        } else {
            $character->changeBackground(
                $background
            );
        }

        $this->updateCharacter->handle(
            $character
        );

        $this->flash->success(
            'The adventurer’s register has been updated.'
        );
    
        return $this->responses->redirect(
            $this->characterUrl(
                $character->id()
            )
        );
    }

    /**
     * Display the Final Farewell confirmation.
     */
    public function confirmDelete(
        string $id
    ): string {
        $character = $this->findCharacter($id);

        return $this->views->render(
            View::make(
                'characters.delete',
                [
                    'character' => $character,
                    'portrait' => $this
                        ->portraitRenderer
                        ->forCharacter(
                            $character
                        ),
                ]
            )
        );
    }

    /**
     * Delete an existing Character.
     */
    public function destroy(
        string $id
    ): RedirectResponse {
        /*
         * Resolve through the user-scoped repository first. This confirms
         * the Character exists and belongs to the current adventurer
         * before any destructive action is attempted.
         */
        $character = $this->findCharacter($id);
        $name = $character->name()->value();

        $this->deleteCharacter->handle(
            $character->id()
        );

        $this->flash->success(
            sprintf(
                '%s has been removed from your Adventurer Register.',
                $name
            )
        );

        return $this->responses->redirect(
            $this->charactersUrl()
        );
    }

    /**
     * Display a Character.
     */
    public function show(
        string $id
    ): string {
        $character = $this->findCharacter($id);
    
        return $this->views->render(
            View::make(
                'characters.show',
                [
                    'character' => $character,
                    'portrait' => $this
                        ->portraitRenderer
                        ->forCharacter(
                            $character
                        ),
                    'sealRegistry' => $this->sealRegistry,
                ]
            )
        );
    }
    
    /**
     * Display the Character editing form.
     */
    public function edit(
        string $id
    ): string {
        $character = $this->findCharacter($id);
    
        return $this->views->render(
            View::make(
                'characters.edit',
                [
                    'character' => $character,
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
     * Find a Character or fail.
     */
    private function findCharacter(
        string $id
    ): Character {
        $character = $this->characters->find(
            CharacterId::fromString($id)
        );
    
        if (! $character instanceof Character) {
            throw new RuntimeException(
                'The requested character could not be found.'
            );
        }
    
        return $character;
    }

    /**
     * Build a Character detail URL.
     */
    private function characterUrl(
        CharacterId $id
    ): string {
        return add_query_arg(
            'gmrc_route',
            'characters/' . rawurlencode(
                $id->value()
            ),
            home_url('/companion/')
        );
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

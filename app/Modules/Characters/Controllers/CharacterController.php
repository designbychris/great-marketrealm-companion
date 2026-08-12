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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Requests\StoreCharacterRequest;
use GreatMarketrealmCompanion\Modules\Characters\Requests\UpdateCharacterRequest;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use GreatMarketrealmCompanion\Modules\Characters\Services\CompleteAdventurerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\CharacterInventoryRepository;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\InventoryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\RisingRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterBuildProfileRepository;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Repositories\CharacterPortraitRepository;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitAttachmentId;
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
        private SubmittedPortraitRecipeFactory $submittedPortraits,
        private CharacterCatalogueRepository $catalogue,
        private CharacterBuildProfileRepository $buildProfiles
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
        $old = $this->flash->old();

        if (! is_array($old)) {
            $old = [];
        }

        $errors = $this->flash->errors();

        $name = isset($old['name'])
            && is_scalar($old['name'])
                ? sanitize_text_field(
                    (string) $old['name']
                )
                : '';

        $race = isset($old['race'])
            && is_scalar($old['race'])
                ? sanitize_key(
                    (string) $old['race']
                )
                : '';

        $characterClass = isset($old['class'])
            && is_scalar($old['class'])
                ? sanitize_key(
                    (string) $old['class']
                )
                : '';

        return $this->views->render(
            View::make(
                'characters.create',
                [
                    'old' => $old,
                    'errors' => $errors ?? [],
                    'flash' => [
                        'error' => $this->flash->error(),
                    ],
                    /*
                     * New Characters are created from the Grand Catalogue.
                     * The legacy registries remain available elsewhere for
                     * previously persisted Character identities.
                     */
                    'raceOptions' => $this->catalogue->raceOptions(),
                    'classOptions' => $this->catalogue->classOptions(),
                    'catalogueRaces' => $this->catalogue->raceOptions(),
                    'catalogueClasses' => $this->catalogue->classOptions(),
                    'catalogueHeritages' => $this->catalogue->heritages(),
                    'catalogueSubclasses' => $this->catalogue->subclasses(),
    
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
        $catalogueData = $request->catalogueData();

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

        if (
            $this->catalogue->heritageBelongsTo($catalogueData['heritage'], $data['race'])
            && $this->catalogue->subclassBelongsTo($catalogueData['subclass'], $data['class'])
        ) {
            $this->buildProfiles->save(
                $character->id(),
                $catalogueData['heritage'],
                $catalogueData['subclass']
            );
        }
    
        $this->flash->success(
            'Your character has entered the Marketrealm!'
        );
    
        return $this->responses->redirect(
            $this->characterUrl(
                $character->id()
            )
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

        $submittedRecipe = $this
            ->submittedPortraits
            ->create(
                $request->portraitData(),
                $character->race()->value(),
                $character
                    ->characterClass()
                    ->value()
            );

        if ($submittedRecipe !== null) {
            $portraitRepository =
                new CharacterPortraitRepository();

            $existingPortrait =
                $portraitRepository->find(
                    $character->id()
                );

            if (
                $existingPortrait
                    instanceof CharacterPortrait
                && $existingPortrait
                    ->mode()
                    ->isCustom()
                && $existingPortrait
                    ->attachmentId()
                    instanceof PortraitAttachmentId
            ) {
                $portraitRepository->save(
                    $character->id(),
                    CharacterPortrait::custom(
                        $existingPortrait
                            ->attachmentId(),
                        $submittedRecipe
                    )
                );
            } else {
                $portraitRepository->save(
                    $character->id(),
                    CharacterPortrait::generated(
                        $submittedRecipe
                    )
                );
            }
        }

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
     * Replace the generated Guild portrait with a user image.
     */
    public function uploadPortrait(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $file = $_FILES['gmrc_custom_portrait'] ?? null;

        if (
            ! is_array($file)
            || ! isset($file['tmp_name'], $file['name'])
            || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_OK
        ) {
            $this->flash->error(
                'Choose a portrait image before asking the Illuminator to frame it.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        $checked = wp_check_filetype_and_ext(
            (string) $file['tmp_name'],
            (string) $file['name']
        );

        if (
            ! isset($checked['type'])
            || ! in_array($checked['type'], $allowed, true)
        ) {
            $this->flash->error(
                'Guild portraits must be JPG, PNG or WebP images.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        if ((int) ($file['size'] ?? 0) > 8 * MB_IN_BYTES) {
            $this->flash->error(
                'That portrait is too large. Please keep it below 8 MB.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachmentId = media_handle_upload(
            'gmrc_custom_portrait',
            0
        );

        if (is_wp_error($attachmentId)) {
            $this->flash->error(
                'The Guild Illuminator could not frame that image. Please try another.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        $portraitRepository = new CharacterPortraitRepository();

        $existing = $portraitRepository->find(
            $character->id()
        );

        $fallback = $existing instanceof CharacterPortrait
            ? $existing->recipe()
            : null;

        $portraitRepository->save(
            $character->id(),
            CharacterPortrait::custom(
                PortraitAttachmentId::fromInt(
                    (int) $attachmentId
                ),
                $fallback
            )
        );

        $this->flash->success(
            'The Guild Illuminator has framed your custom portrait.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id())
        );
    }

    /**
     * Return a custom portrait to its generated Guild illustration.
     */
    public function resetPortrait(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $portraitRepository = new CharacterPortraitRepository();

        $existing = $portraitRepository->find(
            $character->id()
        );

        if ($existing instanceof CharacterPortrait) {
            $portraitRepository->save(
                $character->id(),
                $existing->useGeneratedFallback()
            );
        }

        $this->flash->success(
            'The Guild-generated portrait has been restored.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id())
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
    
        $catalogue = new ItemCatalogue();
        $inventoryRepository = new CharacterInventoryRepository();
        $inventory = $inventoryRepository->find(
            $character->id()
        );
        $inventoryPresenter = new InventoryPresenter(
            $catalogue
        );

        $portrait = $this
            ->portraitRenderer
            ->forCharacter(
                $character
            );

        $inventoryState = $inventoryPresenter->present(
            $character,
            $inventory
        );

        $attacks = (new AttackPresenter($catalogue))->present(
            $character,
            $inventory
        );

        $arcana = (new ArcanePantryPresenter(
            new ArcaneAbilityCatalogue()
        ))->present(
            $character
        );

        $progression = (new RisingRegisterPresenter())
            ->present($character);

        $completeAdventurer = (new CompleteAdventurerPresenter())
            ->present(
                $character,
                $portrait,
                $inventoryState,
                $attacks,
                $arcana,
                $progression
            );

        return $this->views->render(
            View::make(
                'characters.show',
                [
                    'character' => $character,
                    'portrait' => $portrait,
                    'sealRegistry' => $this->sealRegistry,
                    'inventory' => $inventoryState,
                    'inventoryArmourClass' =>
                        $inventoryPresenter->armourClass(
                            $character,
                            $inventory
                        ),
                    'attacks' => $attacks,
                    'arcana' => $arcana,
                    'progression' => $progression,
                    'completeAdventurer' => $completeAdventurer,
                ]
            )
        );
    }
    
    /**
     * Add a catalogue item to the Adventurer's Pack.
     */
    public function addInventoryItem(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $itemId = sanitize_key(
            (string) ($_POST['item_id'] ?? '')
        );
        $quantity = max(
            1,
            min(99, (int) ($_POST['quantity'] ?? 1))
        );

        $catalogue = new ItemCatalogue();
        if ($catalogue->find($itemId) === null) {
            $this->flash->error(
                'That item could not be found in the Guild stores.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        $repository = new CharacterInventoryRepository();
        $inventory = $repository->find($character->id());
        $repository->save(
            $character->id(),
            $inventory->add($itemId, $quantity)
        );

        $this->flash->success(
            'The item has been packed into the adventurer’s satchel.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'equipment')
        );
    }

    /**
     * Change the quantity carried for an inventory item.
     */
    public function updateInventoryItem(
        string $id,
        string $item
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $quantity = max(
            0,
            min(99, (int) ($_POST['quantity'] ?? 1))
        );
        $repository = new CharacterInventoryRepository();
        $inventory = $repository->find($character->id());
        $repository->save(
            $character->id(),
            $inventory->setQuantity(
                sanitize_key($item),
                $quantity
            )
        );

        $this->flash->success(
            'The packing register has been updated.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'equipment')
        );
    }

    /**
     * Equip or unequip an item from the pack.
     */
    public function equipInventoryItem(
        string $id,
        string $item
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $itemId = sanitize_key($item);
        $repository = new CharacterInventoryRepository();
        $catalogue = new ItemCatalogue();
        $inventory = $repository->find($character->id());
        $entry = $inventory->find($itemId);

        if ($entry !== null) {
            $inventory = $entry->equipped()
                ? $inventory->unequip($itemId)
                : $inventory->equip($itemId, $catalogue);
            $repository->save($character->id(), $inventory);
        }

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'equipment')
        );
    }

    /**
     * Remove an item completely from the Adventurer's Pack.
     */
    public function removeInventoryItem(
        string $id,
        string $item
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $repository = new CharacterInventoryRepository();
        $inventory = $repository->find($character->id());
        $repository->save(
            $character->id(),
            $inventory->remove(
                sanitize_key($item)
            )
        );

        $this->flash->success(
            'The item has been removed from the packing register.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'equipment')
        );
    }


    /** Record experience earned by the adventurer. */
    public function addExperience(string $id): RedirectResponse
    {
        $character = $this->findCharacter($id);
        $amount = (int) ($_POST['experience'] ?? 0);

        if ($amount < 1 || $amount > 1000000) {
            $this->flash->error(
                'Enter an experience award between 1 and 1,000,000.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id(), 'progression')
            );
        }

        $oldLevel = $character->level()->value();
        $oldMaximum = $character->hitPoints()->maximum();

        $character->gainExperience(
            Experience::fromInt($amount)
        );

        $this->characters->save($character);

        $levelsGained = $character->level()->value() - $oldLevel;
        $hpGained = $character->hitPoints()->maximum() - $oldMaximum;

        $this->flash->success(
            $levelsGained > 0
                ? sprintf(
                    '%d XP recorded. Level %d certified! Maximum hit points increased by %d.',
                    $amount,
                    $character->level()->value(),
                    $hpGained
                )
                : sprintf(
                    '%d XP has been entered into the Rising Register.',
                    $amount
                )
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'progression')
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
                    'portrait' => $this
                        ->portraitRenderer
                        ->forWorkbench(
                            $character
                        ),
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
        CharacterId $id,
        ?string $ledgerTab = null
    ): string {
        $url = add_query_arg(
            'gmrc_route',
            'characters/' . rawurlencode(
                $id->value()
            ),
            home_url('/companion/')
        );

        if ($ledgerTab !== null) {
            $url = add_query_arg(
                'gmrc_ledger_tab',
                sanitize_key($ledgerTab),
                $url
            );
        }

        return $url;
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

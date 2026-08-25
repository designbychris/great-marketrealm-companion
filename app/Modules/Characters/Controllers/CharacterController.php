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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterPurse;
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
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\StartingEquipmentGrantService;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\InventoryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Services\RollTargetCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\RisingRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplineRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleGroveRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidGroveArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericSacredDomainRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericDivineArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeGiftLedgerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationGiftLedgerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererMetamagicService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockEldritchArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories\ActiveClassResourceRepository;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories\SorcererMetamagicRepository;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories\ActiveClassConditionRepository;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\FighterBattleReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\PaladinSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\WarlockPactReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SorcererSorceryReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\RangerFieldReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\ClericSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\BarbarianRageReserveService;
use InvalidArgumentException;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\LivingRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementLedgerPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Services\PathGiftPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceMode;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Choices\ChoiceRequirement;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\AdvancementChoiceStore;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\PendingAdvancementRepository;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementSealPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\GuildCertificationService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementChoiceRequirementResolver;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\AdvancementHistoryRepository;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Services\SubclassPreviewCatalogue;
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
use GreatMarketrealmCompanion\Modules\Parties\Presenters\CharacterFellowshipPresenter;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyOwnerId;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\BackgroundMechanicsRegister;
use GreatMarketrealmCompanion\Modules\Honours\Services\CharacterBookOfDeeds;
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
        private CharacterBuildProfileRepository $buildProfiles,
        private ?CharacterFellowshipPresenter $fellowships = null,
        private ?CharacterBookOfDeeds $characterHonours = null
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
                    'subclassPreviews' => (
                        new SubclassPreviewCatalogue(
                            $this->catalogue
                        )
                    )->all(),
                    'backgroundReferences' =>
                        $this->backgroundReferences(),
                    'startingEquipmentPackages' => array_map(
                        static fn ($package): array => [
                            'id' => $package->id(),
                            'class' => $package->classKey(),
                            'label' => $package->label(),
                            'items' => $package->items(),
                        ],
                        (new StartingEquipmentPackageRegister())->all()
                    ),
    
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
            background: Background::fromStringWithMechanics(
                $registration['background'],
                $registration['background_skills'],
                $registration['background_tools']
            ),
            selectedLanguages: Languages::fromStrings(
                $registration['languages']
            ),
            selectedToolProficiencies:
                ToolProficiencies::fromStrings(
                    $registration['tools']
                ),
            heritage: $catalogueData['heritage']
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

        (new StartingEquipmentGrantService())->grant(
            $character->id(),
            $data['class'],
            $request->startingEquipmentPackage()
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
    
        $currentBackground = $character->background();
        $background = $currentBackground->value() === $data['background']
            ? $currentBackground
            : (new BackgroundMechanicsRegister())->background($data['background']);

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

        try {
            $this->deleteCharacter->handle(
                $character->id()
            );
        } catch (RuntimeException $exception) {
            $this->flash->error($exception->getMessage());

            return $this->responses->redirect(
                $this->charactersUrl()
            );
        }

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
        return $this->renderLedger(
            $this->findCharacter($id),
            'characters.show',
            true
        );
    }

    /**
     * Render a trusted cross-account Character projection without mutation
     * controls. The caller must establish its own authorization boundary.
     */
    public function renderReadOnlyForCampaign(
        Character $character,
        string $campaignId,
        string $campaignName
    ): string {
        return $this->renderLedger(
            $character,
            'dungeonmaster.characters.show',
            false,
            [
                'campaignId' => $campaignId,
                'campaignName' => $campaignName,
                'readOnly' => true,
            ]
        );
    }

    /**
     * Assemble the canonical Character Ledger projection.
     *
     * @param array<string,mixed> $viewContext
     */
    private function renderLedger(
        Character $character,
        string $viewName,
        bool $includeFellowships,
        array $viewContext = []
    ): string {
    
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

        $activeResources = (
            new ActiveClassResourceRepository()
        )->find(
            $character->id()
        );

        $arcana['slots'] =
            $character
                ->characterClass()
                ->value()
            === 'warlock'
                ? (
                    new WarlockPactReserveService()
                )->presentSlots(
                    $character,
                    $activeResources
                )
                : (
                    new SharedSpellSlotReserveService()
                )->present(
                    $character,
                    $activeResources
                );

        $martialRegister = (
            new FighterMartialRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $activeConditions = (
            new ActiveClassConditionRepository()
        )->find(
            $character->id()
        );

        $rageRegister = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $character,
            $activeResources,
            $activeConditions
        );

        $cunningRegister = (
            new RogueCunningRegisterPresenter()
        )->present(
            $character
        );

        $disciplineRegister = (
            new MonkDisciplineRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $sacredRegister = (
            new PaladinSacredRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $patronRegister = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $eldritchArts = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $character
        );

        $metamagicChoices = (
            new SorcererMetamagicRepository()
        )->find(
            $character->id()
        );

        $originRegister = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $character,
            $activeResources,
            $metamagicChoices
        );

        $fieldRegister = (
            new RangerFieldRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $fieldArts = (
            new RangerFieldArtsPresenter()
        )->present(
            $character,
            $activeResources
        );

        $groveRegister = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $groveArts = (
            new DruidGroveArtsPresenter()
        )->present(
            $character,
            $activeResources
        );

        $domainRegister = (
            new ClericSacredDomainRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $divineArts = (
            new ClericDivineArtsPresenter()
        )->present(
            $character,
            $activeResources
        );

        $artificerRegister = (
            new ArtificerSpecialisationRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $artificerGifts = (
            new ArtificerSpecialisationGiftLedgerPresenter()
        )->present(
            $character
        );

        $collegeRegister = (
            new BardCollegeRegisterPresenter()
        )->present(
            $character,
            $activeResources
        );

        $collegeGifts = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $character
        );

        $progression = (new RisingRegisterPresenter())
            ->present($character);

        $pathGifts = (new PathGiftPresenter())
            ->present($character);

        $advancementHistory = (
            new AdvancementHistoryRepository()
        )->all(
            $character->id()
        );

        $livingRegister = (new LivingRegisterPresenter())
            ->present($character, $advancementHistory);

        $completeAdventurer = (new CompleteAdventurerPresenter())
            ->present(
                $character,
                $portrait,
                $inventoryState,
                $attacks,
                $arcana,
                $progression
            );

        $fellowships = [];
        $characterHonours = [];

        if (
            $includeFellowships
            && $this->fellowships instanceof CharacterFellowshipPresenter
        ) {
            $ownerUserId = function_exists(
                'get_current_user_id'
            )
                ? (int) \get_current_user_id()
                : 0;

            if ($ownerUserId > 0) {
                $fellowships = $this->fellowships->present(
                    $character->id(),
                    PartyOwnerId::fromInt($ownerUserId)
                );
            }
        }

        if (
            $includeFellowships
            && $this->characterHonours instanceof CharacterBookOfDeeds
        ) {
            $ownerUserId = function_exists('get_current_user_id')
                ? (int) \get_current_user_id()
                : 0;

            if ($ownerUserId > 0) {
                $characterHonours = $this->characterHonours->forCharacter(
                    $character,
                    $ownerUserId
                );
            }
        }

        return $this->views->render(
            View::make(
                $viewName,
                array_merge(
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
                    'rollTargets' => (
                        new RollTargetCatalogue()
                    )->forCharacter($character),
                    'arcana' => $arcana,
                    'martialRegister' => $martialRegister,
                    'rageRegister' => $rageRegister,
                    'cunningRegister' => $cunningRegister,
                    'disciplineRegister' => $disciplineRegister,
                    'sacredRegister' => $sacredRegister,
                    'patronRegister' => $patronRegister,
                    'eldritchArts' => $eldritchArts,
                    'originRegister' => $originRegister,
                    'fieldRegister' => $fieldRegister,
                    'fieldArts' => $fieldArts,
                    'groveRegister' => $groveRegister,
                    'groveArts' => $groveArts,
                    'domainRegister' => $domainRegister,
                    'divineArts' => $divineArts,
                    'artificerRegister' => $artificerRegister,
                    'artificerGifts' => $artificerGifts,
                    'collegeRegister' => $collegeRegister,
                    'collegeGifts' => $collegeGifts,
                    'progression' => $progression,
                    'pathGifts' => $pathGifts,
                    'advancementHistory' =>
                        $advancementHistory,
                    'livingRegister' => $livingRegister,
                    'completeAdventurer' => $completeAdventurer,
                    'fellowships' => $fellowships,
                    'characterHonours' => $characterHonours,
                    ],
                    $viewContext
                )
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


    /**
     * Add coins to the adventurer's personal purse.
     */
    public function depositPurse(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $amount = $this->purseAmountFromRequest();

        if (! $amount instanceof CharacterPurse) {
            $this->flash->error(
                'Enter at least one coin using valid GP, SP and CP amounts.'
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'equipment'
                )
            );
        }

        $character->depositToPurse($amount);
        $this->characters->save($character);

        $this->flash->success(
            sprintf(
                '%s has been added to the Adventurer’s Purse.',
                $amount->formatted()
            )
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'equipment'
            )
        );
    }

    /**
     * Spend coins from the adventurer's personal purse.
     */
    public function withdrawPurse(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $amount = $this->purseAmountFromRequest();

        if (! $amount instanceof CharacterPurse) {
            $this->flash->error(
                'Enter at least one coin using valid GP, SP and CP amounts.'
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'equipment'
                )
            );
        }

        if (
            $amount->copper()
            > $character->purse()->copper()
        ) {
            $this->flash->error(
                'The adventurer does not have enough coin in their purse.'
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'equipment'
                )
            );
        }

        $character->withdrawFromPurse($amount);
        $this->characters->save($character);

        $this->flash->success(
            sprintf(
                '%s has been spent from the Adventurer’s Purse.',
                $amount->formatted()
            )
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'equipment'
            )
        );
    }

    /**
     * Spend one use from a certified active class resource.
     */
    public function spendClassResource(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $resource = sanitize_key(
            (string) (
                $_POST['resource']
                ?? ''
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        $state = $repository->find(
            $character->id()
        );

        try {
            $state = (
                new FighterBattleReserveService()
            )->spend(
                $character,
                $state,
                $resource
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Battle Reserve has been marked as spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Refresh Fighter Battle Reserves after a short or long rest.
     */
    public function refreshClassResources(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $rest = sanitize_key(
            (string) (
                $_POST['rest']
                ?? ''
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        $state = $repository->find(
            $character->id()
        );

        $reserves =
            new FighterBattleReserveService();

        try {
            if ($rest === 'short') {
                $state = $reserves->shortRest(
                    $character,
                    $state
                );
            } elseif ($rest === 'long') {
                $state = $reserves->longRest(
                    $character,
                    $state
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a valid rest before refreshing Battle Reserves.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            $rest === 'short'
                ? 'Short-rest Battle Reserves have been restored.'
                : 'All Battle Reserves have been restored after the long rest.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend one finite Ranger Path field resource.
     */
    public function spendRangerFieldReserve(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $resource = sanitize_key(
            (string) (
                $_POST['resource']
                ?? ''
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new RangerFieldReserveService()
            )->spend(
                $character,
                $repository->find(
                    $character->id()
                ),
                $resource
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Ranger Field Reserve has been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore Ranger Path field resources and shared spell slots.
     */
    public function restRangerFieldReserves(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = $repository->find(
                $character->id()
            );

            $state = (
                new RangerFieldReserveService()
            )->longRest(
                $character,
                $state
            );

            $state = (
                new SharedSpellSlotReserveService()
            )->longRest(
                $character,
                $state
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'A long rest has restored the Ranger’s certified Field Reserves and spell slots.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    public function spendDruidPrimalReserve(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $resource = sanitize_key(
            (string) ($_POST['resource'] ?? '')
        );
        $repository = new ActiveClassResourceRepository();

        try {
            $state = (
                new DruidPrimalReserveService()
            )->spend(
                $character,
                $repository->find($character->id()),
                $resource
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error($exception->getMessage());
            return $this->responses->redirect(
                $this->characterUrl($character->id(), 'arcana')
            );
        }

        $repository->save($character->id(), $state);
        $this->flash->success(
            'The Druid’s Primal Reserve has been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'arcana')
        );
    }

    public function restDruidPrimalReserves(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $rest = sanitize_key(
            (string) ($_POST['rest'] ?? 'long')
        );
        $repository = new ActiveClassResourceRepository();

        try {
            $state = $repository->find($character->id());
            $service = new DruidPrimalReserveService();

            $state = $rest === 'short'
                ? $service->shortRest($character, $state)
                : $service->longRest($character, $state);

            if ($rest !== 'short') {
                $state = (
                    new SharedSpellSlotReserveService()
                )->longRest(
                    $character,
                    $state
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error($exception->getMessage());
            return $this->responses->redirect(
                $this->characterUrl($character->id(), 'arcana')
            );
        }

        $repository->save($character->id(), $state);

        $this->flash->success(
            $rest === 'short'
                ? 'Short-rest Primal Reserves have been restored.'
                : 'A long rest has restored Primal Reserves and spell slots.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id(), 'arcana')
        );
    }

    public function spendClericSacredReserve(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $resource = sanitize_key(
            (string) ($_POST['resource'] ?? '')
        );
        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new ClericSacredReserveService()
            )->spend(
                $character,
                $repository->find(
                    $character->id()
                ),
                $resource
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Cleric’s Sacred Reserve has been marked as spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    public function restClericSacredReserves(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $rest = sanitize_key(
            (string) ($_POST['rest'] ?? '')
        );
        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = $repository->find(
                $character->id()
            );

            $service =
                new ClericSacredReserveService();

            if ($rest === 'short') {
                $state = $service->shortRest(
                    $character,
                    $state
                );
            } elseif ($rest === 'long') {
                $state = $service->longRest(
                    $character,
                    $state
                );

                $state = (
                    new SharedSpellSlotReserveService()
                )->longRest(
                    $character,
                    $state
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a short or long rest before restoring Cleric Sacred Reserves.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            $rest === 'short'
                ? 'Channel Divinity has been restored after the short rest.'
                : 'A long rest has restored Cleric Sacred Reserves and spell slots.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Save the Sorcerer's certified Metamagic selections.
     */
    public function saveMetamagicChoices(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $choices = is_array(
            $_POST['metamagic']
            ?? null
        )
            ? array_values(
                $_POST['metamagic']
            )
            : [];

        try {
            $choices = (
                new SorcererMetamagicService()
            )->validateChoices(
                $character,
                $choices
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        (
            new SorcererMetamagicRepository()
        )->save(
            $character->id(),
            $choices
        );

        $this->flash->success(
            'The Sorcerer’s Metamagic Arts have been entered into the Origin Spark Register.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend Sorcery Points to use one certified Metamagic Art.
     */
    public function useMetamagic(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $metamagic = sanitize_key(
            (string) (
                $_POST['metamagic']
                ?? ''
            )
        );

        $spellLevel = max(
            0,
            min(
                9,
                (int) (
                    $_POST['spell_level']
                    ?? 0
                )
            )
        );

        $choices = (
            new SorcererMetamagicRepository()
        )->find(
            $character->id()
        );

        $resources =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new SorcererMetamagicService()
            )->use(
                $character,
                $resources->find(
                    $character->id()
                ),
                $choices,
                $metamagic,
                $spellLevel
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $resources->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Metamagic Art has been recorded and its Sorcery Point cost has been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend Sorcery Points directly from the Font of Magic reserve.
     */
    public function spendSorceryPoints(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $amount = max(
            1,
            min(
                20,
                (int) (
                    $_POST['amount']
                    ?? 1
                )
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new SorcererSorceryReserveService()
            )->spend(
                $character,
                $repository->find(
                    $character->id()
                ),
                $amount
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            $amount === 1
                ? 'One Sorcery Point has been spent.'
                : $amount . ' Sorcery Points have been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Resolve one Flexible Casting conversion.
     */
    public function convertSorceryReserve(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $direction = sanitize_key(
            (string) (
                $_POST['direction']
                ?? ''
            )
        );

        $slotLevel = max(
            1,
            min(
                9,
                (int) (
                    $_POST['slot_level']
                    ?? 0
                )
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        try {
            $service =
                new SorcererSorceryReserveService();

            $state = $repository->find(
                $character->id()
            );

            if ($direction === 'points-to-slot') {
                $state = $service->createSpellSlot(
                    $character,
                    $state,
                    $slotLevel
                );
            } elseif ($direction === 'slot-to-points') {
                $state = $service->convertSpellSlot(
                    $character,
                    $state,
                    $slotLevel
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a valid Font of Magic conversion.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            $direction === 'points-to-slot'
                ? 'Sorcery Points have been shaped into a spell slot.'
                : 'A spell slot has been converted into Sorcery Points.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore Sorcery Points and standard spell slots after a long rest.
     */
    public function restSorceryReserves(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = $repository->find(
                $character->id()
            );

            $state = (
                new SorcererSorceryReserveService()
            )->longRest(
                $character,
                $state
            );

            $state = (
                new SharedSpellSlotReserveService()
            )->longRest(
                $character,
                $state
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'A long rest has restored the Sorcerer’s Sorcery Points and spell slots.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend one Warlock Pact Magic slot.
     */
    public function spendPactSlot(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new WarlockPactReserveService()
            )->spend(
                $character,
                $repository->find(
                    $character->id()
                )
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'One Pact Magic slot has been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore the Warlock's Pact Magic reserve after a short or long rest.
     */
    public function restPactSlots(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $rest = sanitize_key(
            (string) (
                $_POST['rest']
                ?? ''
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        try {
            $service =
                new WarlockPactReserveService();

            $state = $repository->find(
                $character->id()
            );

            if ($rest === 'short') {
                $state = $service->shortRest(
                    $character,
                    $state
                );
            } elseif ($rest === 'long') {
                $state = $service->longRest(
                    $character,
                    $state
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a short or long rest before restoring Pact Magic.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Warlock’s Pact Magic reserve has been restored.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Resolve a named Paladin Sacred Action.
     */
    public function useSacredAction(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $action = sanitize_key(
            (string) (
                $_POST['sacred_action']
                ?? ''
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        $state = $repository->find(
            $character->id()
        );

        try {
            $sacred =
                new PaladinSacredReserveService();

            if ($action === 'lay-on-hands') {
                $amount = max(
                    1,
                    min(
                        100,
                        (int) (
                            $_POST['amount']
                            ?? 1
                        )
                    )
                );

                $state = $sacred->spend(
                    $character,
                    $state,
                    PaladinSacredReserveService::LAY_ON_HANDS,
                    $amount
                );

                if (
                    sanitize_key(
                        (string) (
                            $_POST['target']
                            ?? ''
                        )
                    ) === 'self'
                ) {
                    $character->heal($amount);
                    $this->characters->save(
                        $character
                    );
                }
            } elseif ($action === 'divine-sense') {
                $state = $sacred->spend(
                    $character,
                    $state,
                    PaladinSacredReserveService::DIVINE_SENSE
                );
            } elseif ($action === 'cleansing-touch') {
                $state = $sacred->spend(
                    $character,
                    $state,
                    PaladinSacredReserveService::CLEANSING_TOUCH
                );
            } elseif ($action === 'divine-smite') {
                $slotLevel = max(
                    1,
                    min(
                        9,
                        (int) (
                            $_POST['slot_level']
                            ?? 0
                        )
                    )
                );

                if (
                    $character
                        ->level()
                        ->value()
                    < 2
                ) {
                    throw new InvalidArgumentException(
                        'Divine Smite has not yet been certified for this Paladin.'
                    );
                }

                $state = (
                    new SharedSpellSlotReserveService()
                )->spend(
                    $character,
                    $state,
                    $slotLevel
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a certified Paladin Sacred Action.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            match ($action) {
                'lay-on-hands' =>
                    'Lay on Hands has been entered into the sacred field record.',
                'divine-sense' =>
                    'Divine Sense is active for this use.',
                'cleansing-touch' =>
                    'Cleansing Touch has been marked as used.',
                'divine-smite' =>
                    'The selected spell slot has been committed to Divine Smite.',
                default =>
                    'The Sacred Action has been recorded.',
            }
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend from one of the Paladin's persistent Sacred Reserves.
     */
    public function spendSacredReserve(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        $resource = sanitize_key(
            (string) (
                $_POST['resource']
                ?? ''
            )
        );

        $amount = max(
            1,
            min(
                100,
                (int) (
                    $_POST['amount']
                    ?? 1
                )
            )
        );

        $repository =
            new ActiveClassResourceRepository();

        try {
            $service =
                new PaladinSacredReserveService();

            $state = $service->spend(
                $character,
                $repository->find(
                    $character->id()
                ),
                $resource,
                $amount
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Sacred Reserve has been marked as spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore Paladin Sacred Reserves after a long rest.
     */
    public function restSacredReserves(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $repository =
            new ActiveClassResourceRepository();

        try {
            $state = (
                new PaladinSacredReserveService()
            )->longRest(
                $character,
                $repository->find(
                    $character->id()
                )
            );

            $state = (
                new SharedSpellSlotReserveService()
            )->longRest(
                $character,
                $state
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'A long rest has restored the Paladin’s Sacred Reserves.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Spend one point from the Monk's Discipline Reserve.
     */
    public function spendDiscipline(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $repository =
            new ActiveClassResourceRepository();

        $technique = sanitize_key(
            (string) (
                $_POST['technique']
                ?? ''
            )
        );

        try {
            $service =
                new MonkDisciplineReserveService();

            $state = $repository->find(
                $character->id()
            );

            $state = $technique !== ''
                ? $service->spendTechnique(
                    $character,
                    $state,
                    $technique
                )
                : $service->spend(
                    $character,
                    $state
                );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            $technique !== ''
                ? 'The Monk’s Discipline technique has been entered into the field record.'
                : 'One point of Discipline has been spent.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore the Monk's Discipline Reserve after a short or long rest.
     */
    public function restDiscipline(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $rest = sanitize_key(
            (string) ($_POST['rest'] ?? '')
        );
        $repository =
            new ActiveClassResourceRepository();
        $service =
            new MonkDisciplineReserveService();

        try {
            $state = $repository->find(
                $character->id()
            );

            if ($rest === 'short') {
                $state = $service->shortRest(
                    $character,
                    $state
                );
            } elseif ($rest === 'long') {
                $state = $service->longRest(
                    $character,
                    $state
                );
            } else {
                throw new InvalidArgumentException(
                    'Choose a short or long rest before restoring Discipline.'
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $repository->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'The Monk’s Discipline Reserve has been restored.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Enter Barbarian Rage and persist both reserve expenditure and state.
     */
    public function enterRage(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $resources = new ActiveClassResourceRepository();
        $conditions = new ActiveClassConditionRepository();

        try {
            $next = (
                new BarbarianRageReserveService()
            )->enter(
                $character,
                $resources->find(
                    $character->id()
                ),
                $conditions->find(
                    $character->id()
                )
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $resources->save(
            $character->id(),
            $next['resources']
        );

        $conditions->save(
            $character->id(),
            $next['conditions']
        );

        $this->flash->success(
            'Rage is active. The Barbarian’s fury has been entered into the field record.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * End the currently active Barbarian Rage.
     */
    public function endRage(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $conditions = new ActiveClassConditionRepository();

        try {
            $state = (
                new BarbarianRageReserveService()
            )->end(
                $character,
                $conditions->find(
                    $character->id()
                )
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $conditions->save(
            $character->id(),
            $state
        );

        $this->flash->success(
            'Rage has ended.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Restore Barbarian Rage reserves after a long rest.
     */
    public function restRage(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $resources = new ActiveClassResourceRepository();
        $conditions = new ActiveClassConditionRepository();

        try {
            $next = (
                new BarbarianRageReserveService()
            )->longRest(
                $character,
                $resources->find(
                    $character->id()
                ),
                $conditions->find(
                    $character->id()
                )
            );
        } catch (InvalidArgumentException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->characterUrl(
                    $character->id(),
                    'arcana'
                )
            );
        }

        $resources->save(
            $character->id(),
            $next['resources']
        );

        $conditions->save(
            $character->id(),
            $next['conditions']
        );

        $this->flash->success(
            'A long rest has restored the Barbarian’s Rage Reserves.'
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'arcana'
            )
        );
    }

    /**
     * Update mutable current and temporary hit points during active play.
     */
    public function updateVitalMeasures(string $id): RedirectResponse
    {
        $character = $this->findCharacter($id);
        $maximum = $character->hitPoints()->maximum();
        $current = (int) ($_POST['current_hp'] ?? -1);
        $temporary = (int) ($_POST['temporary_hp'] ?? -1);

        if (
            $current < 0
            || $current > $maximum
            || $temporary < 0
            || $temporary > 999
        ) {
            $this->flash->error(
                'Current HP must be between 0 and maximum HP, and temporary HP between 0 and 999.'
            );

            return $this->responses->redirect(
                $this->characterUrl($character->id())
            );
        }

        $character->updateVitalMeasures(
            $current,
            $temporary
        );

        $this->characters->save($character);

        $this->flash->success(
            'The Adventuring Measures have been updated.'
        );

        return $this->responses->redirect(
            $this->characterUrl($character->id())
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

        $character->gainExperience(
            Experience::fromInt($amount)
        );

        $this->characters->save($character);

        $this->flash->success(
            $character->canAdvance()
                ? sprintf(
                    '%d XP recorded. The Registrar has marked Level %d ready for advancement.',
                    $amount,
                    $character->level()->next()->value()
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
     * Display the Advancement Ledger for the next pending certification.
     */
    public function advancement(
        string $id
    ): string {
        $character = $this->findCharacter($id);
        $presenter = new AdvancementLedgerPresenter();
        $preview = $presenter->present($character);
        $choices = [];

        if (! empty($preview['eligible'])) {
            $targetLevel = (int) $preview[
                'target_level'
            ];

            $repository =
                new PendingAdvancementRepository();

            $pending = $repository->resumeOrBegin(
                $character->id(),
                $character->level()->value(),
                $targetLevel
            );

            $choices = $pending->choices();

            /*
             * Phase III.8.3 stored choices only in the PHP session.
             * If one still exists, migrate it into the durable character
             * advancement record the first time this page is revisited.
             */
            if ($choices === []) {
                $legacyStore =
                    new AdvancementChoiceStore();

                $legacyChoices = $legacyStore->all(
                    $character->id(),
                    $targetLevel
                );

                foreach (
                    $legacyChoices
                    as $choiceKey => $selections
                ) {
                    $pending->recordChoice(
                        $choiceKey,
                        $selections
                    );
                }

                if ($legacyChoices !== []) {
                    $repository->save($pending);

                    $legacyStore->clear(
                        $character->id(),
                        $targetLevel
                    );

                    $choices = $pending->choices();
                }
            }
        }

        $advancement = $presenter->present(
            $character,
            $choices
        );

        return $this->views->render(
            View::make(
                'characters.advancement',
                [
                    'character' => $character,
                    'advancement' => $advancement,
                    'advancementSeal' => (
                        new AdvancementSealPresenter()
                    )->present(
                        $character,
                        $advancement
                    ),
                    'flash' => [
                        'success' => $this->flash->success(),
                        'error' => $this->flash->error(),
                    ],
                ]
            )
        );
    }

    /**
     * Record a temporary Choice Folio selection.
     *
     * No Character mutation occurs here. The choice remains in the current
     * advancement session until a later phase seals the whole advancement.
     */
    public function recordAdvancementChoice(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);
        $preview = (
            new AdvancementLedgerPresenter()
        )->present($character);

        if (empty($preview['eligible'])) {
            $this->flash->error(
                'This adventurer is not currently eligible for advancement.'
            );

            return $this->responses->redirect(
                $this->advancementUrl(
                    $character->id()
                )
            );
        }

        $choiceKey = sanitize_key(
            (string) ($_POST['choice_key'] ?? '')
        );

        $rawSelections =
            $_POST['choice'] ?? [];

        $selections = is_array($rawSelections)
            ? $rawSelections
            : [$rawSelections];

        $requirement = (
            new AdvancementChoiceRequirementResolver()
        )->resolve(
            $character,
            (int) $preview['target_level'],
            $choiceKey
        );

        if ($requirement === null) {
            $this->flash->error(
                'That advancement choice is not recognised by the Registrar.'
            );

            return $this->responses->redirect(
                $this->advancementUrl(
                    $character->id()
                )
            );
        }

        $normalised = $requirement->normalise(
            array_map(
                'strval',
                $selections
            )
        );

        if (! $requirement->satisfiedBy($normalised)) {
            $this->flash->error(
                'Complete the required folio selections before continuing.'
            );

            return $this->responses->redirect(
                $this->advancementUrl(
                    $character->id()
                )
            );
        }

        (
            new PendingAdvancementRepository()
        )->recordChoice(
            $character->id(),
            $character->level()->value(),
            (int) $preview['target_level'],
            $choiceKey,
            $normalised
        );

        /*
         * Remove any older session-only copy now that the choice has
         * been written to the character's durable pending advancement.
         */
        (
            new AdvancementChoiceStore()
        )->clear(
            $character->id(),
            (int) $preview['target_level']
        );

        $this->flash->success(
            'The choice has been saved to the pending Advancement Ledger.'
        );

        return $this->responses->redirect(
            $this->advancementUrl(
                $character->id()
            )
        );
    }

    /**
     * Apply a sealed pending advancement to the permanent Guild Record.
     */
    public function certifyAdvancement(
        string $id
    ): RedirectResponse {
        $character = $this->findCharacter($id);

        try {
            $result = (
                new GuildCertificationService(
                    $this->characters,
                    new PendingAdvancementRepository(),
                    new AdvancementHistoryRepository()
                )
            )->certify($character);
        } catch (RuntimeException|\LogicException $exception) {
            $this->flash->error(
                $exception->getMessage()
            );

            return $this->responses->redirect(
                $this->advancementUrl(
                    $character->id()
                )
            );
        }

        $this->flash->success(
            sprintf(
                'Guild Certification complete: Level %d entered into the Register with +%d maximum HP.',
                (int) $result['target_level'],
                (int) $result['hit_point_gain']
            )
        );

        return $this->responses->redirect(
            $this->characterUrl(
                $character->id(),
                'progression'
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
                    'backgroundReferences' =>
                        $this->backgroundReferences(),
                    'startingEquipmentPackages' => array_map(
                        static fn ($package): array => [
                            'id' => $package->id(),
                            'class' => $package->classKey(),
                            'label' => $package->label(),
                            'items' => $package->items(),
                        ],
                        (new StartingEquipmentPackageRegister())->all()
                    ),
                ]
            )
        );
    }

    /**
     * Canonical optional Marketrealm background references keyed by ID.
     *
     * @return array<string,array<string,mixed>>
     */
    private function backgroundReferences(): array
    {
        $references = [];

        foreach ((new BackgroundMechanicsRegister())->all() as $background) {
            $references[$background->key()] = $background->toArray();
        }

        return $references;
    }

    /**
     * Build a validated coin amount from the current application request.
     */
    private function purseAmountFromRequest(): ?CharacterPurse
    {
        $gold = filter_var(
            $_POST['gold'] ?? null,
            FILTER_VALIDATE_INT
        );
        $silver = filter_var(
            $_POST['silver'] ?? null,
            FILTER_VALIDATE_INT
        );
        $copper = filter_var(
            $_POST['copper'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $gold === false
            || $silver === false
            || $copper === false
            || $gold < 0
            || $gold > 999999
            || $silver < 0
            || $silver > 9
            || $copper < 0
            || $copper > 9
        ) {
            return null;
        }

        $amount = CharacterPurse::fromCoins(
            $gold,
            $silver,
            $copper
        );

        return $amount->isEmpty()
            ? null
            : $amount;
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
     * Build the temporary Advancement Ledger URL.
     */
    private function advancementUrl(
        CharacterId $id
    ): string {
        return add_query_arg(
            'gmrc_route',
            'characters/'
                . rawurlencode($id->value())
                . '/progression/advance',
            home_url('/companion/')
        );
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

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\CharacterInventoryRepository;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\InventoryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\SharedSpellRegister;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Repositories\ActiveClassResourceRepository;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use WP_REST_Request;
use WP_Error;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use WP_REST_Response;

 defined('ABSPATH') || exit;

/** Read-only first mobile contract. Authentication uses WordPress REST authentication. */
final class PocketApi
{
    public function __construct(private CharacterRepositoryInterface $characters, private ?PortraitRenderer $portraits = null)
    {
    }

    public function register(): void
    {
        register_rest_route('gmrc-pocket/v1', '/session', [
            'methods' => 'GET',
            'permission_callback' => static fn (): bool => is_user_logged_in() && get_current_user_id() > 0,
            'callback' => [$this, 'session'],
        ]);

        register_rest_route('gmrc-pocket/v1', '/characters/(?P<id>[A-Za-z0-9]{26})/vitality', [
            'methods' => 'POST',
            'permission_callback' => static fn (): bool => is_user_logged_in() && get_current_user_id() > 0,
            'callback' => [$this, 'updateVitality'],
        ]);

        register_rest_route('gmrc-pocket/v1', '/characters', [
            'methods' => 'GET',
            'permission_callback' => static fn (): bool => is_user_logged_in() && get_current_user_id() > 0,
            'callback' => [$this, 'characters'],
        ]);
    }

    /** A minimal identity check for authenticated clients; never exposes credentials. */
    public function session(WP_REST_Request $request): WP_REST_Response
    {
        $user = wp_get_current_user();

        return new WP_REST_Response([
            'authenticated' => true,
            'user' => [
                'id' => (int) $user->ID,
                'display_name' => (string) $user->display_name,
            ],
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    /** Update only mutable play-state HP for a character owned by this account. */
    public function updateVitality(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $id = (string) $request->get_param('id');
        try {
            $characterId = CharacterId::fromString($id);
        } catch (\InvalidArgumentException $exception) {
            return new WP_Error('gmrc_pocket_invalid_id', 'Invalid character identifier.', ['status' => 400]);
        }

        // The contract's find() is owner-scoped; never use a cross-owner lookup here.
        $character = $this->characters->find($characterId);
        if ($character === null) {
            return new WP_Error('gmrc_pocket_not_found', 'Character not found.', ['status' => 404]);
        }
        $input = $request->get_json_params();
        if (!is_array($input)) {
            return new WP_Error('gmrc_pocket_invalid_body', 'A JSON body is required.', ['status' => 400]);
        }
        $hp = $character->hitPoints();
        foreach (['current', 'temporary', 'expected_current', 'expected_temporary'] as $field) {
            if (!array_key_exists($field, $input) || !is_int($input[$field])) {
                return new WP_Error('gmrc_pocket_invalid_hp', 'HP values must be whole numbers.', ['status' => 400]);
            }
        }
        if ($input['expected_current'] !== $hp->current() || $input['expected_temporary'] !== $hp->temporary()) {
            return new WP_Error('gmrc_pocket_stale_hp', 'HP changed elsewhere. Refresh and try again.', ['status' => 409]);
        }
        $current = $input['current'];
        $temporary = $input['temporary'];
        if ($current < 0 || $current > $hp->maximum() || $temporary < 0 || $temporary > 999) {
            return new WP_Error('gmrc_pocket_invalid_hp', 'Current HP must be between 0 and maximum HP; temporary HP between 0 and 999.', ['status' => 400]);
        }
        $character->updateVitalMeasures($current, $temporary);
        $this->characters->save($character);
        return new WP_REST_Response(['hp' => [
            'current' => $current, 'maximum' => $hp->maximum(), 'temporary' => $temporary,
        ]], 200, ['Cache-Control' => 'private, no-store']);
    }

    public function characters(WP_REST_Request $request): WP_REST_Response
    {
        $result = [];
        // Repository::all() is explicitly scoped to the authenticated WP user.
        $spellRegister = new SharedSpellRegister();
        $arcaneCatalogue = new ArcaneAbilityCatalogue();
        // Existing character IDs can predate the shared Handbook register.
        // These aliases are the same explicit bridge used by the main Arcana resolver.
        $legacySpellAliases = [
            'restorative-preserve' => 'cure-meats',
            'market-missile' => 'mystery-mustard-missile',
            'aisle-lightning' => 'lightning-lemonade',
            'stockroom-fireball' => 'flame-grilled-fireball',
            'stocklight-orb' => 'shelfshine',
        ];
        foreach ($this->characters->all() as $character) {
            $hp = $character->hitPoints();
            $portrait = $this->portraits?->forCharacter($character);
            $portraitUrl = $portrait?->attachmentUrl();
            $portraitSvg = $portrait?->svg() ?? '';
            $portraitData = $portraitUrl ? ['kind' => 'image', 'url' => esc_url_raw($portraitUrl)]
                : ($portraitSvg !== '' ? ['kind' => 'svg', 'url' => 'data:image/svg+xml;base64,' . base64_encode($portraitSvg)]
                : ['kind' => 'none', 'url' => null]);
            // Use the canonical Character value objects, never a second mobile rules engine.
            $savingThrows = $character->savingThrows();
            $savingThrowData = [];
            foreach (['STR' => 'strength', 'DEX' => 'dexterity', 'CON' => 'constitution', 'INT' => 'intelligence', 'WIS' => 'wisdom', 'CHA' => 'charisma'] as $short => $ability) {
                $save = $savingThrows->get($ability);
                $savingThrowData[$short] = ['modifier' => $save->modifier(), 'proficient' => $save->isProficient()];
            }
            $skillData = [];
            foreach ($character->skills()->all() as $identifier => $skill) {
                $skillData[$identifier] = [
                    'modifier' => $skill->modifier(),
                    'proficient' => $skill->isProficient(),
                    'expertise' => $skill->hasExpertise(),
                ];
            }
            // Inventory lookup is owner-scoped; present the same equipped attacks as the Ledger.
            $inventory = (new CharacterInventoryRepository())->find($character->id());
            $catalogue = new ItemCatalogue();
            $attacks = (new AttackPresenter($catalogue))->present($character, $inventory);
            $inventoryRows = (new InventoryPresenter($catalogue))->present($character, $inventory)['rows'];
            // Read-only: resolve only this character's learned spell identities against the shared register.
            // Unknown identities remain visible without fabricated mechanics.
            // Canonical desktop presenter and owner-scoped active resource ledger.
            // This phase is read-only: no spell-slot mutations or inferred resource state.
            $casting = (new ArcanePantryPresenter($arcaneCatalogue))->present($character);
            $slotState = (new ActiveClassResourceRepository())->find($character->id());
            $castingMeasures = [
                'ability' => $casting['casting_ability'],
                'attack_bonus' => $casting['spell_attack'],
                'save_dc' => $casting['save_dc'],
                'slots' => (new SharedSpellSlotReserveService())->present($character, $slotState),
            ];
            $spellbook = $character->spellbook();
            $spellRows = [];
            foreach (['cantrips' => $spellbook->cantrips(), 'spells' => $spellbook->spells()] as $group => $identifiers) {
                foreach ($identifiers as $identifier) {
                    $record = $spellRegister->find($identifier);
                    if ($record === null && isset($legacySpellAliases[$identifier])) {
                        $record = $spellRegister->find($legacySpellAliases[$identifier]);
                    }
                    // Some valid Arcane Pantry spells (e.g. Shelf Alarm) have no
                    // Handbook record. Preserve their real definitions, not guessed rules.
                    $arcane = null;
                    foreach ($arcaneCatalogue->forClass(strtolower($character->characterClass()->label())) as $ability) {
                        if ($ability->id() === $identifier && $ability->kind() === ($group === 'cantrips' ? 'cantrip' : 'spell')) {
                            $arcane = $ability;
                            break;
                        }
                    }
                    $arcaneDescription = $arcane?->description() ?? '';
                    $recordDescription = $record?->rulesText() ?? '';
                    // A canonical Handbook entry may have no structured mechanics.
                    // In that case the Arcane Pantry's explicit fields are usable.
                    $formula = $record?->formula() ?? $arcane?->formula();
                    $scales = $arcane !== null && ($arcane->slotLevelScaling() !== [] || $arcane->characterLevelScaling() !== []);
                    $spellRows[] = [
                        'id' => $identifier,
                        'name' => $arcane?->label() ?? $record?->name() ?? ucwords(str_replace('-', ' ', $identifier)),
                        'group' => $group,
                        'level' => $record?->level() ?? $arcane?->spellLevel(),
                        'school' => $record?->school(),
                        'casting_time' => $record?->castingTime() ?: ($arcane?->activation() ?? ''),
                        'range' => $record?->range() ?: ($arcane?->range() ?? ''),
                        'components' => $record?->components() ?? '',
                        'duration' => $record?->duration() ?: ($arcane?->duration() ?? ''),
                        'rules_text' => $recordDescription !== '' ? $recordDescription : $arcaneDescription,
                        'higher_levels' => $record?->higherLevels() ?? '',
                        'roll_kind' => $scales ? null : ($record?->rollKind() ?? $arcane?->rollKind()),
                        'formula' => $scales ? null : $formula,
                        'damage_type' => $record?->damageType() ?? $arcane?->damageType(),
                        'spell_attack' => ($record?->spellAttack() ?? false) || ($arcane?->isSpellAttack() ?? false),
                        'add_casting_modifier' => ($record?->addCastingModifier() ?? false) || ($arcane?->addCastingModifier() ?? false),
                        'resolved' => $record !== null || $arcane !== null,
                    ];
                }
            }
            $result[] = [
                'portrait' => $portraitData,
                'id' => $character->id()->value(),
                'name' => $character->name()->value(),
                'race' => $character->race()->label(),
                'class' => $character->characterClass()->label(),
                'level' => $character->level()->value(),
                'armour_class' => $character->armourClass()->value(),
                'initiative' => $character->initiative()->signed(),
                'speed_feet' => $character->speed()->feet(),
                'abilities' => [
                    'STR' => $character->abilityScores()->strength()->value(),
                    'DEX' => $character->abilityScores()->dexterity()->value(),
                    'CON' => $character->abilityScores()->constitution()->value(),
                    'INT' => $character->abilityScores()->intelligence()->value(),
                    'WIS' => $character->abilityScores()->wisdom()->value(),
                    'CHA' => $character->abilityScores()->charisma()->value(),
                ],
                'saving_throws' => $savingThrowData,
                'skills' => $skillData,
                'attacks' => $attacks,
                'equipment' => $inventoryRows,
                'spellbook' => $spellRows,
                'spellcasting' => $castingMeasures,
                'proficiency_bonus' => $character->proficiencyBonus()->value(),
                'hp' => [
                    'current' => $hp->current(),
                    'maximum' => $hp->maximum(),
                    'temporary' => $hp->temporary(),
                ],
            ];
        }

        return new WP_REST_Response(['characters' => $result], 200, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
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
        foreach ($this->characters->all() as $character) {
            $hp = $character->hitPoints();
            $portrait = $this->portraits?->forCharacter($character);
            $portraitUrl = $portrait?->attachmentUrl();
            $portraitSvg = $portrait?->svg() ?? '';
            $portraitData = $portraitUrl ? ['kind' => 'image', 'url' => esc_url_raw($portraitUrl)]
                : ($portraitSvg !== '' ? ['kind' => 'svg', 'url' => 'data:image/svg+xml;base64,' . base64_encode($portraitSvg)]
                : ['kind' => 'none', 'url' => null]);
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

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Mobile;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use WP_REST_Request;
use WP_REST_Response;

 defined('ABSPATH') || exit;

/** Read-only first mobile contract. Authentication uses WordPress REST authentication. */
final class PocketApi
{
    public function __construct(private CharacterRepositoryInterface $characters)
    {
    }

    public function register(): void
    {
        register_rest_route('gmrc-pocket/v1', '/session', [
            'methods' => 'GET',
            'permission_callback' => static fn (): bool => is_user_logged_in() && get_current_user_id() > 0,
            'callback' => [$this, 'session'],
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

    public function characters(WP_REST_Request $request): WP_REST_Response
    {
        $result = [];
        // Repository::all() is explicitly scoped to the authenticated WP user.
        foreach ($this->characters->all() as $character) {
            $hp = $character->hitPoints();
            $result[] = [
                'id' => $character->id()->value(),
                'name' => $character->name()->value(),
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

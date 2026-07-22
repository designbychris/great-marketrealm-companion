<?php

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Characters\CharactersServiceProvider;
use GreatMarketrealmCompanion\Navigation\Icons;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;

defined('ABSPATH') || exit;

/**
 * Characters Kingdom.
 *
 * Describes the services, routes, and navigation belonging
 * to the Characters area of Marketrealm Companion.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class CharactersKingdom extends Kingdom
{
    /**
     * Return the Kingdom's unique key.
     */
    public function key(): string
    {
        return 'characters';
    }

    /**
     * Return the Kingdom's service provider.
     *
     * @return class-string<CharactersServiceProvider>
     */
    public function provider(): string
    {
        return CharactersServiceProvider::class;
    }

    /**
     * Return the Kingdom's route files.
     *
     * @return array<int, string>
     */
    public function routes(): array
    {
        return [
            GMRC_PATH . 'app/Modules/Characters/Routes.php',
        ];
    }

    /**
     * Register the Characters navigation item.
     */
    public function registerNavigation(
        Navigation $navigation
    ): void {
        if ($navigation->get($this->key()) !== null) {
            return;
        }

        $navigation->add(
            MenuItem::make(
                'characters',
                __('Characters', 'great-marketrealm-companion'),
                Icons::USERS,
                'characters',
                20
            )
        );
    }

    /**
     * Resources contributed by the Characters Kingdom.
     */
    public function resources(): array
    {
        return [
            CharacterResource::class,
        ];
    }
}

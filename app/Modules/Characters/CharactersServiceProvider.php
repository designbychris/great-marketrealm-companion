<?php

namespace GreatMarketrealmCompanion\Modules\Characters;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Services\Auby\Auby;

defined('ABSPATH') || exit;

/**
 * Characters Service Provider.
 *
 * Registers services belonging to the Characters Kingdom.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class CharactersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $container = $this->app->container();

        $container->singleton(
            CharacterRepository::class,
            static fn (): CharacterRepository =>
                new CharacterRepository()
        );

        $container->bind(
            CreateCharacterAction::class,
            static fn (Container $container): CreateCharacterAction =>
                new CreateCharacterAction(
                    $container->make(
                        CharacterRepository::class
                    )
                )
        );

        $container->bind(
            UpdateCharacterAction::class,
            static fn (Container $container): UpdateCharacterAction =>
                new UpdateCharacterAction(
                    $container->make(
                        CharacterRepository::class
                    )
                )
        );

        $container->bind(
            DeleteCharacterAction::class,
            static fn (Container $container): DeleteCharacterAction =>
                new DeleteCharacterAction(
                    $container->make(
                        CharacterRepository::class
                    )
                )
        );

        $container->singleton(
            Auby::class
        );

        $container->bind(
            CharacterController::class
        );
    }

    public function boot(): void
    {
        add_action(
            'init',
            [$this, 'registerPostType']
        );
    }

    public function registerPostType(): void
    {
        register_post_type(
            'gmrc_character',
            [
                'labels' => [
                    'name'          => 'Characters',
                    'singular_name' => 'Character',
                ],
                'public'              => false,
                'show_ui'             => false,
                'show_in_rest'        => false,
                'supports'            => [
                    'title',
                    'author',
                ],
                'capability_type'      => 'post',
                'map_meta_cap'         => true,
            ]
        );
    }
}

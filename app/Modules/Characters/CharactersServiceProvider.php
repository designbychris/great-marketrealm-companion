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

        $container->bind(
            CharacterController::class,
            static fn (Container $container): CharacterController =>
                new CharacterController(
                    $container->make(
                        CharacterRepository::class
                    ),
                    $container->make(
                        ViewFactory::class
                    ),
                    $container->make(
                        CreateCharacterAction::class
                    ),
                    $container->make(
                        UpdateCharacterAction::class
                    ),
                    $container->make(
                        DeleteCharacterAction::class
                    )
                )
        );
    }

    public function boot(): void
    {
        // Character module boot logic will live here later.
    }
}

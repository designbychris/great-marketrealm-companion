<?php

namespace GreatMarketrealmCompanion\Modules\Characters;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
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
            static function (): CharacterRepository {
                return new CharacterRepository();
            }
        );

        $container->bind(
            CharacterController::class,
            static function (Container $container): CharacterController {
                return new CharacterController(
                    $container->make(CharacterRepository::class),
                    $container->make(ViewFactory::class)
                );
            }
        );
    }

    public function boot(): void
    {
        // Character module boot logic will live here later.
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\ArmouryReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\BackgroundReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\SpellReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Controllers\LibraryController;
use GreatMarketrealmCompanion\Modules\Library\Models\ReferenceLibraryRegistry;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

final class LibraryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $container =
            $this->app->container();

        $container->singleton(
            ReferenceLibraryRegistry::class,
            static function (): ReferenceLibraryRegistry {
                $registry =
                    new ReferenceLibraryRegistry();

                $registry->add(
                    new SpellReferenceCatalogue()
                );
                $registry->add(
                    new BackgroundReferenceCatalogue()
                );
                $registry->add(
                    new ArmouryReferenceCatalogue()
                );

                return $registry;
            }
        );

        $container->bind(
            LibraryController::class,
            static fn (
                Container $container
            ): LibraryController =>
                new LibraryController(
                    $container->make(
                        ReferenceLibraryRegistry::class
                    ),
                    $container->make(
                        ViewFactory::class
                    )
                )
        );
    }
}

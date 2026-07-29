<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Definitions\DefinitionFactory;

final class DefinitionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->container()->singleton(
            DefinitionFactory::class,
            fn () => new DefinitionFactory(),
        );
    }

    public function boot(): void
    {
    }
}

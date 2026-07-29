<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Definitions\Definitions;

final class DefinitionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app
            ->container()
            ->singleton(
                Definitions::class,
                static fn (): Definitions => new Definitions(),
            );
    }

    public function boot(): void
    {
    }
}

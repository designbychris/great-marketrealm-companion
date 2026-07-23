<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Pages\PageRegistry;
use GreatMarketrealmCompanion\Foundation\ServiceProvider;
use GreatMarketrealmCompanion\Resources\ResourceRegistry;

defined('ABSPATH') || exit;

class PageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            PageRegistry::class,
            static fn (): PageRegistry => new PageRegistry()
        );
    }

    public function boot(): void
    {
        $pages = $this->app->make(
            PageRegistry::class
        );

        $resources = $this->app->make(
            ResourceRegistry::class
        );

        foreach ($resources->all() as $resource) {
            $pages->registerResource($resource);
        }

        error_log(
            'GMRC pages: ' .
            implode(', ', array_keys($pages->all()))
        );
    }
}

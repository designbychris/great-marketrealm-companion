<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Kingdoms\KingdomRegistry;
use GreatMarketrealmCompanion\Resources\Resource;
use GreatMarketrealmCompanion\Resources\ResourceRegistry;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Resource Service Provider.
 *
 * Discovers and registers Resources contributed
 * by application Kingdoms.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
class ResourceServiceProvider extends ServiceProvider
{
    /**
     * Register Resource services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            ResourceRegistry::class,
            static fn (): ResourceRegistry => new ResourceRegistry()
        );
    }

    /**
     * Register Kingdom Resources and their routes.
     */
    public function boot(): void
    {
        $resources = $this->app->make(
            ResourceRegistry::class
        );
    
        $kingdoms = $this->app->make(
            KingdomRegistry::class
        );
    
        foreach ($kingdoms->resourceClasses() as $resourceClass) {
            $resources->add(
                $this->createResource(
                    $resourceClass
                )
            );
        }
    
        do_action(
            'gmrc_resources_registered',
            $resources
        );
    }

    /**
     * Create a Resource instance.
     *
     * @param class-string<Resource> $resourceClass
     */
    protected function createResource(
        string $resourceClass
    ): Resource {
        if (! is_subclass_of(
            $resourceClass,
            Resource::class
        )) {
            throw new RuntimeException(
                sprintf(
                    'Invalid Resource class: %s',
                    $resourceClass
                )
            );
        }

        return new $resourceClass(
            $this->app
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use GreatMarketrealmCompanion\Providers\ServiceProvider;

defined('ABSPATH') || exit;

final class PartiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $container = $this->app->container();

        $container->singleton(
            PartyRepository::class,
            static fn (): PartyRepository =>
                new PartyRepository()
        );

        $container->singleton(
            PartyRepositoryInterface::class,
            static fn (Container $container): PartyRepositoryInterface =>
                $container->make(PartyRepository::class)
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
            'gmrc_party',
            [
                'labels' => [
                    'name' => 'Parties',
                    'singular_name' => 'Party',
                ],
                'public' => false,
                'show_ui' => false,
                'show_in_rest' => false,
                'supports' => [
                    'title',
                    'author',
                ],
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ]
        );
    }
}

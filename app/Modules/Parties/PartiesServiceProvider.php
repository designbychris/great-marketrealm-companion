<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\Parties\Actions\AddPartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\ChangePartyMemberRoleAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\ChangePartyMemberOfficeAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\CreatePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\DeletePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RemovePartyMemberAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\RenamePartyAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\UpdatePartyStandardAction;
use GreatMarketrealmCompanion\Modules\Parties\Actions\UpdatePartyCharterAction;
use GreatMarketrealmCompanion\Modules\Parties\Services\PartyFinder;
use GreatMarketrealmCompanion\Modules\Parties\Controllers\PartyController;
use GreatMarketrealmCompanion\Modules\Parties\Presenters\FellowshipPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
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

        $container->singleton(
            PartyFinder::class,
            static fn (Container $container): PartyFinder =>
                new PartyFinder(
                    $container->make(
                        PartyRepositoryInterface::class
                    )
                )
        );

        $container->singleton(
            FellowshipPresenter::class,
            static fn (Container $container): FellowshipPresenter =>
                new FellowshipPresenter(
                    $container->make(
                        CharacterRepositoryInterface::class
                    ),
                    $container->make(
                        PortraitRenderer::class
                    )
                )
        );

        $container->bind(
            CreatePartyAction::class,
            static fn (Container $container): CreatePartyAction =>
                new CreatePartyAction(
                    $container->make(
                        PartyRepositoryInterface::class
                    )
                )
        );

        $container->bind(
            AddPartyMemberAction::class,
            static fn (Container $container): AddPartyMemberAction =>
                new AddPartyMemberAction(
                    $container->make(
                        PartyRepositoryInterface::class
                    ),
                    $container->make(
                        CharacterRepositoryInterface::class
                    ),
                    $container->make(
                        PartyFinder::class
                    )
                )
        );

        $container->bind(
            RemovePartyMemberAction::class
        );

        $container->bind(
            ChangePartyMemberRoleAction::class
        );

        $container->bind(
            ChangePartyMemberOfficeAction::class
        );

        $container->bind(
            RenamePartyAction::class
        );

        $container->bind(
            UpdatePartyStandardAction::class
        );

        $container->bind(
            UpdatePartyCharterAction::class
        );

        $container->bind(
            DeletePartyAction::class
        );

        $container->bind(
            PartyController::class
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

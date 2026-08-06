<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Rules\CharacterCreationRules;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Repositories\CharacterPortraitRepository;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitLayerRegistry;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRecipeGenerator;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\SubmittedPortraitRecipeFactory;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\BackgroundLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\BodyLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\ClassLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\EffectsLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\AccessoryLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\AssetFaceLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\AssetFrameLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\ClassEffectsLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers\GuildOrnamentLayerRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitLayerStack;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitSvgRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitVariantRegistryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitVariantRegistry;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Services\Auby\Auby;
use GreatMarketrealmCompanion\Services\Auby\QuoteRepository;

defined('ABSPATH') || exit;

/**
 * Characters Service Provider.
 *
 * Registers services belonging to the Characters Kingdom.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
final class CharactersServiceProvider extends ServiceProvider
{
    /**
     * Register Characters Kingdom services.
     */
    public function register(): void
    {
        $container = $this->app->container();

        /*
         * Domain rules.
         */
        $container->singleton(
            CharacterCreationRules::class
        );

        /*
         * Domain services.
         */
        $container->singleton(
            CharacterFactory::class,
            static fn (
                Container $container
            ): CharacterFactory =>
                new CharacterFactory(
                    $container->make(
                        CharacterCreationRules::class
                    )
                )
        );

        /*
         * Register the concrete WordPress repository once.
         */
        $container->singleton(
            CharacterRepository::class,
            static fn (): CharacterRepository =>
                new CharacterRepository()
        );

        /*
         * Resolve the repository contract through the existing
         * concrete singleton rather than creating a second instance.
         */
        $container->singleton(
            CharacterRepositoryInterface::class,
            static fn (
                Container $container
            ): CharacterRepositoryInterface =>
                $container->make(
                    CharacterRepository::class
                )
        );

        $container->bind(
            CreateCharacterAction::class,
            static fn (
                Container $container
            ): CreateCharacterAction =>
                new CreateCharacterAction(
                    $container->make(
                        CharacterRepositoryInterface::class
                    ),
                    $container->make(
                        CharacterPortraitRepositoryInterface::class
                    ),
                    $container->make(
                        PortraitRecipeGenerator::class
                    )
                )
        );

        $container->bind(
            UpdateCharacterAction::class,
            static fn (
                Container $container
            ): UpdateCharacterAction =>
                new UpdateCharacterAction(
                    $container->make(
                        CharacterRepositoryInterface::class
                    )
                )
        );

        $container->bind(
            DeleteCharacterAction::class,
            static fn (
                Container $container
            ): DeleteCharacterAction =>
                new DeleteCharacterAction(
                    $container->make(
                        CharacterRepositoryInterface::class
                    )
                )
        );

        $container->singleton(
            QuoteRepository::class
        );

        $container->singleton(
            Auby::class
        );

        /*
         * Portrait rendering stack.
         */
        $container->singleton(
            PortraitSvgAssetLibrary::class,
            static fn (): PortraitSvgAssetLibrary =>
                new PortraitSvgAssetLibrary(
                    __DIR__ . '/Portraits/Library'
                )
        );

        $container->singleton(
            PortraitLayerStack::class,
            static fn (
                Container $container
            ): PortraitLayerStack =>
                new PortraitLayerStack(
                    [
                        new BackgroundLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new BodyLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new AssetFaceLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new ClassLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new AccessoryLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new ClassEffectsLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new EffectsLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new GuildOrnamentLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                        new AssetFrameLayerRenderer(
                            $container->make(PortraitSvgAssetLibrary::class)
                        ),
                    ]
                )
        );
        
        $container->singleton(
            PortraitSvgRenderer::class,
            static fn (
                Container $container
            ): PortraitSvgRenderer =>
                new PortraitSvgRenderer(
                    $container->make(PortraitLayerStack::class),
                    $container->make(PortraitSvgAssetLibrary::class)
                )
        );

        /*
         * Portrait layers and deterministic recipes.
         */
        $container->singleton(
            PortraitLayerRegistry::class
        );
        
        $container->singleton(
            PortraitLayerRegistryInterface::class,
            static fn (
                Container $container
            ): PortraitLayerRegistryInterface =>
                $container->make(
                    PortraitLayerRegistry::class
                )
        );

        $container->singleton(
            PortraitVariantRegistry::class,
            static fn (
                Container $container
            ): PortraitVariantRegistry =>
                new PortraitVariantRegistry(
                    $container->make(
                        PortraitLayerRegistryInterface::class
                    ),
                    $container->make(
                        PortraitSvgAssetLibrary::class
                    )
                )
        );
        
        $container->singleton(
            PortraitVariantRegistryInterface::class,
            static fn (
                Container $container
            ): PortraitVariantRegistryInterface =>
                $container->make(
                    PortraitVariantRegistry::class
                )
        );

        $container->singleton(
            SubmittedPortraitRecipeFactory::class,
            static fn (
                Container $container
            ): SubmittedPortraitRecipeFactory =>
                new SubmittedPortraitRecipeFactory(
                    $container->make(
                        PortraitLayerRegistryInterface::class
                    )
                )
        );
        
        $container->singleton(
            PortraitRecipeGenerator::class,
            static fn (
                Container $container
            ): PortraitRecipeGenerator =>
                new PortraitRecipeGenerator(
                    $container->make(
                        PortraitLayerRegistryInterface::class
                    )
                )
        );
        
        /*
         * WordPress portrait persistence.
         */
        $container->singleton(
            CharacterPortraitRepository::class,
            static fn (): CharacterPortraitRepository =>
                new CharacterPortraitRepository()
        );
        
        $container->singleton(
            CharacterPortraitRepositoryInterface::class,
            static fn (
                Container $container
            ): CharacterPortraitRepositoryInterface =>
                $container->make(
                    CharacterPortraitRepository::class
                )
        );

        /*
         * Presentation-ready portrait service.
         */
        $container->singleton(
            PortraitRenderer::class,
            static fn (
                Container $container
            ): PortraitRenderer =>
                new PortraitRenderer(
                    $container->make(
                        CharacterPortraitRepositoryInterface::class
                    ),
                    $container->make(
                        PortraitRecipeGenerator::class
                    ),
                    $container->make(
                        PortraitSvgRenderer::class
                    )
                )
        );
        
        /*
         * Controller resolved after its dependencies are registered.
         */
        $container->bind(
            CharacterController::class
        );
    }

    /**
     * Register WordPress hooks.
     */
    public function boot(): void
    {
        add_action(
            'init',
            [$this, 'registerPostType']
        );
    }

    /**
     * Register the Character storage post type.
     */
    public function registerPostType(): void
    {
        register_post_type(
            'gmrc_character',
            [
                'labels' => [
                    'name' => 'Characters',
                    'singular_name' => 'Character',
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

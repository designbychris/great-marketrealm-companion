<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

/**
 * Portrait Renderer.
 *
 * Converts persisted Character portrait information into a
 * presentation-ready view model.
 *
 * Despite its name, this service does not produce HTML. The
 * illuminated portrait component remains responsible for markup.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitRenderer
{
    public function __construct(
        private CharacterPortraitRepositoryInterface $portraits,
        private PortraitRecipeGenerator $recipes,
        private PortraitSvgRenderer $svgRenderer,
    ) {
    }

    /**
     * Build a portrait view model for a Character.
     */
    public function forCharacter(
        Character $character
    ): PortraitViewModel {
        $portrait = $this->portraits->find(
            $character->id()
        );

        if (! $portrait instanceof CharacterPortrait) {
            $portrait = CharacterPortrait::generated(
                $this->recipes->forCharacter(
                    $character
                )
            );
        }

        return $this->viewModel(
            $character,
            $portrait
        );
    }

    /**
     * @param Character[] $characters
     *
     * @return array<string,PortraitViewModel>
     */
    public function forCharacters(
        array $characters
    ): array {
        $portraits = [];

        foreach ($characters as $character) {
            if (! $character instanceof Character) {
                continue;
            }

            $portraits[
                $character->id()->value()
            ] = $this->forCharacter(
                $character
            );
        }

        return $portraits;
    }

    private function viewModel(
        Character $character,
        CharacterPortrait $portrait
    ): PortraitViewModel {
        $recipe = $portrait->recipe();

        $attachmentId =
            $portrait->attachmentId()?->value();

        $attachmentUrl = null;

        if (
            $portrait->mode()->isCustom()
            && $attachmentId !== null
        ) {
            $resolvedUrl = wp_get_attachment_image_url(
                $attachmentId,
                'large'
            );

            if (is_string($resolvedUrl)) {
                $attachmentUrl = $resolvedUrl;
            }
        }

        /*
         * If a custom attachment is unavailable, preserve the
         * generated fallback recipe instead of showing a broken image.
         */
        $mode = $portrait->mode()->value();

        if (
            $mode === 'custom'
            && $attachmentUrl === null
            && $recipe instanceof PortraitRecipe
        ) {
            $mode = 'generated';
        }

        $context = PortraitRenderContext::fromRecipe()

        $svg = $this->svgRenderer->render(
        $context
        );
        
        return new PortraitViewModel(
            mode: $mode,
            name: $character->name()->value(),
            race: $character->race()->value(),
            raceLabel: $character->race()->label(),
            characterClass: $character
                ->characterClass()
                ->value(),
            classLabel: $character
                ->characterClass()
                ->label(),
            layers: $this->recipeLayers(
                $recipe
            ),
            seed: $recipe?->seed()->value(),
            attachmentId: $attachmentId,
            attachmentUrl: $attachmentUrl,
            svg: $svg,
        );
    }

    /**
     * @return array<string,string>
     */
    private function recipeLayers(
        ?PortraitRecipe $recipe
    ): array {
        if (! $recipe instanceof PortraitRecipe) {
            return [];
        }

        return array_map(
            static fn ($layer): string =>
                $layer->value(),
            $recipe->layers()
        );
    }
}

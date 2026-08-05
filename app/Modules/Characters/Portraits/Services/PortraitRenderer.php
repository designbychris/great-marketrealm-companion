<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\CharacterPortraitRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\CharacterPortrait;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitSvgRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

/**
 * Portrait Renderer.
 *
 * Converts portrait information into presentation-ready
 * PortraitViewModel instances.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitRenderer
{
    /**
     * Slots used by the procedural portrait recipe.
     *
     * @var array<int,string>
     */
    private const LAYER_SLOTS = [
        'background',
        'body',
        'head',
        'eyes',
        'mouth',
        'palette',
        'heritage',
        'outfit',
        'equipment',
        'class_accessory',
        'frame',
        'effects',
    ];

    public function __construct(
        private CharacterPortraitRepositoryInterface $portraits,
        private PortraitRecipeGenerator $recipes,
        private PortraitSvgRenderer $svgRenderer
    ) {
    }

    /**
     * Build a portrait for a persisted Character.
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
     * Build the provisional portrait used by the live creator.
     *
     * A Character entity does not yet exist at this point, so the
     * render context is created directly from primitive form values.
     */
    public function preview(
        string $name = '',
        string $race = '',
        string $characterClass = ''
    ): PortraitViewModel {
        $name = trim($name);
        $race = sanitize_key($race);
        $characterClass = sanitize_key(
            $characterClass
        );

        if (! Race::supports($race)) {
            $race = '';
        }

        if (! CharacterClass::supports(
            $characterClass
        )) {
            $characterClass = '';
        }

        $raceLabel = $race !== ''
            ? Race::fromString($race)->label()
            : '';

        $classLabel = $characterClass !== ''
            ? CharacterClass::fromString(
                $characterClass
            )->label()
            : '';

        $context = PortraitRenderContext::provisional(
            $name,
            $race,
            $characterClass
        );

        return new PortraitViewModel(
            mode: 'generated',
            name: $name,
            race: $race,
            raceLabel: $raceLabel,
            characterClass: $characterClass,
            classLabel: $classLabel,
            svg: $this->svgRenderer->render(
                $context
            ),
            layers: $this->contextLayers(
                $context
            ),
            seed: $context->seed()
        );
    }

    /**
     * Build several portrait view models indexed by Character ID.
     *
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

    /**
     * Build a presentation model for a persisted portrait.
     */
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

        $mode = $portrait->mode()->value();

        /*
         * Fall back to the generated recipe when a custom media
         * attachment has been removed or is unavailable.
         */
        if (
            $mode === 'custom'
            && $attachmentUrl === null
            && $recipe instanceof PortraitRecipe
        ) {
            $mode = 'generated';
        }

        $baseViewModel = new PortraitViewModel(
            mode: $mode,
            name: $character
                ->name()
                ->value(),
            race: $character
                ->race()
                ->value(),
            raceLabel: $character
                ->race()
                ->label(),
            characterClass: $character
                ->characterClass()
                ->value(),
            classLabel: $character
                ->characterClass()
                ->label(),
            svg: '',
            layers: $this->recipeLayers(
                $recipe
            ),
            seed: $recipe?->seed()->value(),
            attachmentId: $attachmentId,
            attachmentUrl: $attachmentUrl
        );

        $svg = '';

        if ($mode === 'generated') {
            $context =
                PortraitRenderContext::fromViewModel(
                    $baseViewModel
                );

            $svg = $this->svgRenderer->render(
                $context
            );
        }

        return new PortraitViewModel(
            mode: $baseViewModel->mode(),
            name: $baseViewModel->name(),
            race: $baseViewModel->race(),
            raceLabel:
                $baseViewModel->raceLabel(),
            characterClass:
                $baseViewModel->characterClass(),
            classLabel:
                $baseViewModel->classLabel(),
            svg: $svg,
            layers: $baseViewModel->layers(),
            seed: $baseViewModel->seed(),
            attachmentId:
                $baseViewModel->attachmentId(),
            attachmentUrl:
                $baseViewModel->attachmentUrl()
        );
    }

    /**
     * Convert a saved recipe into primitive layer identifiers.
     *
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

    /**
     * Convert a provisional rendering context into layer values.
     *
     * @return array<string,string>
     */
    private function contextLayers(
        PortraitRenderContext $context
    ): array {
        $layers = [];

        foreach (self::LAYER_SLOTS as $slot) {
            $value = $context->layer(
                $slot
            );

            if ($value === '') {
                continue;
            }

            $layers[$slot] = $value;
        }

        return $layers;
    }
}

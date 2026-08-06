<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Optional class-specific decorative effects layer.
 */
final class ClassEffectsLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function __construct(
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    public function priority(): int
    {
        return 38;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $layerId = $context->layer(
            'class_effects'
        );

        if (! $this->assets->has($layerId)) {
            $layerId = $this->defaultLayerId(
                $context
            );
        }

        return $this->layerMarkup(
            $layerId
        );
    }

    private function defaultLayerId(
        PortraitRenderContext $context
    ): string {
        $characterClass = sanitize_key(
            $context->characterClass()
        );

        return $characterClass !== ''
            ? $characterClass . '-effects-01'
            : '';
    }

    private function layerMarkup(
        string $layerId
    ): string {
        if (! $this->assets->has($layerId)) {
            return '<g class="gmrc-portrait-layer gmrc-portrait-layer--class-effects" data-portrait-layer="class-effects" hidden></g>';
        }

        return sprintf(
            '<g class="gmrc-portrait-layer gmrc-portrait-layer--class-effects" data-portrait-layer="class-effects" data-layer-id="%1$s"><use data-portrait-asset-use href="#%2$s"></use></g>',
            esc_attr($layerId),
            esc_attr($this->assets->symbolId($layerId))
        );
    }
}

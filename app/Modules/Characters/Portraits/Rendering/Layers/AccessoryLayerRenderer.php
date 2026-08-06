<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Optional class accessory portrait layer.
 */
final class AccessoryLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function __construct(
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    public function priority(): int
    {
        return 35;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $layerId = $context->layer(
            'class_accessory'
        );

        if (! $this->assets->has($layerId)) {
            $layerId = $this->defaultLayerId(
                $context
            );
        }

        return $this->layerMarkup(
            'accessory',
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
            ? $characterClass . '-accessory-01'
            : '';
    }

    private function layerMarkup(
        string $slot,
        string $layerId
    ): string {
        if (! $this->assets->has($layerId)) {
            return sprintf(
                '<g class="gmrc-portrait-layer gmrc-portrait-layer--%1$s" data-portrait-layer="%1$s" hidden></g>',
                esc_attr($slot)
            );
        }

        return sprintf(
            '<g class="gmrc-portrait-layer gmrc-portrait-layer--%1$s" data-portrait-layer="%1$s" data-layer-id="%2$s"><use data-portrait-asset-use href="#%3$s"></use></g>',
            esc_attr($slot),
            esc_attr($layerId),
            esc_attr($this->assets->symbolId($layerId))
        );
    }
}

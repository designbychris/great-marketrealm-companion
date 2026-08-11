<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Render the selected Grand Catalogue heritage above race anatomy.
 */
final class HeritageLayerRenderer implements PortraitLayerRendererInterface
{
    public function __construct(
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    public function priority(): int
    {
        return 22;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $layerId = $context->layer('heritage');

        if (
            $layerId === ''
            || str_ends_with($layerId, '-none')
            || ! $this->assets->has($layerId)
        ) {
            return '<g class="gmrc-portrait-layer '
                . 'gmrc-portrait-layer--heritage" '
                . 'data-portrait-layer="heritage" hidden></g>';
        }

        return sprintf(
            '<g class="gmrc-portrait-layer '
            . 'gmrc-portrait-layer--heritage" '
            . 'data-portrait-layer="heritage" '
            . 'data-layer-id="%1$s">'
            . '<use data-portrait-asset-use '
            . 'href="#%2$s"></use></g>',
            esc_attr($layerId),
            esc_attr(
                $this->assets->symbolId($layerId)
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

final class AssetFrameLayerRenderer implements PortraitLayerRendererInterface
{
    public function __construct(private PortraitSvgAssetLibrary $assets)
    {
    }

    public function priority(): int
    {
        return 50;
    }

    public function render(PortraitRenderContext $context): string
    {
        $layerId = $context->layer('frame');

        if (! $this->assets->has($layerId)) {
            return '<g class="gmrc-portrait-layer gmrc-portrait-layer--frame" data-portrait-layer="frame" hidden><use data-portrait-asset-use></use></g>';
        }

        return sprintf(
            '<g class="gmrc-portrait-layer gmrc-portrait-layer--frame" data-portrait-layer="frame" data-layer-id="%1$s"><use data-portrait-asset-use href="#%2$s"></use></g>',
            esc_attr($layerId),
            esc_attr($this->assets->symbolId($layerId))
        );
    }
}

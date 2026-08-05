<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

final class AssetFaceLayerRenderer implements PortraitLayerRendererInterface
{
    public function __construct(private PortraitSvgAssetLibrary $assets)
    {
    }

    public function priority(): int
    {
        return 25;
    }

    public function render(PortraitRenderContext $context): string
    {
        return $this->layer('eyes', $context->layer('eyes'))
            . "\n"
            . $this->layer('mouth', $context->layer('mouth'));
    }

    private function layer(string $slot, string $layerId): string
    {
        if (! $this->assets->has($layerId)) {
            return sprintf(
                '<g class="gmrc-portrait-layer gmrc-portrait-layer--%1$s" data-portrait-layer="%1$s" hidden><use data-portrait-asset-use></use></g>',
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

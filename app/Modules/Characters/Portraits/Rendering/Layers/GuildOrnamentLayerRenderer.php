<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Optional class or guild ornament portrait layer.
 */
final class GuildOrnamentLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function __construct(
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    public function priority(): int
    {
        return 45;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $layerId = $context->layer(
            'guild_ornament'
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
            ? $characterClass . '-ornament-01'
            : '';
    }

    private function layerMarkup(
        string $layerId
    ): string {
        if (! $this->assets->has($layerId)) {
            return '<g class="gmrc-portrait-layer gmrc-portrait-layer--guild-ornament" data-portrait-layer="guild-ornament" hidden></g>';
        }

        return sprintf(
            '<g class="gmrc-portrait-layer gmrc-portrait-layer--guild-ornament" data-portrait-layer="guild-ornament" data-layer-id="%1$s"><use data-portrait-asset-use href="#%2$s"></use></g>',
            esc_attr($layerId),
            esc_attr($this->assets->symbolId($layerId))
        );
    }
}

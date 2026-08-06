<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Rendering;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\Generation2CollectionResolver;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Render a complete Generation 2 collection from SVG symbols.
 */
final class Generation2PortraitRenderer
{
    public function __construct(
        private Generation2CollectionResolver $collections,
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    public function supports(
        PortraitRenderContext $context
    ): bool {
        return $this->collections->supports(
            $context->race(),
            $context->characterClass()
        );
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        if (! $this->supports($context)) {
            return '';
        }

        $uses = [];

        foreach (
            $this->collections->assetIds(
                $context->race(),
                $context->characterClass()
            ) as $assetId
        ) {
            if (! $this->assets->has($assetId)) {
                continue;
            }

            $uses[] = $this->assets->useMarkup($assetId);
        }

        if ($uses === []) {
            return '';
        }

        return sprintf(
            '<g class="gmrc-portrait-generation-two" '
            . 'data-portrait-generation="2" '
            . 'data-portrait-collection="fructan-grocer">%s</g>',
            implode("\n", $uses)
        );
    }
}

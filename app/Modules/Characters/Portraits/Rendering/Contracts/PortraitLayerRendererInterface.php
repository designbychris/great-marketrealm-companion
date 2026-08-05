<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;

defined('ABSPATH') || exit;

/**
 * Portrait Layer Renderer Contract.
 */
interface PortraitLayerRendererInterface
{
    /**
     * Return the layer's stacking priority.
     */
    public function priority(): int;

    /**
     * Render the SVG layer.
     */
    public function render(
        PortraitRenderContext $context
    ): string;
}

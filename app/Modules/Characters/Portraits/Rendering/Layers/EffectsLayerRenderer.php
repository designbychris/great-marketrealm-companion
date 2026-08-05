<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;

defined('ABSPATH') || exit;

/**
 * Effects Portrait Layer Renderer.
 */
final class EffectsLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function priority(): int
    {
        return 40;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $variant =
            $context->effectsVariant();

        $first = $variant === 2
            ? '❧'
            : '✦';

        $second = $variant === 3
            ? '✺'
            : '✧';

        ob_start();
        ?>
        <g
            class="
                gmrc-portrait-layer
                gmrc-portrait-layer--effects
                gmrc-portrait-layer--variant-<?php
                    echo esc_attr(
                        (string) $variant
                    );
                ?>
            "
            data-portrait-layer="effects"
            data-layer-id="<?php echo esc_attr(
                $context->layer('effects')
            ); ?>"
        >
            <text
                x="92"
                y="150"
                class="gmrc-portrait-layers__spark"
            >
                <?php echo esc_html($first); ?>
            </text>

            <text
                x="375"
                y="290"
                class="gmrc-portrait-layers__spark"
            >
                <?php echo esc_html($second); ?>
            </text>

            <text
                x="105"
                y="390"
                class="gmrc-portrait-layers__spark"
            >
                ✧
            </text>
        </g>
        <?php

        return (string) ob_get_clean();
    }
}

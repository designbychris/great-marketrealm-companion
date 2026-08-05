<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;

defined('ABSPATH') || exit;

/**
 * Body Portrait Layer Renderer.
 */
final class BodyLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function priority(): int
    {
        return 20;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $variant =
            $context->bodyVariant();

        $silhouetteId =
            $context->definitionId(
                'silhouette'
            );

        $shadowId =
            $context->definitionId(
                'shadow'
            );

        $torsoWidth = match ($variant) {
            2 => 138,
            3 => 116,
            default => 126,
        };

        $headWidth = $variant === 3
            ? 82
            : 92;

        $headHeight = $variant === 2
            ? 118
            : 110;

        ob_start();
        ?>
        <g
            class="
                gmrc-portrait-layer
                gmrc-portrait-layer--race
                gmrc-portrait-layer--variant-<?php
                    echo esc_attr(
                        (string) $variant
                    );
                ?>
            "
            data-portrait-layer="race"
            data-layer-id="<?php echo esc_attr(
                $context->layer('body')
            ); ?>"
            filter="url(#<?php echo esc_attr(
                $shadowId
            ); ?>)"
        >
            <ellipse
                cx="240"
                cy="455"
                rx="<?php echo esc_attr(
                    (string) $torsoWidth
                ); ?>"
                ry="92"
                fill="url(#<?php echo esc_attr(
                    $silhouetteId
                ); ?>)"
            />

            <path
                d="
                    M160 430
                    C165 345 190 305 240 295
                    C290 305 315 345 320 430
                    Z
                "
                fill="url(#<?php echo esc_attr(
                    $silhouetteId
                ); ?>)"
            />

            <ellipse
                cx="240"
                cy="225"
                rx="<?php echo esc_attr(
                    (string) $headWidth
                ); ?>"
                ry="<?php echo esc_attr(
                    (string) $headHeight
                ); ?>"
                fill="url(#<?php echo esc_attr(
                    $silhouetteId
                ); ?>)"
            />

            <circle
                cx="207"
                cy="220"
                r="8"
                fill="#f8dfa0"
            />

            <circle
                cx="273"
                cy="220"
                r="8"
                fill="#f8dfa0"
            />

            <path
                d="M211 260 Q240 278 269 260"
                fill="none"
                stroke="#f8dfa0"
                stroke-width="7"
                stroke-linecap="round"
            />

            <text
                x="240"
                y="245"
                text-anchor="middle"
                class="gmrc-portrait-layers__initial"
                data-portrait-initial
            >
                <?php echo esc_html(
                    $context->initial()
                ); ?>
            </text>
        </g>
        <?php

        return (string) ob_get_clean();
    }
}

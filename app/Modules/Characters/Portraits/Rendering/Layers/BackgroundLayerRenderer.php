<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Background Portrait Layer Renderer.
 */
final class BackgroundLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function __construct(
        private ?PortraitSvgAssetLibrary $assets = null
    ) {
    }

    public function priority(): int
    {
        return 10;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $assetLayerId = $context->layer('background');

        if ($this->assets?->has($assetLayerId)) {
            return sprintf(
                '<g class="gmrc-portrait-layer gmrc-portrait-layer--background" data-portrait-layer="background" data-layer-id="%1$s"><use data-portrait-asset-use href="#%2$s"></use></g>',
                esc_attr($assetLayerId),
                esc_attr($this->assets->symbolId($assetLayerId))
            );
        }

        $variant =
            $context->backgroundVariant();

        $gradientId =
            $context->definitionId(
                'background'
            );

        ob_start();
        ?>
        <g
            class="
                gmrc-portrait-layer
                gmrc-portrait-layer--background
                gmrc-portrait-layer--variant-<?php
                    echo esc_attr(
                        (string) $variant
                    );
                ?>
            "
            data-portrait-layer="background"
            data-layer-id="<?php echo esc_attr(
                $context->layer(
                    'background'
                )
            ); ?>"
        >
            <rect
                x="12"
                y="12"
                width="456"
                height="576"
                rx="220"
                fill="url(#<?php echo esc_attr(
                    $gradientId
                ); ?>)"
            />

            <circle
                cx="240"
                cy="235"
                r="175"
                fill="none"
                stroke="#bc8c35"
                stroke-width="4"
                stroke-dasharray="3 12"
                opacity="0.65"
            />

            <path
                d="
                    M55 490
                    C120 430 160 455 205 505
                    C260 445 330 430 425 500
                "
                fill="none"
                stroke="#62744d"
                stroke-width="9"
                stroke-linecap="round"
                opacity="0.44"
            />
        </g>
        <?php

        return (string) ob_get_clean();
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Layers;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Class Portrait Layer Renderer.
 *
 * Draws the provisional outfit and equipment. These shapes will
 * later be replaced by registry-backed class SVG assets.
 */
final class ClassLayerRenderer implements
    PortraitLayerRendererInterface
{
    public function __construct(
        private ?PortraitSvgAssetLibrary $assets = null
    ) {
    }

    public function priority(): int
    {
        return 30;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $outfitLayerId = $context->layer('outfit');
        $equipmentLayerId = $context->layer('equipment');

        if (
            $this->assets?->has($outfitLayerId)
            && $this->assets?->has($equipmentLayerId)
        ) {
            return sprintf(
                '<g class="gmrc-portrait-layer gmrc-portrait-layer--class" data-portrait-layer="class" data-layer-id="%1$s"><use data-portrait-asset-slot="outfit" href="#%2$s"></use><use data-portrait-asset-slot="equipment" href="#%3$s"></use></g>',
                esc_attr($outfitLayerId),
                esc_attr($this->assets->symbolId($outfitLayerId)),
                esc_attr($this->assets->symbolId($equipmentLayerId))
            );
        }

        $outfitVariant =
            $context->outfitVariant();

        $equipmentVariant =
            $context->equipmentVariant();

        $garmentId =
            $context->definitionId(
                'garment'
            );

        ob_start();
        ?>
        <g
            class="
                gmrc-portrait-layer
                gmrc-portrait-layer--class
                gmrc-portrait-layer--variant-<?php
                    echo esc_attr(
                        (string) $outfitVariant
                    );
                ?>
            "
            data-portrait-layer="class"
            data-layer-id="<?php echo esc_attr(
                $context->layer('outfit')
            ); ?>"
        >
            <path
                d="
                    M145 430
                    C175 360 205 340 240 340
                    C275 340 305 360 335 430
                    L365 545
                    L115 545
                    Z
                "
                fill="url(#<?php echo esc_attr(
                    $garmentId
                ); ?>)"
                stroke="#f3d58a"
                stroke-width="5"
            />

            <path
                d="
                    M190 365
                    L240 425
                    L290 365
                "
                fill="none"
                stroke="#f3d58a"
                stroke-width="8"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <circle
                cx="240"
                cy="426"
                r="19"
                fill="#bc8c35"
                stroke="#fff0bd"
                stroke-width="4"
            />

            <?php
            echo $this->equipment(
                $equipmentVariant,
                $context->layer(
                    'equipment'
                )
            );
            ?>
        </g>
        <?php

        return (string) ob_get_clean();
    }

    private function equipment(
        int $variant,
        string $layerId
    ): string {
        ob_start();
        ?>

        <?php if ($variant === 1) : ?>
            <path
                class="gmrc-portrait-layers__equipment"
                data-layer-id="<?php echo esc_attr(
                    $layerId
                ); ?>"
                d="
                    M350 170
                    L366 185
                    L250 390
                    L228 378
                    Z
                "
                fill="#69513f"
                stroke="#35271e"
                stroke-width="5"
            />

            <path
                d="M337 156 L382 202"
                fill="none"
                stroke="#bc8c35"
                stroke-width="13"
                stroke-linecap="round"
            />
        <?php elseif ($variant === 2) : ?>
            <path
                class="gmrc-portrait-layers__equipment"
                data-layer-id="<?php echo esc_attr(
                    $layerId
                ); ?>"
                d="
                    M338 160
                    Q410 290 339 430
                "
                fill="none"
                stroke="#78502c"
                stroke-width="12"
                stroke-linecap="round"
            />

            <path
                d="
                    M338 160
                    Q292 295 339 430
                "
                fill="none"
                stroke="#d7bd7b"
                stroke-width="3"
            />
        <?php else : ?>
            <path
                class="gmrc-portrait-layers__equipment"
                data-layer-id="<?php echo esc_attr(
                    $layerId
                ); ?>"
                d="
                    M348 150
                    L360 450
                "
                fill="none"
                stroke="#68442b"
                stroke-width="12"
                stroke-linecap="round"
            />

            <circle
                cx="348"
                cy="145"
                r="30"
                fill="#7c5790"
                stroke="#efd58c"
                stroke-width="7"
            />
        <?php endif; ?>

        <?php

        return (string) ob_get_clean();
    }
}

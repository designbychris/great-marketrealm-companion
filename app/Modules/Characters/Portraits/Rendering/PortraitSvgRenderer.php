<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Rendering\Generation2PortraitRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Services\PortraitSvgAssetLibrary;

defined('ABSPATH') || exit;

/**
 * Portrait SVG Renderer.
 */
final class PortraitSvgRenderer
{
    public function __construct(
        private PortraitLayerStack $layers,
        private ?PortraitSvgAssetLibrary $assets = null,
        private ?Generation2PortraitRenderer $generationTwo = null
    ) {
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $titleId = $context->definitionId('title');
        $backgroundId = $context->definitionId('background');
        $silhouetteId = $context->definitionId('silhouette');
        $garmentId = $context->definitionId('garment');
        $shadowId = $context->definitionId('shadow');

        $backgroundColours = $context->backgroundColours();
        $bodyColours = $context->bodyColours();
        $outfitColours = $context->outfitColours();

        $generationTwoMarkup = '';

        if (
            $this->generationTwo
                instanceof Generation2PortraitRenderer
            && $this->generationTwo->supports($context)
        ) {
            $generationTwoMarkup =
                $this->generationTwo->render($context);
        }

        ob_start();
        ?>
        <svg
            class="gmrc-portrait-layers"
            viewBox="0 0 480 600"
            role="img"
            aria-labelledby="<?php echo esc_attr($titleId); ?>"
            data-portrait-generation="<?php echo $generationTwoMarkup !== '' ? '2' : '1'; ?>"
        >
            <title id="<?php echo esc_attr($titleId); ?>">
                Guild portrait for
                <?php echo esc_html($context->displayName()); ?>
            </title>

            <defs>
                <?php
                if ($this->assets instanceof PortraitSvgAssetLibrary) {
                    echo $this->assets->definitions(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
                ?>

                <radialGradient
                    id="<?php echo esc_attr($backgroundId); ?>"
                    cx="50%"
                    cy="35%"
                    r="75%"
                >
                    <stop offset="0%" stop-color="<?php echo esc_attr($backgroundColours[0]); ?>"/>
                    <stop offset="70%" stop-color="<?php echo esc_attr($backgroundColours[1]); ?>"/>
                    <stop offset="100%" stop-color="<?php echo esc_attr($backgroundColours[2]); ?>"/>
                </radialGradient>

                <linearGradient
                    id="<?php echo esc_attr($silhouetteId); ?>"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="100%"
                >
                    <stop offset="0%" stop-color="<?php echo esc_attr($bodyColours[0]); ?>"/>
                    <stop offset="100%" stop-color="<?php echo esc_attr($bodyColours[1]); ?>"/>
                </linearGradient>

                <linearGradient
                    id="<?php echo esc_attr($garmentId); ?>"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="100%"
                >
                    <stop offset="0%" stop-color="<?php echo esc_attr($outfitColours[0]); ?>"/>
                    <stop offset="100%" stop-color="<?php echo esc_attr($outfitColours[1]); ?>"/>
                </linearGradient>

                <filter id="<?php echo esc_attr($shadowId); ?>">
                    <feDropShadow
                        dx="0"
                        dy="10"
                        stdDeviation="8"
                        flood-color="#3b2418"
                        flood-opacity="0.3"
                    />
                </filter>
            </defs>

            <?php if ($generationTwoMarkup !== '') : ?>
                <?php
                echo $generationTwoMarkup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            <?php else : ?>
                <?php
                echo $this->layers->render($context); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            <?php endif; ?>
        </svg>
        <?php

        return (string) ob_get_clean();
    }
}

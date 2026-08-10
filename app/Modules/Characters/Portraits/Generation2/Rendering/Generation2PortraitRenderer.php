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
    /**
     * Runtime classes shared by PHP-rendered Ledgers and the live Creator.
     *
     * @var array<string,string>
     */
    private const ASSET_CLASSES = [
        'g2-background-market-garden-01' => 'gmrc-g2-background',
        'g2-fructan-grocer-ground-shadow-01' => 'gmrc-g2-ground-shadow',
        'g2-fructan-body-apple-base-01' => 'gmrc-g2-character gmrc-g2-body',
        'g2-fructan-body-apple-shadow-01' => 'gmrc-g2-character gmrc-g2-body',
        'g2-fructan-body-apple-highlight-01' => 'gmrc-g2-character gmrc-g2-body',
        'g2-fructan-body-apple-blush-01' => 'gmrc-g2-character gmrc-g2-face',
        'g2-fructan-body-apple-speckles-01' => 'gmrc-g2-character gmrc-g2-face',
        'g2-fructan-heritage-apple-leaves-01' => 'gmrc-g2-character gmrc-g2-leaves',
        'g2-fructan-heritage-apple-leaves-shadow-01' => 'gmrc-g2-character gmrc-g2-leaves',
        'g2-fructan-heritage-apple-leaves-highlight-01' => 'gmrc-g2-character gmrc-g2-leaves',
        'g2-fructan-heritage-apple-stem-01' => 'gmrc-g2-character gmrc-g2-stem',
        'g2-brows-friendly-01' => 'gmrc-g2-character gmrc-g2-brows',
        'g2-eyes-auby-bright-01' => 'gmrc-g2-character gmrc-g2-eyes',
        'g2-mouth-auby-smile-01' => 'gmrc-g2-character gmrc-g2-mouth',
        'g2-eyelids-apple-closed-01' => 'gmrc-g2-face-overlay gmrc-g2-eyelids',
        'g2-grocer-shirt-everyday-01' => 'gmrc-g2-character gmrc-g2-outfit',
        'g2-grocer-apron-everyday-01' => 'gmrc-g2-character gmrc-g2-outfit',
        'g2-grocer-outfit-shadow-01' => 'gmrc-g2-character gmrc-g2-outfit',
        'g2-grocer-outfit-highlight-01' => 'gmrc-g2-character gmrc-g2-outfit',
        'g2-grocer-stitching-01' => 'gmrc-g2-character gmrc-g2-outfit',
        'g2-grocer-ledger-01' => 'gmrc-g2-character gmrc-g2-ledger',
        'g2-grocer-satchel-base-01' => 'gmrc-g2-character gmrc-g2-satchel',
        'g2-grocer-satchel-detail-01' => 'gmrc-g2-character gmrc-g2-satchel',
        'g2-grocer-produce-01' => 'gmrc-g2-character gmrc-g2-satchel',
        'g2-grocer-hands-01' => 'gmrc-g2-character gmrc-g2-hands',
        'g2-grocer-boots-01' => 'gmrc-g2-character gmrc-g2-boots',
        'g2-effects-golden-pollen-far-01' => 'gmrc-g2-pollen gmrc-g2-pollen--far',
        'g2-effects-golden-pollen-near-01' => 'gmrc-g2-pollen gmrc-g2-pollen--near',
        'g2-frame-guild-woodland-01' => 'gmrc-g2-frame',
    ];

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

        $before = [];
        $breathing = [];
        $after = [];
        $characterStarted = false;

        foreach (
            $this->collections->assetIds(
                $context->race(),
                $context->characterClass()
            ) as $assetId
        ) {
            /*
             * Auby's old painted cameo has been retired in favour of the
             * physical Seal of Approval rendered by the portrait component.
             */
            if ($assetId === 'g2-auby-finishing-touch-01') {
                continue;
            }

            if (! $this->assets->has($assetId)) {
                continue;
            }

            $classNames = self::ASSET_CLASSES[$assetId] ?? '';

            $markup = $this->assets->useMarkup(
                $assetId,
                $classNames
            );

            $isCharacter = str_contains(
                ' ' . $classNames . ' ',
                ' gmrc-g2-character '
            ) || str_contains(
                ' ' . $classNames . ' ',
                ' gmrc-g2-face-overlay '
            );

            if ($isCharacter) {
                $characterStarted = true;
                $breathing[] = $markup;
                continue;
            }

            if (! $characterStarted) {
                $before[] = $markup;
                continue;
            }

            $after[] = $markup;
        }

        if ($breathing === []) {
            return '';
        }

        return sprintf(
            '<g class="gmrc-portrait-generation-two" '
            . 'data-portrait-generation="2" '
            . 'data-portrait-collection="fructan-grocer">%s'
            . '<g class="gmrc-g2-breathing-group">%s</g>%s</g>',
            implode("\n", $before),
            implode("\n", $breathing),
            implode("\n", $after)
        );
    }
}

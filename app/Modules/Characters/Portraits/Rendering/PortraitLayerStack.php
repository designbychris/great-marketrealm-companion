<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\Contracts\PortraitLayerRendererInterface;

defined('ABSPATH') || exit;

/**
 * Portrait Layer Stack.
 *
 * Orders and renders the individual layers forming a generated
 * Guild portrait.
 */
final class PortraitLayerStack
{
    /**
     * @var array<int,PortraitLayerRendererInterface>
     */
    private array $layers = [];

    /**
     * @param array<int,PortraitLayerRendererInterface> $layers
     */
    public function __construct(
        array $layers = []
    ) {
        foreach ($layers as $layer) {
            $this->add($layer);
        }
    }

    public function add(
        PortraitLayerRendererInterface $layer
    ): self {
        $this->layers[] = $layer;

        return $this;
    }

    public function render(
        PortraitRenderContext $context
    ): string {
        $layers = $this->layers;

        usort(
            $layers,
            static fn (
                PortraitLayerRendererInterface $first,
                PortraitLayerRendererInterface $second
            ): int => $first->priority()
                <=> $second->priority()
        );

        return implode(
            "\n",
            array_map(
                static fn (
                    PortraitLayerRendererInterface $layer
                ): string => $layer->render(
                    $context
                ),
                $layers
            )
        );
    }
}

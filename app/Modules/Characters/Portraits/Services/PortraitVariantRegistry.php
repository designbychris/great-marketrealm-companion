<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitVariantRegistryInterface;

defined('ABSPATH') || exit;

/**
 * Asset-aware Portrait Variant Registry.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitVariantRegistry implements PortraitVariantRegistryInterface
{
    public function __construct(
        private PortraitLayerRegistryInterface $layers,
        private PortraitSvgAssetLibrary $assets
    ) {
    }

    /**
     * @return array<int,string>
     */
    public function variants(
        string $slot,
        string $race = '',
        string $characterClass = ''
    ): array {
        $slot = sanitize_key($slot);

        $available = array_merge(
            $this->layers->shared(),
            $this->layers->forRace($race),
            $this->layers->forClass($characterClass)
        );

        $variants = $available[$slot] ?? [];

        return array_values(
            array_filter(
                array_unique($variants),
                fn ($variant): bool =>
                    is_string($variant)
                    && $variant !== ''
                    && (
                        str_ends_with($variant, '-none')
                        || $this->assets->has($variant)
                    )
            )
        );
    }

    public function defaultFor(
        string $slot,
        string $race = '',
        string $characterClass = ''
    ): ?string {
        return $this->variants(
            $slot,
            $race,
            $characterClass
        )[0] ?? null;
    }

    public function next(
        string $slot,
        string $current,
        string $race = '',
        string $characterClass = ''
    ): ?string {
        return $this->move(
            $this->variants($slot, $race, $characterClass),
            $current,
            1
        );
    }

    public function previous(
        string $slot,
        string $current,
        string $race = '',
        string $characterClass = ''
    ): ?string {
        return $this->move(
            $this->variants($slot, $race, $characterClass),
            $current,
            -1
        );
    }

    public function supports(
        string $slot,
        string $variant,
        string $race = '',
        string $characterClass = ''
    ): bool {
        return in_array(
            $variant,
            $this->variants($slot, $race, $characterClass),
            true
        );
    }

    /**
     * @param array<int,string> $variants
     */
    private function move(
        array $variants,
        string $current,
        int $direction
    ): ?string {
        if ($variants === []) {
            return null;
        }

        $index = array_search($current, $variants, true);

        if ($index === false) {
            return $variants[0];
        }

        $count = count($variants);
        $next = ($index + $direction) % $count;

        if ($next < 0) {
            $next += $count;
        }

        return $variants[$next];
    }
}

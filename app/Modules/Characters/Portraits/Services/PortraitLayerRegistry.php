<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;

defined('ABSPATH') || exit;

/**
 * Portrait Layer Registry.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitLayerRegistry implements PortraitLayerRegistryInterface
{
    /**
     * @return array<string,array<int,string>>
     */
    public function shared(): array
    {
        return [
            'background' => [
                'background-parchment-01',
                'background-market-arch-01',
                'background-guild-hall-01',
            ],
            'eyes' => [
                'eyes-round-01',
                'eyes-bright-01',
                'eyes-determined-01',
            ],
            'mouth' => [
                'mouth-neutral-01',
                'mouth-smile-01',
                'mouth-grin-01',
            ],
            'frame' => [
                'frame-guild-gold-01',
                'frame-vine-gold-01',
                'frame-market-scroll-01',
            ],
            'effects' => [
                'effects-none',
                'effects-gold-motes-01',
                'effects-ink-sparks-01',
            ],
        ];
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function forRace(string $race): array
    {
        $race = sanitize_key($race);

        $expanded =
            PortraitRaceAssetMap::forRace($race);

        $defaults = [
            'body' => $expanded['body'] ?? [
                $race . '-body-01',
                $race . '-body-02',
            ],
            'head' => [
                $race . '-head-01',
                $race . '-head-02',
                $race . '-head-03',
            ],
            'palette' => [
                $race . '-palette-01',
                $race . '-palette-02',
                $race . '-palette-03',
            ],
            'heritage' => $expanded['heritage'] ?? [
                $race . '-heritage-none',
            ],
        ];

        $filtered = apply_filters(
            'gmrc_portrait_race_layers',
            $defaults,
            $race
        );

        return is_array($filtered) ? $filtered : $defaults;
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function forClass(string $characterClass): array
    {
        $characterClass = sanitize_key($characterClass);

        $defaults = [
            'outfit' => [
                $characterClass . '-outfit-01',
                $characterClass . '-outfit-02',
                $characterClass . '-outfit-03',
            ],
            'equipment' => [
                $characterClass . '-equipment-01',
                $characterClass . '-equipment-02',
                $characterClass . '-equipment-03',
            ],
            'class_accessory' => [
                $characterClass . '-accessory-none',
                $characterClass . '-accessory-01',
                $characterClass . '-accessory-02',
            ],
            'class_effects' => [
                $characterClass . '-effects-none',
                $characterClass . '-effects-01',
                $characterClass . '-effects-02',
            ],
            'guild_ornament' => [
                $characterClass . '-ornament-none',
                $characterClass . '-ornament-01',
                $characterClass . '-ornament-02',
            ],
        ];

        $filtered = apply_filters(
            'gmrc_portrait_class_layers',
            $defaults,
            $characterClass
        );

        return is_array($filtered) ? $filtered : $defaults;
    }

    public function supports(
        string $slot,
        string $layerId,
        string $race,
        string $characterClass
    ): bool {
        $available = array_merge(
            $this->shared(),
            $this->forRace($race),
            $this->forClass($characterClass)
        );

        $slot = sanitize_key($slot);

        return isset($available[$slot])
            && in_array($layerId, $available[$slot], true);
    }
}

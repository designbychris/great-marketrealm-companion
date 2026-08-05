<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts;

defined('ABSPATH') || exit;

/**
 * Portrait Layer Registry Contract.
 *
 * Supplies the valid portrait layers available for
 * shared, Race-specific and Class-specific slots.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
interface PortraitLayerRegistryInterface
{
    /**
     * Return shared portrait layer options.
     *
     * @return array<string,array<int,string>>
     */
    public function shared(): array;

    /**
     * Return portrait layers available to a Race.
     *
     * @return array<string,array<int,string>>
     */
    public function forRace(
        string $race
    ): array;

    /**
     * Return portrait layers available to a Class.
     *
     * @return array<string,array<int,string>>
     */
    public function forClass(
        string $characterClass
    ): array;

    /**
     * Determine whether a layer is valid for a slot.
     */
    public function supports(
        string $slot,
        string $layerId,
        string $race,
        string $characterClass
    ): bool;
}

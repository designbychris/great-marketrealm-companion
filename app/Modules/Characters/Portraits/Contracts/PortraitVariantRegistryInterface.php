<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts;

defined('ABSPATH') || exit;

/**
 * Portrait Variant Registry Contract.
 *
 * Exposes selectable portrait variants without requiring callers
 * to understand asset filenames or storage paths.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
interface PortraitVariantRegistryInterface
{
    /**
     * Return all available variants for a portrait slot.
     *
     * @return array<int,string>
     */
    public function variants(
        string $slot,
        string $race = '',
        string $characterClass = ''
    ): array;

    /**
     * Return the deterministic default variant for a slot.
     */
    public function defaultFor(
        string $slot,
        string $race = '',
        string $characterClass = ''
    ): ?string;

    /**
     * Return the variant after the current value, wrapping at the end.
     */
    public function next(
        string $slot,
        string $current,
        string $race = '',
        string $characterClass = ''
    ): ?string;

    /**
     * Return the variant before the current value, wrapping at the start.
     */
    public function previous(
        string $slot,
        string $current,
        string $race = '',
        string $characterClass = ''
    ): ?string;

    /**
     * Determine whether a variant is selectable for a slot.
     */
    public function supports(
        string $slot,
        string $variant,
        string $race = '',
        string $characterClass = ''
    ): bool;
}

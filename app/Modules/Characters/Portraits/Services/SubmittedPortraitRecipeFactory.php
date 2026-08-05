<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitSeed;
use Throwable;

defined('ABSPATH') || exit;

/**
 * Submitted Portrait Recipe Factory.
 *
 * Converts browser-submitted portrait information into a
 * trusted recipe after checking every layer against the
 * server-side registry.
 *
 * Invalid or incomplete submissions return null so Character
 * creation can safely fall back to a server-generated recipe.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class SubmittedPortraitRecipeFactory
{
    /**
     * Required generated portrait slots.
     *
     * @var array<int,string>
     */
    private const REQUIRED_SLOTS = [
        'background',
        'body',
        'head',
        'eyes',
        'mouth',
        'palette',
        'heritage',
        'outfit',
        'equipment',
        'class_accessory',
        'frame',
        'effects',
    ];

    public function __construct(
        private PortraitLayerRegistryInterface $layers
    ) {
    }

    /**
     * Create a trusted recipe from submitted values.
     *
     * @param array{
     *     seed?:mixed,
     *     layers?:mixed
     * } $data
     */
    public function create(
        array $data,
        string $race,
        string $characterClass
    ): ?PortraitRecipe {
        $seed = isset($data['seed'])
            && is_scalar($data['seed'])
                ? strtolower(
                    trim(
                        (string) $data['seed']
                    )
                )
                : '';

        $submittedLayers = is_array(
            $data['layers'] ?? null
        )
            ? $data['layers']
            : [];

        if (
            ! preg_match(
                '/^[a-f0-9]{16}$/',
                $seed
            )
        ) {
            return null;
        }

        $trustedLayers = [];

        foreach (
            self::REQUIRED_SLOTS
            as $slot
        ) {
            $layer = $submittedLayers[$slot]
                ?? null;

            if (! is_scalar($layer)) {
                return null;
            }

            $layer = sanitize_key(
                (string) $layer
            );

            if (
                $layer === ''
                || ! $this->layers->supports(
                    $slot,
                    $layer,
                    $race,
                    $characterClass
                )
            ) {
                return null;
            }

            $trustedLayers[$slot] =
                $layer;
        }

        try {
            return PortraitRecipe::create(
                PortraitSeed::fromString(
                    $seed
                ),
                $trustedLayers
            );
        } catch (Throwable) {
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Contracts\PortraitLayerRegistryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Models\PortraitRecipe;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitSeed;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Deterministic Portrait Recipe Generator.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitRecipeGenerator
{
    public function __construct(
        private PortraitLayerRegistryInterface $layers
    ) {
    }

    public function forCharacter(
        Character $character
    ): PortraitRecipe {
        return $this->generate(
            PortraitSeed::fromCharacterId(
                $character->id()
            ),
            $character->race()->value(),
            $character
                ->characterClass()
                ->value()
        );
    }

    public function generate(
        PortraitSeed $seed,
        string $race,
        string $characterClass
    ): PortraitRecipe {
        $available = array_merge(
            $this->layers->shared(),
            $this->layers->forRace($race),
            $this->layers->forClass(
                $characterClass
            )
        );

        $selected = [];

        foreach ($available as $slot => $options) {
            $options = array_values(
                array_filter(
                    $options,
                    static fn ($option): bool =>
                        is_string($option)
                        && trim($option) !== ''
                )
            );

            if ($options === []) {
                continue;
            }

            $index = $seed->numberFor(
                $slot
            ) % count($options);

            $selected[$slot] =
                $options[$index];
        }

        if ($selected === []) {
            throw new RuntimeException(
                'No portrait layers were available for the generated recipe.'
            );
        }

        return PortraitRecipe::create(
            $seed,
            $selected
        );
    }
}

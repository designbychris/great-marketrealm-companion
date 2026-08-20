<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class DruidCircleProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'druid';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Druid Circle progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'druid',
            'label' => 'Druid Circle',
            'folio_label' => 'Circle Grove Folio',
            'choice_key' => 'druid-circle',
            'selection_level' => 2,
            'description' =>
                'Choose the Great Marketrealm Circle that shapes this Druid’s relationship with growth, decay, flame, soil and transformation.',
        ];
    }
}

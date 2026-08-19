<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class MonkWayProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'monk';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Monastic Way progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'monk',
            'label' => 'Monastic Way',
            'folio_label' => 'Monastic Way Folio',
            'choice_key' => 'monastic-way',
            'selection_level' => 3,
            'description' =>
                'Choose the Way that defines how this Monk turns discipline, movement and martial training into a specialist identity.',
        ];
    }
}

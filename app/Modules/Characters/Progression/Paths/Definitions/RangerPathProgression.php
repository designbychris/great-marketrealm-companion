<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class RangerPathProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'ranger';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Ranger Path progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'ranger',
            'label' => 'Ranger Path',
            'folio_label' => 'Field Path Folio',
            'choice_key' => 'ranger-path',
            'selection_level' => 3,
            'description' =>
                'Choose the field tradition that shapes this Ranger’s specialist hunting, survival and expedition craft.',
        ];
    }
}

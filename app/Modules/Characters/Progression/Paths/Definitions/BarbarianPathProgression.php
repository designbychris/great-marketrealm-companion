<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class BarbarianPathProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'barbarian';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Barbarian Path progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'barbarian',
            'label' => 'Primal Path',
            'folio_label' => 'Primal Path Folio',
            'choice_key' => 'barbarian-primal-path',
            'selection_level' => 3,
            'description' =>
                'Choose the primal Path that shapes this Barbarian’s Rage and adventuring identity.',
        ];
    }
}

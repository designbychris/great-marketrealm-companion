<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class RogueArchetypeProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'rogue';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Rogue Archetype progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'rogue',
            'label' => 'Rogue Archetype',
            'folio_label' => 'Rogue Archetype Folio',
            'choice_key' => 'rogue-archetype',
            'selection_level' => 3,
            'description' =>
                'Choose the specialist Archetype that defines how this Rogue approaches stealth, trickery, precision and opportunity.',
        ];
    }
}

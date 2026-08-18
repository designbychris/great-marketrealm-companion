<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class FighterPathProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'fighter';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Fighter Path progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'fighter',
            'label' => 'Martial Path',
            'folio_label' => 'Martial Path Folio',
            'choice_key' => 'fighter-martial-path',
            'selection_level' => 3,
            'description' =>
                'Choose the specialised martial path that shapes this Fighter’s training and battlefield identity.',
        ];
    }
}

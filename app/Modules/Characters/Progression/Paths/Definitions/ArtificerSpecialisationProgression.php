<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist Artificer Specialisation selection boundary.
 */
final class ArtificerSpecialisationProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'artificer';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Artificer Specialisation progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'artificer',
            'label' => 'Artificer Specialisation',
            'folio_label' => 'Specialist Workshop Folio',
            'choice_key' => 'artificer-specialisation',
            'selection_level' => 3,
            'description' =>
                'Choose the Great Marketrealm Artificer Specialisation that shapes this inventor’s workshop practice, magical craft and specialist creations.',
        ];
    }
}

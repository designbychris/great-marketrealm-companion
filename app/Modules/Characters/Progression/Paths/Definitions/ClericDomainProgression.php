<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class ClericDomainProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'cleric';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Cleric Domain progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'cleric',
            'label' => 'Divine Domain',
            'folio_label' => 'Sacred Domain Folio',
            'choice_key' => 'cleric-domain',
            'selection_level' => 1,
            'description' =>
                'Choose the Great Marketrealm Divine Domain that shapes this Cleric’s sacred duties, rites and miracles.',
        ];
    }
}

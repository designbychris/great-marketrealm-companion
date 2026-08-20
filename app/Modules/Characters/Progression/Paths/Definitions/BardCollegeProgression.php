<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Specialist Bard College selection boundary.
 */
final class BardCollegeProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'bard';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Bard College progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'bard',
            'label' => 'Bard College',
            'folio_label' => 'College Performance Folio',
            'choice_key' => 'bard-college',
            'selection_level' => 3,
            'description' =>
                'Choose the Great Marketrealm Bard College whose performance tradition shapes this Bard’s specialist repertoire.',
        ];
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class WizardPathProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'wizard';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Wizard Path progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'wizard',
            'label' => 'Arcane Tradition',
            'folio_label' =>
                'Arcane Tradition Folio',
            'choice_key' =>
                'wizard-arcane-tradition',
            'selection_level' => 2,
            'description' =>
                'Choose the school or tradition that shapes this Wizard’s deeper magical studies.',
        ];
    }
}

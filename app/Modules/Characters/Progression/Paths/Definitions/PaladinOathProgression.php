<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class PaladinOathProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'paladin';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Sacred Oath progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'paladin',
            'label' => 'Sacred Oath',
            'folio_label' => 'Sacred Oath Folio',
            'choice_key' => 'sacred-oath',
            'selection_level' => 3,
            'description' =>
                'Choose the Sacred Oath that defines this Paladin’s vows, duties, specialist identity and later Oath Gifts.',
        ];
    }
}

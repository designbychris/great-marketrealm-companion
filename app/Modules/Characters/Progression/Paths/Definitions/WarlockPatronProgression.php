<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class WarlockPatronProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'warlock';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Warlock Patron progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'warlock',
            'label' => 'Otherworldly Patron',
            'folio_label' => 'Patron Contract Folio',
            'choice_key' => 'otherworldly-patron',
            'selection_level' => 1,
            'description' =>
                'Choose the supernatural Patron whose bargain defines this Warlock’s specialist identity and later Patron Gifts.',
        ];
    }
}

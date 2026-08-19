<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class SorcererOriginProgression implements PathProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return $class->value() === 'sorcerer';
    }

    /** @return array<string,mixed> */
    public function definition(
        CharacterClass $class
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'Sorcerous Origin progression cannot resolve another Calling.'
            );
        }

        return [
            'class' => 'sorcerer',
            'label' => 'Sorcerous Origin',
            'folio_label' => 'Origin Spark Folio',
            'choice_key' => 'sorcerous-origin',
            'selection_level' => 1,
            'description' =>
                'Choose the supernatural source whose power is already alive within this Sorcerer and whose later gifts mature at higher levels.',
        ];
    }
}

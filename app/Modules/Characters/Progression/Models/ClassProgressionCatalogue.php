<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Models;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class ClassProgressionCatalogue
{
    /** @return array<int,string> */
    public function classes(): array
    {
        return CharacterClass::identifiers();
    }

    public function supports(CharacterClass $class): bool
    {
        return in_array(
            $class->value(),
            $this->classes(),
            true
        );
    }

    /**
     * Foundation entry for a class level.
     *
     * Phase III.8.1 deliberately leaves class-specific automatic gains and
     * player choices empty rather than inventing rules not yet imported into
     * the Marketrealm progression catalogue.
     *
     * @return array<string,mixed>
     */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        if (! $this->supports($class)) {
            throw new InvalidArgumentException(
                'The class is not registered for advancement.'
            );
        }

        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }

        return [
            'class' => $class->value(),
            'label' => $class->label(),
            'level' => $level,
            'automatic' => [],
            'choices' => [],
            'catalogue_status' => 'foundation',
        ];
    }
}

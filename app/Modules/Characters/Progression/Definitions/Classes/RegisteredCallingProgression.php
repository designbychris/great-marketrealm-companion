<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Contracts\ClassProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class RegisteredCallingProgression implements ClassProgressionDefinitionInterface
{
    public function supports(
        CharacterClass $class
    ): bool {
        return true;
    }

    /** @return array<string,mixed> */
    public function forLevel(
        CharacterClass $class,
        int $level
    ): array {
        $this->guardLevel($level);

        return [
            'class' => $class->value(),
            'label' => $class->label(),
            'level' => $level,
            'automatic' => [],
            'delegated' => [],
            'catalogue_status' => 'registered',
        ];
    }

    private function guardLevel(int $level): void
    {
        if ($level < 2 || $level > 20) {
            throw new InvalidArgumentException(
                'Advancement catalogue levels must be between 2 and 20.'
            );
        }
    }
}

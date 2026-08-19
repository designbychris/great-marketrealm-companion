<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Certified Rogue precision policy shared by Ledger presenters.
 */
final class RoguePrecisionPolicy
{
    public function sneakAttackDice(
        Character $character
    ): string {
        $this->assertRogue($character);

        $dice = (int) ceil(
            max(
                1,
                $character
                    ->level()
                    ->value()
            ) / 2
        );

        return $dice . 'd6';
    }

    private function assertRogue(
        Character $character
    ): void {
        if (
            $character
                ->characterClass()
                ->value()
            === 'rogue'
        ) {
            return;
        }

        throw new InvalidArgumentException(
            'Rogue precision policy requires a Rogue Character.'
        );
    }
}

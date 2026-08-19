<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\PaladinSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Player-facing Paladin Sacred Actions.
 */
final class PaladinSacredActionPresenter
{
    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        ActiveClassResourceState $resources
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'paladin'
        ) {
            return [
                'supported' => false,
                'smite_options' => [],
            ];
        }

        $level = $character
            ->level()
            ->value();

        $sacred =
            new PaladinSacredReserveService();

        $slots =
            (new SharedSpellSlotReserveService())
                ->present(
                    $character,
                    $resources
                );

        $smiteOptions = [];

        if ($level >= 2) {
            foreach ($slots as $slot) {
                $slotLevel =
                    (int) $slot['level'];

                $dice = min(
                    5,
                    1 + $slotLevel
                );

                $smiteOptions[] = [
                    'slot_level' =>
                        $slotLevel,
                    'remaining' =>
                        (int) $slot['remaining'],
                    'total' =>
                        (int) $slot['total'],
                    'available' =>
                        (int) $slot['remaining'] > 0,
                    'formula' =>
                        $dice . 'd8',
                    'label' =>
                        sprintf(
                            'Level %d slot · %dd8',
                            $slotLevel,
                            $dice
                        ),
                ];
            }
        }

        return [
            'supported' => true,
            'lay_on_hands' => [
                'available' =>
                    $sacred->remaining(
                        $character,
                        $resources,
                        PaladinSacredReserveService::LAY_ON_HANDS
                    ) > 0,
                'remaining' =>
                    $sacred->remaining(
                        $character,
                        $resources,
                        PaladinSacredReserveService::LAY_ON_HANDS
                    ),
            ],
            'divine_sense' => [
                'available' =>
                    $sacred->remaining(
                        $character,
                        $resources,
                        PaladinSacredReserveService::DIVINE_SENSE
                    ) > 0,
            ],
            'cleansing_touch' => [
                'unlocked' =>
                    $level >= 14,
                'available' =>
                    $level >= 14
                    && $sacred->remaining(
                        $character,
                        $resources,
                        PaladinSacredReserveService::CLEANSING_TOUCH
                    ) > 0,
            ],
            'divine_smite' => [
                'unlocked' =>
                    $level >= 2,
                'qualification' =>
                    'Spend only after the table confirms a qualifying melee weapon hit.',
                'damage_type' =>
                    'radiant',
                'smite_options' =>
                    $smiteOptions,
            ],
        ];
    }
}

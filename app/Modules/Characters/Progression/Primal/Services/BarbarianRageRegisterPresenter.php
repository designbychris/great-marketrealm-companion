<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\BarbarianRageReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Barbarian progression state for the Character Ledger.
 *
 * III.12.3A derives certified capability from Character level and the
 * persisted Primal Path. It deliberately does not track spent Rage uses or
 * whether Rage is currently active; those belong to III.12.3C.
 */
final class BarbarianRageRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Reckless Attack & Danger Sense',
            'detail' =>
                'Aggression and instinct become specialist Barbarian tools.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Primal Path',
            'detail' =>
                'Choose the Path that shapes this Barbarian’s Rage.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Extra Attack & Fast Movement',
            'detail' =>
                'The Barbarian attacks twice and moves with greater speed.',
        ],
        7 => [
            'level' => 7,
            'label' => 'Feral Instinct',
            'detail' =>
                'Battlefield instinct sharpens at the opening of combat.',
        ],
        9 => [
            'level' => 9,
            'label' => 'Brutal Critical',
            'detail' =>
                'Critical weapon hits gain one additional weapon damage die.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Relentless Rage',
            'detail' =>
                'Rage becomes difficult to extinguish at the brink of defeat.',
        ],
        13 => [
            'level' => 13,
            'label' => 'Brutal Critical II',
            'detail' =>
                'Critical weapon hits gain two additional weapon damage dice.',
        ],
        15 => [
            'level' => 15,
            'label' => 'Persistent Rage',
            'detail' =>
                'Rage becomes much harder to end prematurely.',
        ],
        17 => [
            'level' => 17,
            'label' => 'Brutal Critical III',
            'detail' =>
                'Critical weapon hits gain three additional weapon damage dice.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Indomitable Might',
            'detail' =>
                'Raw Strength becomes exceptionally dependable when tested.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Primal Champion',
            'detail' =>
                'The Barbarian reaches the summit of primal physical power.',
        ],
    ];

    public function __construct(
        private ?PathCandidateCatalogue $paths = null,
        private ?PathGiftCatalogue $gifts = null
    ) {
        $this->paths ??=
            new PathCandidateCatalogue();

        $this->gifts ??=
            new PathGiftCatalogue();
    }

    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        ?ActiveClassResourceState $resources = null,
        ?ActiveClassConditionState $conditions = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'barbarian'
        ) {
            return [
                'supported' => false,
            ];
        }

        $level = $character->level()->value();

        $resources ??=
            ActiveClassResourceState::fresh();

        $conditions ??=
            ActiveClassConditionState::fresh();

        $rageReserves =
            new BarbarianRageReserveService();

        $unlimited =
            $rageReserves->unlimited(
                $character
            );

        $maximum =
            $rageReserves->maximum(
                $character
            );

        return [
            'supported' => true,
            'level' => $level,
            'rage' => [
                'uses' => $maximum,
                'maximum' => $maximum,
                'unlimited' => $unlimited,
                'expended' =>
                    $resources->expended(
                        'rage'
                    ),
                'remaining' =>
                    $unlimited
                        ? null
                        : $resources->remaining(
                            'rage',
                            $maximum
                        ),
                'active' =>
                    $conditions->active(
                        'rage'
                    ),
                'damage_bonus' =>
                    $this->rageDamageBonus(
                        $level
                    ),
                'duration' => '1 minute',
                'activation' => 'Bonus action',
                'refresh' => 'Long rest',
            ],
            'attacks_per_action' =>
                $level >= 5 ? 2 : 1,
            'speed_bonus' =>
                $level >= 5 ? 10 : 0,
            'brutal_critical_dice' =>
                $this->brutalCriticalDice(
                    $level
                ),
            'features' => [
                [
                    'key' => 'reckless-attack',
                    'label' => 'Reckless Attack',
                    'level' => 2,
                    'unlocked' => $level >= 2,
                    'detail' =>
                        'Trade caution for a more aggressive opening in battle.',
                ],
                [
                    'key' => 'danger-sense',
                    'label' => 'Danger Sense',
                    'level' => 2,
                    'unlocked' => $level >= 2,
                    'detail' =>
                        'Instinct sharpens reactions to sudden visible danger.',
                ],
                [
                    'key' => 'feral-instinct',
                    'label' => 'Feral Instinct',
                    'level' => 7,
                    'unlocked' => $level >= 7,
                    'detail' =>
                        'The Barbarian’s combat instincts sharpen further.',
                ],
                [
                    'key' => 'relentless-rage',
                    'label' => 'Relentless Rage',
                    'level' => 11,
                    'unlocked' => $level >= 11,
                    'detail' =>
                        'Rage can carry the Barbarian through a devastating blow.',
                ],
                [
                    'key' => 'persistent-rage',
                    'label' => 'Persistent Rage',
                    'level' => 15,
                    'unlocked' => $level >= 15,
                    'detail' =>
                        'Rage becomes far harder to end before the Barbarian chooses.',
                ],
                [
                    'key' => 'indomitable-might',
                    'label' => 'Indomitable Might',
                    'level' => 18,
                    'unlocked' => $level >= 18,
                    'detail' =>
                        'Strength checks can rely on the Barbarian’s extraordinary physical power.',
                ],
                [
                    'key' => 'primal-champion',
                    'label' => 'Primal Champion',
                    'level' => 20,
                    'unlocked' => $level >= 20,
                    'detail' =>
                        'The Barbarian reaches the peak of primal might.',
                ],
            ],
            'path' =>
                $this->pathState(
                    $character
                ),
            'path_gifts' =>
                $this->certifiedPathGifts(
                    $character
                ),
            'next_milestone' =>
                $this->nextMilestone($level),
            'milestones' =>
                array_values(self::MILESTONES),
        ];
    }

    private function rageDamageBonus(
        int $level
    ): int {
        if ($level >= 16) {
            return 4;
        }

        if ($level >= 9) {
            return 3;
        }

        return 2;
    }

    private function brutalCriticalDice(
        int $level
    ): int {
        if ($level >= 17) {
            return 3;
        }

        if ($level >= 13) {
            return 2;
        }

        return $level >= 9
            ? 1
            : 0;
    }

    /**
     * @return array<string,mixed>
     */
    private function pathState(
        Character $character
    ): array {
        $level = $character->level()->value();
        $chosen = $character
            ->callingPath()
            ->isChosen();

        if (! $chosen) {
            return [
                'chosen' => false,
                'available' => $level >= 3,
                'key' => '',
                'label' => $level >= 3
                    ? 'Awaiting Primal Path'
                    : 'Opens at Level 3',
            ];
        }

        $key = $character
            ->callingPath()
            ->value();

        $label = ucwords(
            str_replace('-', ' ', $key)
        );

        foreach (
            $this->paths->forClass(
                $character->characterClass()
            )
            as $candidate
        ) {
            if (
                (string) (
                    $candidate['key']
                    ?? ''
                )
                === $key
            ) {
                $label = (string) (
                    $candidate['label']
                    ?? $label
                );

                break;
            }
        }

        return [
            'chosen' => true,
            'available' => true,
            'key' => $key,
            'label' => $label,
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function certifiedPathGifts(
        Character $character
    ): array {
        if (! $character->callingPath()->isChosen()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->gifts->all(
                    $character->callingPath()->value()
                ),
                fn (array $gift): bool =>
                    $character->pathGifts()->has(
                        (string) ($gift['key'] ?? '')
                    )
            )
        );
    }

    /**
     * @return array<string,mixed>|null
     */
    private function nextMilestone(
        int $level
    ): ?array {
        foreach (self::MILESTONES as $milestone) {
            if (
                $milestone['level']
                > $level
            ) {
                return $milestone;
            }
        }

        return null;
    }
}

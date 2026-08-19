<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Warlock Patron Contract Register for III.12.7A.
 */
final class WarlockPatronRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Eldritch Invocations',
            'detail' =>
                'The first Invocation choices become available.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Pact Boon',
            'detail' =>
                'The bargain deepens into a distinct Pact Boon.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Patron Gift',
            'detail' =>
                'The chosen Patron grants its next specialist gift.',
        ],
        9 => [
            'level' => 9,
            'label' => '5th-level Pact Magic',
            'detail' =>
                'Pact Magic reaches its highest ordinary slot level.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Mystic Arcanum',
            'detail' =>
                'A 6th-level Mystic Arcanum opens and the Pact slot reserve expands.',
        ],
        13 => [
            'level' => 13,
            'label' => '7th-level Mystic Arcanum',
            'detail' =>
                'A second high-circle Arcanum becomes available.',
        ],
        15 => [
            'level' => 15,
            'label' => '8th-level Mystic Arcanum',
            'detail' =>
                'The Warlock’s Arcanum ladder advances again.',
        ],
        17 => [
            'level' => 17,
            'label' => '9th-level Mystic Arcanum',
            'detail' =>
                'The final Mystic Arcanum opens and Pact slots reach four.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Eldritch Master',
            'detail' =>
                'The Warlock reaches the Calling’s final pact mastery.',
        ],
    ];

    public function __construct(
        private ?WarlockPatronPolicy $policy = null,
        private ?PathCandidateCatalogue $paths = null
    ) {
        $this->policy ??=
            new WarlockPatronPolicy();

        $this->paths ??=
            new PathCandidateCatalogue();
    }

    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'warlock'
        ) {
            return [
                'supported' => false,
            ];
        }

        $level = $character
            ->level()
            ->value();

        return [
            'supported' => true,
            'level' => $level,
            'patron' =>
                $this->patronState(
                    $character
                ),
            'pact_magic' => [
                'slot_level' =>
                    $this->policy
                        ->pactSlotLevel(
                            $character
                        ),
                'slots' =>
                    $this->policy
                        ->pactSlots(
                            $character
                        ),
                'refresh' =>
                    'Short or long rest',
            ],
            'invocations' => [
                'known' =>
                    $this->policy
                        ->invocationsKnown(
                            $character
                        ),
                'unlocked' =>
                    $level >= 2,
            ],
            'pact_boon' => [
                'unlocked' =>
                    $level >= 3,
                'label' =>
                    $level >= 3
                        ? 'Available'
                        : 'Opens at Level 3',
            ],
            'mystic_arcanum' => [
                'levels' =>
                    $this->policy
                        ->mysticArcanumLevels(
                            $character
                        ),
                'unlocked' =>
                    $level >= 11,
            ],
            'spell_save_dc' =>
                $this->policy
                    ->pactSaveDc(
                        $character
                    ),
            'spell_attack' =>
                $this->policy
                    ->pactAttackBonus(
                        $character
                    ),
            'eldritch_master' => [
                'unlocked' =>
                    $level >= 20,
            ],
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function patronState(
        Character $character
    ): array {
        if (
            ! $character
                ->callingPath()
                ->isChosen()
        ) {
            return [
                'chosen' => false,
                'key' => '',
                'label' =>
                    'Awaiting Patron Contract',
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
                ) === $key
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
            'key' => $key,
            'label' => $label,
        ];
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

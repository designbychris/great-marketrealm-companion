<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Monk progression state for the Character Ledger.
 */
final class MonkDisciplineRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Discipline',
            'detail' =>
                'The Monk gains a level-scaled Discipline pool and faster movement.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Monastic Way',
            'detail' =>
                'Choose the Way that shapes this Monk’s specialist identity.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Stunning Strike',
            'detail' =>
                'Discipline can be channelled through a successful melee strike.',
        ],
        7 => [
            'level' => 7,
            'label' => 'Evasion',
            'detail' =>
                'Reflex training improves survival against qualifying area effects.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Diamond Soul',
            'detail' =>
                'The Monk’s discipline fortifies every saving throw.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Empty Body',
            'detail' =>
                'Deep Discipline can fuel an extraordinary advanced state.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Perfect Self',
            'detail' =>
                'The Monk reaches the summit of disciplined mastery.',
        ],
    ];

    public function __construct(
        private ?PathCandidateCatalogue $paths = null,
        private ?MonkDisciplinePolicy $policy = null
    ) {
        $this->paths ??=
            new PathCandidateCatalogue();

        $this->policy ??=
            new MonkDisciplinePolicy();
    }

    /**
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        ?ActiveClassResourceState $resources = null
    ): array {
        $resources ??=
            ActiveClassResourceState::fresh();
        if (
            $character
                ->characterClass()
                ->value()
            !== 'monk'
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
            'discipline' => [
                'unlocked' => $level >= 2,
                'maximum' =>
                    $this->policy->maximum(
                        $character
                    ),
                'remaining' =>
                    (new MonkDisciplineReserveService(
                        $this->policy
                    ))->remaining(
                        $character,
                        $resources
                    ),
                'expended' =>
                    $resources->expended(
                        MonkDisciplineReserveService::RESOURCE
                    ),
                'save_dc' =>
                    $this->policy->saveDc(
                        $character
                    ),
                'refresh' =>
                    'Short or long rest',
            ],
            'movement' => [
                'bonus_feet' =>
                    $this->policy
                        ->movementBonusFeet(
                            $character
                        ),
                'unlocked' => $level >= 2,
            ],
            'features' => [
                $this->feature(
                    'deflect-missiles',
                    'Deflect Missiles',
                    3,
                    $level,
                    'A trained reaction against incoming ranged weapon attacks.'
                ),
                $this->feature(
                    'slow-fall',
                    'Slow Fall',
                    4,
                    $level,
                    'Reduce dangerous falling impact through trained control.'
                ),
                $this->feature(
                    'stunning-strike',
                    'Stunning Strike',
                    5,
                    $level,
                    'Channel Discipline through a successful melee strike.'
                ),
                $this->feature(
                    'disciplined-strikes',
                    'Disciplined Strikes',
                    6,
                    $level,
                    'Specialist strikes overcome increasingly unusual resistance.'
                ),
                $this->feature(
                    'evasion',
                    'Evasion',
                    7,
                    $level,
                    'Qualifying Dexterity-save area effects become easier to survive.'
                ),
                $this->feature(
                    'stillness-of-mind',
                    'Stillness of Mind',
                    7,
                    $level,
                    'Disciplined focus can break certain effects that seize the mind.'
                ),
                $this->feature(
                    'diamond-soul',
                    'Diamond Soul',
                    14,
                    $level,
                    'Inner discipline fortifies every saving throw.'
                ),
                $this->feature(
                    'empty-body',
                    'Empty Body',
                    18,
                    $level,
                    'Deep Discipline can fuel an extraordinary advanced state.'
                ),
                $this->feature(
                    'perfect-self',
                    'Perfect Self',
                    20,
                    $level,
                    'The Monk reaches the height of disciplined mastery.'
                ),
            ],
            'way' =>
                $this->wayState(
                    $character
                ),
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function feature(
        string $key,
        string $label,
        int $requiredLevel,
        int $level,
        string $detail
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'level' => $requiredLevel,
            'unlocked' =>
                $level >= $requiredLevel,
            'detail' => $detail,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function wayState(
        Character $character
    ): array {
        $level = $character
            ->level()
            ->value();

        if (
            ! $character
                ->callingPath()
                ->isChosen()
        ) {
            return [
                'chosen' => false,
                'available' => $level >= 3,
                'key' => '',
                'label' => $level >= 3
                    ? 'Awaiting Monastic Way'
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
            'available' => true,
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
            if ($milestone['level'] > $level) {
                return $milestone;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Bard College Register for active-play orientation.
 */
final class BardCollegeRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Jack of All Trades & Song of Rest',
            'detail' =>
                'Versatility broadens and restorative performance awakens with a d6.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Bard College & Expertise',
            'detail' =>
                'Choose a College and deepen two mastered skills through Expertise.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Font of Inspiration',
            'detail' =>
                'Bardic Inspiration becomes d8 and begins refreshing after short rests.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Countercharm & College Gift',
            'detail' =>
                'Performance can guard against charm and fear while the College reaches another milestone.',
        ],
        9 => [
            'level' => 9,
            'label' => 'Song of Rest d8',
            'detail' =>
                'Restorative performance improves to a d8.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Bardic Inspiration d10 & Magical Secrets',
            'detail' =>
                'Inspiration grows to d10 and the Bard begins learning magic beyond the normal repertoire.',
        ],
        13 => [
            'level' => 13,
            'label' => 'Song of Rest d10',
            'detail' =>
                'Restorative performance improves to a d10.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Final College Gift & Magical Secrets',
            'detail' =>
                'The College reaches its final specialist milestone and Magical Secrets expands again.',
        ],
        15 => [
            'level' => 15,
            'label' => 'Bardic Inspiration d12',
            'detail' =>
                'The Bardic Inspiration die reaches d12.',
        ],
        17 => [
            'level' => 17,
            'label' => 'Song of Rest d12',
            'detail' =>
                'Restorative performance reaches its final d12.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Final Magical Secrets',
            'detail' =>
                'The Bard learns the final pair of Magical Secrets.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Superior Inspiration',
            'detail' =>
                'The Calling reaches its final Inspiration safeguard.',
        ],
    ];

    public function __construct(
        private ?BardPerformancePolicy $policy = null,
        private ?PathCandidateCatalogue $colleges = null,
        private ?PathGiftCatalogue $gifts = null
    ) {
        $this->policy ??=
            new BardPerformancePolicy();

        $this->colleges ??=
            new PathCandidateCatalogue();

        $this->gifts ??=
            new PathGiftCatalogue();
    }

    /** @return array<string,mixed> */
    public function present(
        Character $character,
        ?ActiveClassResourceState $resources = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'bard'
        ) {
            return [
                'supported' => false,
            ];
        }

        $resources ??=
            ActiveClassResourceState::fresh();

        $level = $character
            ->level()
            ->value();

        $college = $character
            ->callingPath()
            ->value();

        $candidates =
            $this->colleges->forClass(
                $character->characterClass()
            );

        $giftCount = $college !== ''
            && $this->gifts->supports($college)
                ? count(
                    $this->gifts->all($college)
                )
                : 0;

        return [
            'supported' => true,
            'level' => $level,
            'college' => [
                'selection_level' => 3,
                'available' => $level >= 3,
                'chosen' => $college !== '',
                'key' => $college,
                'label' =>
                    $this->collegeLabel(
                        $college,
                        $candidates
                    ),
                'candidate_count' =>
                    count($candidates),
                'candidates' =>
                    $candidates,
                'gift_count' =>
                    $giftCount,
                'gift_status' =>
                    $giftCount > 0
                        ? 'College Gifts certified'
                        : 'College Gifts await their dedicated phase',
            ],
            'inspiration' => [
                'die' =>
                    $this->policy
                        ->inspirationDie(
                            $character
                        ),
                'maximum' =>
                    $this->policy
                        ->inspirationMaximum(
                            $character
                        ),
                'refresh' =>
                    $this->policy
                        ->inspirationRefresh(
                            $character
                        ),
                'resource_tracking' => false,
                'font_unlocked' => $level >= 5,
            ],
            'song_of_rest' => [
                'unlocked' => $level >= 2,
                'die' =>
                    $this->policy
                        ->songOfRestDie(
                            $character
                        ),
            ],
            'expertise' => [
                'first_unlocked' => $level >= 3,
                'second_unlocked' => $level >= 10,
            ],
            'countercharm' => [
                'unlocked' => $level >= 6,
            ],
            'magical_secrets' => [
                'unlocked' => $level >= 10,
                'pairs' =>
                    match (true) {
                        $level >= 18 => 3,
                        $level >= 14 => 2,
                        $level >= 10 => 1,
                        default => 0,
                    },
            ],
            'spellcasting' => [
                'unlocked' => true,
                'model' => 'known-spells',
                'ability' => 'Charisma',
                'spells_known' =>
                    $this->policy
                        ->spellsKnown(
                            $character
                        ),
                'cantrips_known' =>
                    $this->policy
                        ->cantripsKnown(
                            $character
                        ),
                'maximum_spell_level' =>
                    $this->policy
                        ->maximumSpellLevel(
                            $character
                        ),
                'save_dc' =>
                    $this->policy
                        ->spellSaveDc(
                            $character
                        ),
                'spell_attack' =>
                    $this->policy
                        ->spellAttackBonus(
                            $character
                        ),
                'slots' => (
                    new SharedSpellSlotReserveService()
                )->present(
                    $character,
                    $resources
                ),
            ],
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $candidates
     */
    private function collegeLabel(
        string $college,
        array $candidates
    ): string {
        if ($college === '') {
            return 'College not yet chosen';
        }

        foreach ($candidates as $candidate) {
            if (
                ($candidate['key'] ?? '')
                === $college
            ) {
                return (string) (
                    $candidate['label']
                    ?? $college
                );
            }
        }

        return ucwords(
            str_replace(
                '-',
                ' ',
                $college
            )
        );
    }

    /** @return array<string,mixed>|null */
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

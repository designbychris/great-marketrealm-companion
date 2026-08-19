<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Ranger Field Register for III.12.9A.
 */
final class RangerFieldRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Fighting Style & Spellcasting',
            'detail' =>
                'The Ranger adopts a fighting style and begins Wisdom-based half-caster spellcasting.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Primeval Awareness',
            'detail' =>
                'Field instincts sharpen into supernatural awareness.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Extra Attack',
            'detail' =>
                'The Ranger can attack twice when taking the Attack action.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Favoured Mark Improvement',
            'detail' =>
                'The Ranger’s quarry knowledge and tracking expertise deepen.',
        ],
        8 => [
            'level' => 8,
            'label' => 'Land’s Stride',
            'detail' =>
                'Fieldcraft makes difficult natural terrain easier to cross.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Hide in Plain Sight',
            'detail' =>
                'The Ranger’s concealment skills mature.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Vanish & Favoured Mark Improvement',
            'detail' =>
                'Mobility and quarry expertise reach another major threshold.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Feral Senses',
            'detail' =>
                'The Ranger can locate threats ordinary sight would miss.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Foe Slayer',
            'detail' =>
                'The Ranger’s quarry instincts become decisive precision.',
        ],
    ];

    public function __construct(
        private ?RangerFieldPolicy $policy = null,
        private ?SpellcastingProgressionCatalogue $spellcasting = null,
        private ?PathCandidateCatalogue $paths = null,
        private ?PathProgressionCatalogue $pathProgression = null
    ) {
        $this->policy ??=
            new RangerFieldPolicy();

        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();

        $this->paths ??=
            new PathCandidateCatalogue();

        $this->pathProgression ??=
            new PathProgressionCatalogue();
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
            !== 'ranger'
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

        $casting = $level >= 2
            ? $this->spellcasting
                ->forLevel(
                    $character->characterClass(),
                    $level
                )
            : null;

        $slots = $level >= 2
            ? (
                new SharedSpellSlotReserveService()
            )->present(
                $character,
                $resources
            )
            : [];

        $pathCandidates =
            $this->paths->forClass(
                $character->characterClass()
            );

        return [
            'supported' => true,
            'level' => $level,
            'favoured_mark' => [
                'label' => 'Favoured Mark',
                'stage' =>
                    $this->policy
                        ->favouredMarkStage(
                            $character
                        ),
                'detail' =>
                    'Study a quarry and mark the signs needed to track it through the Marketrealm.',
                'resource_tracking' =>
                    false,
            ],
            'natural_explorer' => [
                'label' =>
                    'Natural Explorer',
                'detail' =>
                    'Travel, scouting and survival remain core Ranger fieldcraft.',
            ],
            'extra_attack' => [
                'unlocked' =>
                    $this->policy
                        ->extraAttackUnlocked(
                            $character
                        ),
                'attacks' =>
                    $this->policy
                        ->extraAttackUnlocked(
                            $character
                        )
                        ? 2
                        : 1,
            ],
            'spellcasting' => [
                'unlocked' =>
                    $level >= 2,
                'model' =>
                    (string) (
                        $casting['model']
                        ?? 'known-spells'
                    ),
                'spells_known' =>
                    (int) (
                        $casting['spells_known']
                        ?? 0
                    ),
                'cantrips_known' =>
                    (int) (
                        $casting['cantrips_known']
                        ?? 0
                    ),
                'maximum_spell_level' =>
                    (int) (
                        $casting['maximum_spell_level']
                        ?? 0
                    ),
                'slots' => $slots,
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
                'ability' =>
                    'Wisdom',
            ],
            'path' => [
                'registered' =>
                    $this->pathProgression
                        ->forClass(
                            $character->characterClass()
                        ) !== null,
                'candidate_count' =>
                    count($pathCandidates),
                'candidates' =>
                    $pathCandidates,
                'status' =>
                    $pathCandidates === []
                        ? 'Awaiting Ranger path catalogue'
                        : 'Ranger paths available',
            ],
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
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

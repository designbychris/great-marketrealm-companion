<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Circle Grove Register for Druid active-play orientation.
 */
final class DruidCircleGroveRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Wild Shape & Druid Circle',
            'detail' =>
                'Transformation craft opens and the Druid joins one of the six registered Circles.',
        ],
        4 => [
            'level' => 4,
            'label' => 'Wild Shape Improvement',
            'detail' =>
                'Wild Shape expands to stronger forms and the Druid gains another cantrip.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Circle Gift',
            'detail' =>
                'The chosen Circle reaches its next specialist feature milestone.',
        ],
        8 => [
            'level' => 8,
            'label' => 'Wild Shape Improvement',
            'detail' =>
                'Wild Shape reaches its later core transformation threshold.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Circle Gift & Cantrip',
            'detail' =>
                'Circle progression deepens and the Druid gains a fourth baseline cantrip.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Final Circle Gift',
            'detail' =>
                'The chosen Circle reaches its final specialist gift milestone.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Timeless Body & Beast Spells',
            'detail' =>
                'Age loosens its grip and spellcasting becomes compatible with Wild Shape.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Archdruid',
            'detail' =>
                'The Druid reaches the height of nature magic and transformation craft.',
        ],
    ];

    public function __construct(
        private ?DruidGrovePolicy $policy = null,
        private ?PathCandidateCatalogue $circles = null,
        private ?PathGiftCatalogue $gifts = null
    ) {
        $this->policy ??=
            new DruidGrovePolicy();

        $this->circles ??=
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
            !== 'druid'
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

        $circle = $character
            ->callingPath()
            ->value();

        $candidates =
            $this->circles->forClass(
                $character
                    ->characterClass()
            );

        $giftCount = $circle !== ''
            && $this->gifts->supports($circle)
                ? count(
                    $this->gifts->all(
                        $circle
                    )
                )
                : 0;

        $primalReserves = (
            new DruidPrimalReserveService()
        )->reserves(
            $character,
            $resources
        );

        return [
            'supported' => true,
            'level' => $level,
            'primal_reserves' => $primalReserves,
            'circle' => [
                'selection_level' => 2,
                'chosen' => $circle !== '',
                'key' => $circle,
                'label' =>
                    $this->circleLabel(
                        $circle,
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
                        ? 'Circle Gifts certified'
                        : 'Circle Gifts unavailable',
                'spells' => (
                    new DruidCircleSpellCatalogue()
                )->forCircle($circle),
                'unlocked_spells' => (
                    new DruidCircleSpellCatalogue()
                )->unlocked($circle, $level),
            ],
            'wild_shape' => [
                'unlocked' => $level >= 2,
                'stage' =>
                    $this->policy
                        ->wildShapeStage(
                            $character
                        ),
                'next_improvement_level' =>
                    match (true) {
                        $level < 2 => 2,
                        $level < 4 => 4,
                        $level < 8 => 8,
                        default => null,
                    },
                'resource_tracking' =>
                    false,
            ],
            'spellcasting' => [
                'unlocked' => true,
                'model' =>
                    'prepared-spells',
                'ability' =>
                    'Wisdom',
                'prepared_maximum' =>
                    $this->policy
                        ->preparedSpellMaximum(
                            $character
                        ),
                'prepared_formula' =>
                    'Druid level + Wisdom modifier',
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
    private function circleLabel(
        string $circle,
        array $candidates
    ): string {
        if ($circle === '') {
            return 'Circle not yet chosen';
        }

        foreach ($candidates as $candidate) {
            if (
                ($candidate['key'] ?? '')
                === $circle
            ) {
                return (string) (
                    $candidate['label']
                    ?? $circle
                );
            }
        }

        return ucwords(
            str_replace(
                '-',
                ' ',
                $circle
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

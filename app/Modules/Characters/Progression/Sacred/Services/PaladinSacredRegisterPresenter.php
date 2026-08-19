<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Paladin Sacred Register for III.12.6A.
 */
final class PaladinSacredRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Spellcasting & Divine Smite',
            'detail' =>
                'The Paladin’s martial and sacred magic disciplines join at Level 2.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Sacred Oath',
            'detail' =>
                'The Paladin formally chooses the Oath that defines their specialist vows.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Extra Attack',
            'detail' =>
                'The Attack action advances to two attacks.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Aura of Protection',
            'detail' =>
                'The Paladin begins projecting a 10-foot protective aura.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Aura of Courage',
            'detail' =>
                'The Paladin’s sacred presence protects nearby allies against fear.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Improved Divine Smite',
            'detail' =>
                'Melee weapon strikes gain an enduring radiant edge.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Cleansing Touch',
            'detail' =>
                'The Paladin gains a limited sacred cleansing action.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Greater Aura',
            'detail' =>
                'Qualifying Paladin auras extend to 30 feet.',
        ],
    ];

    public function __construct(
        private ?PaladinSacredPolicy $policy = null,
        private ?PathCandidateCatalogue $paths = null
    ) {
        $this->policy ??=
            new PaladinSacredPolicy();

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
            !== 'paladin'
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
            'lay_on_hands' => [
                'maximum' =>
                    $this->policy
                        ->layOnHandsMaximum(
                            $character
                        ),
                'refresh' =>
                    'Long rest',
            ],
            'divine_sense' => [
                'maximum' =>
                    $this->policy
                        ->divineSenseMaximum(
                            $character
                        ),
                'refresh' =>
                    'Long rest',
            ],
            'sacred_save_dc' =>
                $this->policy
                    ->sacredSaveDc(
                        $character
                    ),
            'aura' => [
                'range_feet' =>
                    $this->policy
                        ->auraRangeFeet(
                            $character
                        ),
                'unlocked' =>
                    $level >= 6,
            ],
            'cleansing_touch' => [
                'unlocked' =>
                    $level >= 14,
                'maximum' =>
                    $this->policy
                        ->cleansingTouchMaximum(
                            $character
                        ),
                'refresh' =>
                    'Long rest',
            ],
            'features' => [
                $this->feature(
                    'spellcasting',
                    'Spellcasting',
                    2,
                    $level,
                    'Half-caster spell progression begins here; detailed slot certification follows the Paladin spellcasting slice.'
                ),
                $this->feature(
                    'divine-smite',
                    'Divine Smite',
                    2,
                    $level,
                    'A qualifying melee hit can later consume a certified spell slot for radiant damage.'
                ),
                $this->feature(
                    'divine-health',
                    'Divine Health',
                    3,
                    $level,
                    'Sacred conviction hardens the Paladin against disease.'
                ),
                $this->feature(
                    'extra-attack',
                    'Extra Attack',
                    5,
                    $level,
                    'The Attack action advances to two attacks.'
                ),
                $this->feature(
                    'aura-of-protection',
                    'Aura of Protection',
                    6,
                    $level,
                    'Qualifying saving throws benefit from the Paladin’s nearby sacred presence.'
                ),
                $this->feature(
                    'aura-of-courage',
                    'Aura of Courage',
                    10,
                    $level,
                    'Nearby allies are protected against fear while within the certified aura.'
                ),
                $this->feature(
                    'improved-divine-smite',
                    'Improved Divine Smite',
                    11,
                    $level,
                    'Melee weapon strikes carry an enduring measure of radiant power.'
                ),
                $this->feature(
                    'cleansing-touch',
                    'Cleansing Touch',
                    14,
                    $level,
                    'A limited sacred action can end qualifying magical effects.'
                ),
            ],
            'oath' =>
                $this->oathState(
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
    private function oathState(
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
                    ? 'Awaiting Sacred Oath'
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

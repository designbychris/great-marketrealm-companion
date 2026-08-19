<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Rogue progression state for the Character Ledger.
 *
 * III.12.4A presents certified Rogue capability without persisting
 * once-per-turn or reaction state. Those mechanics belong to III.12.4C/D.
 */
final class RogueCunningRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Cunning Action',
            'detail' =>
                'Bonus-action mobility and escape become part of the Rogue’s specialist toolkit.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Rogue Archetype',
            'detail' =>
                'Choose the Archetype that defines the Rogue’s specialist methods.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Uncanny Dodge',
            'detail' =>
                'A defensive reaction becomes available against a visible attacker.',
        ],
        7 => [
            'level' => 7,
            'label' => 'Evasion',
            'detail' =>
                'The Rogue becomes exceptionally difficult to catch in damaging area effects.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Reliable Talent',
            'detail' =>
                'Trained skill use becomes extraordinarily dependable.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Blindsense',
            'detail' =>
                'Nearby unseen threats become harder to hide from the Rogue.',
        ],
        15 => [
            'level' => 15,
            'label' => 'Slippery Mind',
            'detail' =>
                'Mental discipline hardens against hostile control.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Elusive',
            'detail' =>
                'The Rogue becomes extraordinarily difficult to catch at a disadvantage.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Stroke of Luck',
            'detail' =>
                'Rogue mastery can turn a missed opportunity into decisive success.',
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
        Character $character
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'rogue'
        ) {
            return [
                'supported' => false,
            ];
        }

        $level = $character
            ->level()
            ->value();

        $cunningActions = (
            new RogueCunningActionPresenter()
        )->present(
            $character
        );

        return [
            'supported' => true,
            'level' => $level,
            'sneak_attack' => [
                'dice' =>
                    $this->sneakAttackDice(
                        $level
                    ),
                'frequency' => 'Once per turn',
                'status' => 'Certified',
            ],
            'cunning_action' => [
                'unlocked' => $level >= 2,
                'options' => [
                    'Dash',
                    'Disengage',
                    'Hide',
                ],
                'cost' =>
                    $cunningActions['cost']
                    ?? 'Bonus action',
                'refresh' =>
                    $cunningActions['refresh']
                    ?? 'Every turn',
                'actions' =>
                    $cunningActions['actions']
                    ?? [],
            ],
            'features' => [
                [
                    'key' => 'uncanny-dodge',
                    'label' => 'Uncanny Dodge',
                    'level' => 5,
                    'unlocked' => $level >= 5,
                    'detail' =>
                        'Use a reaction to reduce the impact of a qualifying visible attacker.',
                ],
                [
                    'key' => 'expertise',
                    'label' => 'Expertise',
                    'level' => 6,
                    'unlocked' => $level >= 6,
                    'detail' =>
                        'Additional trained skills can benefit from deeper Rogue mastery.',
                ],
                [
                    'key' => 'evasion',
                    'label' => 'Evasion',
                    'level' => 7,
                    'unlocked' => $level >= 7,
                    'detail' =>
                        'Dexterity-based area danger becomes much easier to avoid.',
                ],
                [
                    'key' => 'reliable-talent',
                    'label' => 'Reliable Talent',
                    'level' => 11,
                    'unlocked' => $level >= 11,
                    'detail' =>
                        'Proficient skill use becomes remarkably dependable.',
                ],
                [
                    'key' => 'blindsense',
                    'label' => 'Blindsense',
                    'level' => 14,
                    'unlocked' => $level >= 14,
                    'detail' =>
                        'Nearby unseen threats become harder to conceal.',
                ],
                [
                    'key' => 'slippery-mind',
                    'label' => 'Slippery Mind',
                    'level' => 15,
                    'unlocked' => $level >= 15,
                    'detail' =>
                        'The Rogue’s mental discipline hardens significantly.',
                ],
                [
                    'key' => 'elusive',
                    'label' => 'Elusive',
                    'level' => 18,
                    'unlocked' => $level >= 18,
                    'detail' =>
                        'Enemies struggle to gain an easy advantage over the Rogue.',
                ],
                [
                    'key' => 'stroke-of-luck',
                    'label' => 'Stroke of Luck',
                    'level' => 20,
                    'unlocked' => $level >= 20,
                    'detail' =>
                        'The Rogue reaches the height of opportunistic mastery.',
                ],
            ],
            'archetype' =>
                array_merge(
                    $this->archetypeState(
                        $character
                    ),
                    [
                        'gifts' =>
                            $this->certifiedArchetypeGifts(
                                $character
                            ),
                    ]
                ),
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
            'milestones' =>
                array_values(self::MILESTONES),
        ];
    }

    private function sneakAttackDice(
        int $level
    ): string {
        $dice = (int) ceil(
            max(1, $level) / 2
        );

        return $dice . 'd6';
    }

    /**
     * @return array<string,mixed>
     */
    private function archetypeState(
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
                    ? 'Awaiting Rogue Archetype'
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
     * @return array<int,array<string,mixed>>
     */
    private function certifiedArchetypeGifts(
        Character $character
    ): array {
        if (
            ! $character
                ->callingPath()
                ->isChosen()
        ) {
            return [];
        }

        $path = $character
            ->callingPath()
            ->value();

        return array_values(
            array_filter(
                $this->gifts->all($path),
                fn (array $gift): bool =>
                    $character
                        ->pathGifts()
                        ->has(
                            (string) (
                                $gift['key']
                                ?? ''
                            )
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

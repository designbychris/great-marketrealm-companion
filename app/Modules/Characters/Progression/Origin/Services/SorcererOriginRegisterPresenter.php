<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Sorcerer Origin Spark Register for III.12.8A.
 */
final class SorcererOriginRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Font of Magic',
            'detail' =>
                'Sorcery Points and flexible magical conversion begin.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Metamagic',
            'detail' =>
                'The Sorcerer learns two ways to reshape known spells.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Origin Gift',
            'detail' =>
                'The chosen Sorcerous Origin reaches its next specialist milestone.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Metamagic',
            'detail' =>
                'A third Metamagic option becomes available.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Origin Gift',
            'detail' =>
                'The Sorcerous Origin reaches another specialist milestone.',
        ],
        17 => [
            'level' => 17,
            'label' => 'Metamagic',
            'detail' =>
                'A fourth Metamagic option becomes available.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Origin Gift',
            'detail' =>
                'The Sorcerous Origin reaches its final specialist milestone.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Sorcerous Restoration',
            'detail' =>
                'Short rests restore a measure of spent Sorcery Points.',
        ],
    ];

    public function __construct(
        private ?SorcererOriginPolicy $policy = null,
        private ?PathCandidateCatalogue $paths = null,
        private ?SpellcastingProgressionCatalogue $spellcasting = null
    ) {
        $this->policy ??=
            new SorcererOriginPolicy();

        $this->paths ??=
            new PathCandidateCatalogue();

        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();
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
            !== 'sorcerer'
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

        $spellcasting = $level >= 2
            ? $this->spellcasting
                ->forLevel(
                    $character->characterClass(),
                    $level
                )
            : null;

        return [
            'supported' => true,
            'level' => $level,
            'origin' =>
                $this->originState(
                    $character
                ),
            'sorcery_points' => [
                'maximum' =>
                    $this->policy
                        ->sorceryPointMaximum(
                            $character
                        ),
                'unlocked' =>
                    $level >= 2,
            ],
            'metamagic' => [
                'known' =>
                    $this->policy
                        ->metamagicKnown(
                            $character
                        ),
                'unlocked' =>
                    $level >= 3,
            ],
            'spellcasting' => [
                'model' =>
                    (string) (
                        $spellcasting['model']
                        ?? 'known-spells'
                    ),
                'spells_known' =>
                    (int) (
                        $spellcasting['spells_known']
                        ?? 2
                    ),
                'cantrips_known' =>
                    (int) (
                        $spellcasting['cantrips_known']
                        ?? 4
                    ),
                'maximum_spell_level' =>
                    $level >= 1
                        ? min(
                            9,
                            intdiv($level + 1, 2)
                        )
                        : 0,
                'slots' => (
                    new SharedSpellSlotReserveService()
                )->present(
                    $character,
                    $resources
                ),
            ],
            'spell_save_dc' =>
                $this->policy
                    ->spellSaveDc(
                        $character
                    ),
            'spell_attack' =>
                $this->policy
                    ->spellAttackBonus(
                        $character
                    ),
            'sorcerous_restoration' => [
                'unlocked' =>
                    $level >= 20,
                'restored_points' =>
                    $level >= 20
                        ? 4
                        : 0,
            ],
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
    }

    /** @return array<string,mixed> */
    private function originState(
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
                    'Awaiting Origin Spark',
                'detail' => '',
            ];
        }

        $key = $character
            ->callingPath()
            ->value();

        $label = ucwords(
            str_replace('-', ' ', $key)
        );

        $detail = '';

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
                ) !== $key
            ) {
                continue;
            }

            $label = (string) (
                $candidate['label']
                ?? $label
            );

            $detail = (string) (
                $candidate['detail']
                ?? ''
            );

            break;
        }

        return [
            'chosen' => true,
            'key' => $key,
            'label' => $label,
            'detail' => $detail,
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

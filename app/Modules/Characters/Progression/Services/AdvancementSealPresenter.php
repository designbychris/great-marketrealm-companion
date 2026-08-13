<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

final class AdvancementSealPresenter
{
    /**
     * @param array<string,mixed> $advancement
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        array $advancement
    ): array {
        $eligible = ! empty(
            $advancement['eligible']
        );

        $foliosComplete = ! empty(
            $advancement['folios_complete']
        );

        $ready = $eligible
            && $foliosComplete;

        $recordedChoices = is_array(
            $advancement['recorded_choices']
            ?? null
        )
            ? $advancement['recorded_choices']
            : [];

        $vitalityChoice = (string) (
            $recordedChoices[
                'vitality-hit-points'
            ][0] ?? ''
        );

        $hpDetail = match ($vitalityChoice) {
            'average' => sprintf(
                '+%d maximum HP using the class average.',
                (int) (
                    $advancement[
                        'suggested_hp_gain'
                    ] ?? 0
                )
            ),
            'roll' => sprintf(
                'Roll %s with Constitution modifier %s during certification.',
                (string) (
                    $advancement['hit_die']
                    ?? ''
                ),
                $this->signed(
                    (int) (
                        $advancement[
                            'constitution_modifier'
                        ] ?? 0
                    )
                )
            ),
            default =>
                'A hit point advancement method still needs to be recorded.',
        };

        $currentLevel = (int) (
            $advancement['current_level']
            ?? $character->level()->value()
        );

        $targetLevel = (int) (
            $advancement['target_level']
            ?? $currentLevel
        );

        $review = [
            [
                'key' => 'level',
                'label' => 'Level',
                'detail' => sprintf(
                    '%d → %d',
                    $currentLevel,
                    $targetLevel
                ),
                'ready' => $eligible,
            ],
            [
                'key' => 'vitality',
                'label' => 'Vitality',
                'detail' => $hpDetail,
                'ready' =>
                    $vitalityChoice !== '',
            ],
            [
                'key' => 'proficiency',
                'label' => 'Proficiency',
                'detail' => sprintf(
                    '%s → %s',
                    (string) (
                        $advancement[
                            'current_proficiency'
                        ] ?? ''
                    ),
                    (string) (
                        $advancement[
                            'target_proficiency'
                        ] ?? ''
                    )
                ),
                'ready' => true,
            ],
        ];

        $classProgression = is_array(
            $advancement['class_progression']
            ?? null
        )
            ? $advancement['class_progression']
            : [];

        if ($classProgression !== []) {
            $review[] = [
                'key' => 'calling',
                'label' => 'Calling',
                'detail' => sprintf(
                    '%s Level %d progression reviewed.',
                    (string) (
                        $classProgression['label']
                        ?? $character
                            ->characterClass()
                            ->label()
                    ),
                    $targetLevel
                ),
                'ready' => true,
            ];
        }

        foreach (
            ($advancement['folios'] ?? [])
            as $folio
        ) {
            if (
                ! is_array($folio)
                || ! in_array(
                    (string) ($folio['key'] ?? ''),
                    ['spellbook', 'cantrips'],
                    true
                )
            ) {
                continue;
            }

            $review[] = [
                'key' => (string) $folio['key'],
                'label' => (string) (
                    $folio['label'] ?? 'Spellbook'
                ),
                'detail' => (string) (
                    $folio['summary'] ?? ''
                ),
                'ready' => ! empty($folio['ready']),
            ];
        }

        return [
            'available' => $eligible,
            'ready' => $ready,
            'title' => $ready
                ? 'The Advancement Seal'
                : 'Registrar’s Review Pending',
            'status' => $ready
                ? 'READY FOR GUILD CERTIFICATION'
                : 'FOLIOS REQUIRE ATTENTION',
            'copy' => $ready
                ? 'Every current advancement folio has passed review. The record is ready for the later Guild certification step.'
                : 'The Registrar cannot seal this advancement until every required folio is ready.',
            'current_level' => $currentLevel,
            'target_level' => $targetLevel,
            'folio_ready_count' => (int) (
                $advancement[
                    'folio_ready_count'
                ] ?? 0
            ),
            'folio_total' => (int) (
                $advancement[
                    'folio_total'
                ] ?? 0
            ),
            'review' => $review,
            /*
             * The Seal means "review-ready", not "mutated".
             * Phase III.8.5 will own the atomic certification.
             */
            'commit_available' => false,
        ];
    }

    private function signed(int $value): string
    {
        return $value >= 0
            ? '+' . $value
            : (string) $value;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Services\PathGiftPresenter;

defined('ABSPATH') || exit;

/**
 * Presents the Character's permanent, currently certified progression state.
 *
 * Unlike the Rising Register, this presenter never describes pending
 * advancement paperwork. It only reads state already entered into the
 * Character aggregate and completed Guild certification archive.
 */
final class LivingRegisterPresenter
{
    public function __construct(
        private ?PathGiftPresenter $pathGifts = null
    ) {
        $this->pathGifts ??= new PathGiftPresenter();
    }

    /**
     * @param array<int,array<string,mixed>> $history
     * @return array<string,mixed>
     */
    public function present(Character $character, array $history = []): array
    {
        $spellbook = $character->spellbook();
        $pathState = $this->pathGifts->present($character);
        $latest = $history === [] ? null : $history[array_key_last($history)];
        $latest = is_array($latest) ? $latest : null;
        $chronicle = $this->chronicle($history);
        $milestoneCount = array_sum(array_map(
            static fn (array $entry): int => count($entry['milestones'] ?? []),
            $chronicle
        ));
        $journey = $this->journeyMeasure($chronicle);
        $changeRecord = $this->changeRecord($chronicle, $character);

        return [
            'level' => $character->level()->value(),
            'calling' => $character->characterClass()->label(),
            'calling_key' => $character->characterClass()->value(),
            'path' => $character->callingPath()->value(),
            'path_label' => (string) ($pathState['path_label'] ?? ''),
            'has_path' => $character->callingPath()->isChosen(),
            'proficiency' => $character->proficiencyBonus()->signed(),
            'current_hp' => $character->hitPoints()->current(),
            'maximum_hp' => $character->hitPoints()->maximum(),
            'experience' => $character->experience()->value(),
            'spells_known' => count($spellbook->spells()),
            'cantrips_known' => count($spellbook->cantrips()),
            'arcana_known' => count($spellbook->spells()) + count($spellbook->cantrips()),
            'path_gifts' => $pathState['gifts'] ?? [],
            'path_gift_count' => (int) ($pathState['count'] ?? 0),
            'certification_count' => count($history),
            'latest_certification' => $latest,
            'fresh_ink' => $this->freshInk($latest),
            'has_fresh_ink' => $latest !== null,
            'chronicle' => $chronicle,
            'has_chronicle' => $chronicle !== [],
            'milestone_count' => $milestoneCount,
            'has_milestones' => $milestoneCount > 0,
            'journey_measure' => $journey,
            'has_journey_measure' => $chronicle !== [],
            'change_record' => $changeRecord,
            'has_change_record' => $chronicle !== [],
            'is_living_record' => true,
        ];
    }

    /**
     * Turn the latest immutable certification archive entry into a concise
     * account of what the Guild most recently entered into the Register.
     *
     * @param array<string,mixed>|null $latest
     * @return array<string,mixed>|null
     */
    private function freshInk(?array $latest): ?array
    {
        if ($latest === null) {
            return null;
        }

        $choices = is_array($latest['choices'] ?? null)
            ? $latest['choices']
            : [];
        $gifts = is_array($latest['path_gifts_granted'] ?? null)
            ? $latest['path_gifts_granted']
            : [];

        return [
            'from_level' => (int) ($latest['from_level'] ?? 0),
            'target_level' => (int) ($latest['target_level'] ?? 0),
            'hit_point_gain' => (int) ($latest['hit_point_gain'] ?? 0),
            'old_maximum_hp' => (int) ($latest['old_maximum_hp'] ?? 0),
            'new_maximum_hp' => (int) ($latest['new_maximum_hp'] ?? 0),
            'proficiency' => (string) ($latest['proficiency'] ?? ''),
            'spells_learned' => $this->choiceValues($choices, 'wizard-spells'),
            'cantrips_learned' => $this->choiceValues($choices, 'wizard-cantrips'),
            'path_gifts_granted' => array_values(array_filter(array_map(
                static fn (mixed $gift): string => is_array($gift)
                    ? (string) ($gift['label'] ?? $gift['key'] ?? '')
                    : '',
                $gifts
            ))),
            'certified_at' => (string) ($latest['certified_at'] ?? ''),
        ];
    }


    /**
     * @param array<int,array<string,mixed>> $history
     * @return array<int,array<string,mixed>>
     */
    private function chronicle(array $history): array
    {
        $entries = [];
        $total = count($history);

        foreach (array_reverse($history, true) as $index => $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $ink = $this->freshInk($entry);

            if ($ink === null) {
                continue;
            }

            $entries[] = $ink + [
                'sequence' => ((int) $index) + 1,
                'is_latest' => ((int) $index) === ($total - 1),
                'certification_key' => (string) ($entry['certification_key'] ?? ''),
                'calling_path' => (string) ($entry['calling_path'] ?? ''),
                'milestones' => $this->milestones($history, (int) $index, $entry),
            ];
        }

        return $entries;
    }


    /**
     * @param array<int,array<string,mixed>> $history
     * @param array<string,mixed> $entry
     * @return array<int,array{key:string,label:string,symbol:string}>
     */
    private function milestones(array $history, int $index, array $entry): array
    {
        $marks = [];
        $targetLevel = (int) ($entry['target_level'] ?? 0);

        if ($index === 0) {
            $marks[] = $this->milestone('first-seal', 'First Guild Seal', '◆');
        }

        $path = (string) ($entry['calling_path'] ?? '');
        $previousPath = $index > 0 && is_array($history[$index - 1] ?? null)
            ? (string) ($history[$index - 1]['calling_path'] ?? '')
            : '';

        if ($path !== '' && $previousPath === '') {
            $marks[] = $this->milestone('calling-path', 'Calling Path Entered', '✦');
        }

        if (is_array($entry['path_gifts_granted'] ?? null) && $entry['path_gifts_granted'] !== []) {
            $marks[] = $this->milestone('path-gift', 'Gift of the Path', '✧');
        }

        if (in_array($targetLevel, [5, 10, 15, 20], true)) {
            $marks[] = $this->milestone(
                'level-' . $targetLevel,
                'Level ' . $targetLevel . ' Guild Milestone',
                '★'
            );
        }

        return $marks;
    }

    /** @return array{key:string,label:string,symbol:string} */
    private function milestone(string $key, string $label, string $symbol): array
    {
        return compact('key', 'label', 'symbol');
    }


    /**
     * Summarise the retained Chronicle without creating another source of
     * progression truth. Every figure here is derived from sealed entries.
     *
     * @param array<int,array<string,mixed>> $chronicle
     * @return array<string,int|string>
     */
    private function journeyMeasure(array $chronicle): array
    {
        $totals = [
            'certifications' => count($chronicle),
            'maximum_hp_gained' => 0,
            'spells_learned' => 0,
            'cantrips_learned' => 0,
            'path_gifts_granted' => 0,
            'milestones' => 0,
            'first_certified_at' => '',
            'latest_certified_at' => '',
        ];

        foreach ($chronicle as $entry) {
            $totals['maximum_hp_gained'] += max(0, (int) ($entry['hit_point_gain'] ?? 0));
            $totals['spells_learned'] += count($entry['spells_learned'] ?? []);
            $totals['cantrips_learned'] += count($entry['cantrips_learned'] ?? []);
            $totals['path_gifts_granted'] += count($entry['path_gifts_granted'] ?? []);
            $totals['milestones'] += count($entry['milestones'] ?? []);
        }

        if ($chronicle !== []) {
            $latest = $chronicle[0];
            $first = $chronicle[array_key_last($chronicle)];
            $totals['latest_certified_at'] = (string) ($latest['certified_at'] ?? '');
            $totals['first_certified_at'] = (string) ($first['certified_at'] ?? '');
        }

        return $totals;
    }


    /**
     * Describe the certified journey from the earliest retained seal to the
     * Character's current permanent state. This remains a read-only projection
     * over the Chronicle; it never becomes mutable progression state.
     *
     * @param array<int,array<string,mixed>> $chronicle
     * @return array<string,mixed>
     */
    private function changeRecord(array $chronicle, Character $character): array
    {
        if ($chronicle === []) {
            return [];
        }

        $oldestFirst = array_reverse($chronicle);
        $first = $oldestFirst[0];
        $firstPath = null;
        $firstGift = null;
        $firstArcana = null;

        foreach ($oldestFirst as $entry) {
            if ($firstPath === null && (string) ($entry['calling_path'] ?? '') !== '') {
                $firstPath = $this->changeMoment($entry);
            }

            if ($firstGift === null && ($entry['path_gifts_granted'] ?? []) !== []) {
                $firstGift = $this->changeMoment($entry);
            }

            if (
                $firstArcana === null
                && (($entry['spells_learned'] ?? []) !== [] || ($entry['cantrips_learned'] ?? []) !== [])
            ) {
                $firstArcana = $this->changeMoment($entry);
            }
        }

        $startingMaximumHp = (int) ($first['old_maximum_hp'] ?? 0);
        $currentMaximumHp = $character->hitPoints()->maximum();

        return [
            'starting_level' => max(1, (int) ($first['from_level'] ?? 1)),
            'current_level' => $character->level()->value(),
            'levels_gained' => max(0, $character->level()->value() - max(1, (int) ($first['from_level'] ?? 1))),
            'starting_maximum_hp' => $startingMaximumHp,
            'current_maximum_hp' => $currentMaximumHp,
            'maximum_hp_change' => $startingMaximumHp > 0
                ? max(0, $currentMaximumHp - $startingMaximumHp)
                : (int) array_sum(array_map(
                    static fn (array $entry): int => max(0, (int) ($entry['hit_point_gain'] ?? 0)),
                    $chronicle
                )),
            'first_path' => $firstPath,
            'first_path_gift' => $firstGift,
            'first_arcana' => $firstArcana,
        ];
    }

    /** @param array<string,mixed> $entry @return array{level:int,sequence:int,certified_at:string} */
    private function changeMoment(array $entry): array
    {
        return [
            'level' => (int) ($entry['target_level'] ?? 0),
            'sequence' => (int) ($entry['sequence'] ?? 0),
            'certified_at' => (string) ($entry['certified_at'] ?? ''),
        ];
    }

    /** @param array<string,mixed> $choices @return array<int,string> */
    private function choiceValues(array $choices, string $key): array
    {
        $values = $choices[$key] ?? [];

        if (! is_array($values)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $value): string => is_scalar($value)
                ? (string) $value
                : '',
            $values
        )));
    }

}

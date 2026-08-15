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
            ];
        }

        return $entries;
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

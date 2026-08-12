<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

/**
 * Builds the final cross-system Guild Record summary.
 *
 * The Complete Adventurer is not another persistence model. It is a
 * presentation audit proving that the existing Character, portrait,
 * inventory, combat, arcana and progression systems can all describe the
 * same adventurer at the same time.
 */
final class CompleteAdventurerPresenter
{
    /**
     * @param array<string,mixed> $inventory
     * @param array<int,array<string,mixed>> $attacks
     * @param array<string,mixed> $arcana
     * @param array<string,mixed> $progression
     * @return array<string,mixed>
     */
    public function present(
        Character $character,
        PortraitViewModel $portrait,
        array $inventory,
        array $attacks,
        array $arcana,
        array $progression
    ): array {
        $itemCount = count($inventory['rows'] ?? []);
        $attackCount = count($attacks);
        $arcaneCount = count($arcana['entries'] ?? []);

        $sections = [
            $this->section(
                'identity',
                'Identity',
                'Registered',
                $character->race()->label()
                    . ' · '
                    . $character->characterClass()->label(),
                'overview',
                '✦',
                trim($character->name()->value()) !== ''
            ),
            $this->section(
                'abilities',
                'Abilities',
                'Recorded',
                'Six Guild ability scores are on file.',
                'overview',
                '⚄',
                count($character->abilityScores()->all()) === 6
            ),
            $this->section(
                'portrait',
                'Portrait',
                $portrait->isCustom()
                    ? 'Custom likeness'
                    : 'Illuminated',
                $portrait->isCustom()
                    ? 'A personal portrait is attached to the Guild Record.'
                    : 'The Guild Illuminator recipe is ready.',
                'overview',
                '◉',
                $portrait->isCustom()
                    || trim($portrait->svg()) !== ''
            ),
            $this->section(
                'equipment',
                'Equipment',
                'Pack indexed',
                sprintf(
                    '%d item%s · %d equipped',
                    $itemCount,
                    $itemCount === 1 ? '' : 's',
                    (int) ($inventory['equipped_count'] ?? 0)
                ),
                'equipment',
                '🎒',
                array_key_exists('rows', $inventory)
            ),
            $this->section(
                'combat',
                'Combat',
                'Calculations ready',
                $attackCount > 0
                    ? sprintf(
                        '%d equipped attack%s prepared.',
                        $attackCount,
                        $attackCount === 1 ? '' : 's'
                    )
                    : 'No weapon equipped; core rolls remain available.',
                'attacks',
                '⚔',
                true
            ),
            $this->section(
                'arcana',
                'Spells & Abilities',
                'Pantry indexed',
                ! empty($arcana['has_spells'])
                    ? sprintf(
                        '%d spell or class entr%s available.',
                        $arcaneCount,
                        $arcaneCount === 1 ? 'y' : 'ies'
                    )
                    : 'Class abilities are indexed; no spellcasting required.',
                'arcana',
                '✧',
                array_key_exists('entries', $arcana)
            ),
            $this->section(
                'progression',
                'Progression',
                'Tracked',
                ! empty($progression['is_maximum'])
                    ? 'Maximum Guild level reached.'
                    : sprintf(
                        '%d XP until Level %d.',
                        (int) ($progression['xp_to_next'] ?? 0),
                        (int) ($progression['next_level'] ?? $character->level()->value())
                    ),
                'progression',
                '↑',
                array_key_exists('progress_percent', $progression)
            ),
        ];

        $readyCount = count(
            array_filter(
                $sections,
                static fn (array $section): bool =>
                    $section['ready'] === true
            )
        );

        $total = count($sections);
        $complete = $readyCount === $total;

        return [
            'complete' => $complete,
            'ready_count' => $readyCount,
            'total' => $total,
            'label' => $complete
                ? 'Complete Adventurer'
                : 'Guild Record Review',
            'summary' => $complete
                ? 'Every major Guild folio is connected to this adventurer.'
                : 'One or more Guild folios need the Registrar’s attention.',
            'sections' => $sections,
        ];
    }

    /** @return array<string,mixed> */
    private function section(
        string $key,
        string $label,
        string $status,
        string $detail,
        string $panel,
        string $symbol,
        bool $ready
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'detail' => $detail,
            'panel' => $panel,
            'symbol' => $symbol,
            'ready' => $ready,
        ];
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\FighterBattleReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Fighter martial state for the Character Ledger.
 *
 * The Register derives its state from certified Character level and the
 * already-persisted Calling Path. III.12.2A does not add another mutable
 * resource store or duplicate advancement history.
 */
final class FighterMartialRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Action Surge',
            'detail' =>
                'One Action Surge becomes available between short rests.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Martial Path',
            'detail' =>
                'The Fighter chooses the specialised path that shapes later training.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Extra Attack',
            'detail' =>
                'The Attack action advances to two attacks.',
        ],
        9 => [
            'level' => 9,
            'label' => 'Indomitable',
            'detail' =>
                'One Indomitable use becomes available between long rests.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Extra Attack',
            'detail' =>
                'The Attack action advances to three attacks.',
        ],
        13 => [
            'level' => 13,
            'label' => 'Indomitable',
            'detail' =>
                'Indomitable increases to two uses.',
        ],
        17 => [
            'level' => 17,
            'label' => 'Deep Reserves',
            'detail' =>
                'Action Surge reaches two uses and Indomitable reaches three.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Extra Attack',
            'detail' =>
                'The Attack action reaches four attacks.',
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
        Character $character,
        ?ActiveClassResourceState $active = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'fighter'
        ) {
            return [
                'supported' => false,
            ];
        }

        $level = $character->level()->value();
        $active ??=
            ActiveClassResourceState::fresh();

        $reserves =
            new FighterBattleReserveService();

        $martialActions = (
            new FighterMartialActionPresenter()
        )->present($character);

        $path = $this->pathState($character);
        $path['gifts'] =
            $this->certifiedPathGifts(
                $character
            );

        return [
            'supported' => true,
            'level' => $level,
            'attacks_per_action' =>
                $this->attacksPerAction($level),
            'resources' => [
                $this->resource(
                    'second-wind',
                    'Second Wind',
                    true,
                    1,
                    $active,
                    'Short rest',
                    sprintf(
                        '1d10 + %d healing',
                        $level
                    ),
                    'Bonus action',
                    $martialActions[
                        'resources'
                    ]['second-wind']
                    ?? []
                ),
                $this->resource(
                    'action-surge',
                    'Action Surge',
                    $level >= 2,
                    $reserves->maximum(
                        $character,
                        'action-surge'
                    ),
                    $active,
                    'Short rest',
                    'Take one additional action',
                    'Free',
                    $martialActions[
                        'resources'
                    ]['action-surge']
                    ?? []
                ),
                $this->resource(
                    'indomitable',
                    'Indomitable',
                    $level >= 9,
                    $reserves->maximum(
                        $character,
                        'indomitable'
                    ),
                    $active,
                    'Long rest',
                    'Reroll a failed saving throw',
                    'On failed save',
                    $martialActions[
                        'resources'
                    ]['indomitable']
                    ?? []
                ),
            ],
            'path' => $path,
            'next_milestone' =>
                $this->nextMilestone($level),
            'milestones' =>
                array_values(self::MILESTONES),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function resource(
        string $key,
        string $label,
        bool $unlocked,
        int $uses,
        ActiveClassResourceState $active,
        string $refresh,
        string $effect,
        string $activation,
        array $action = []
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'unlocked' => $unlocked,
            'uses' => $uses,
            'maximum' => $uses,
            'expended' =>
                $active->expended($key),
            'remaining' =>
                $active->remaining(
                    $key,
                    $uses
                ),
            'refresh' => $refresh,
            'effect' => $effect,
            'activation' => $activation,
            'action' => $action,
        ];
    }

    private function attacksPerAction(
        int $level
    ): int {
        if ($level >= 20) {
            return 4;
        }

        if ($level >= 11) {
            return 3;
        }

        if ($level >= 5) {
            return 2;
        }

        return 1;
    }

    private function indomitableUses(
        int $level
    ): int {
        if ($level >= 17) {
            return 3;
        }

        if ($level >= 13) {
            return 2;
        }

        return $level >= 9
            ? 1
            : 0;
    }

    /**
     * @return array<string,mixed>
     */
    private function pathState(
        Character $character
    ): array {
        $level = $character->level()->value();
        $chosen = $character
            ->callingPath()
            ->isChosen();

        if (! $chosen) {
            return [
                'chosen' => false,
                'available' => $level >= 3,
                'key' => '',
                'label' => $level >= 3
                    ? 'Awaiting Martial Path'
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
                (string) ($candidate['key'] ?? '')
                === $key
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
    private function certifiedPathGifts(
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
            if ($milestone['level'] > $level) {
                return $milestone;
            }
        }

        return null;
    }
}

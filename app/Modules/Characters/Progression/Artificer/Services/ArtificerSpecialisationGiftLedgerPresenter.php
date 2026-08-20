<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Living Ledger projection of certified Artificer Specialisation Gifts.
 */
final class ArtificerSpecialisationGiftLedgerPresenter
{
    public function __construct(
        private ?PathGiftCatalogue $catalogue = null
    ) {
        $this->catalogue ??= new PathGiftCatalogue();
    }

    /** @return array<string,mixed> */
    public function present(Character $character): array
    {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'artificer'
        ) {
            return [
                'supported' => false,
                'gifts' => [],
                'next_gifts' => [],
            ];
        }

        $specialisation = $character
            ->callingPath()
            ->value();

        $level = $character
            ->level()
            ->value();

        if (
            $specialisation === ''
            || ! $this->catalogue->supports($specialisation)
        ) {
            return [
                'supported' => true,
                'specialisation' => $specialisation,
                'specialisation_label' =>
                    $specialisation === ''
                        ? 'Specialisation not yet chosen'
                        : $this->fallbackLabel($specialisation),
                'gifts' => [],
                'count' => 0,
                'next_level' =>
                    $specialisation === ''
                        ? 3
                        : null,
                'next_gifts' => [],
                'complete' => false,
            ];
        }

        $all = $this->catalogue->all(
            $specialisation
        );

        $gifts = array_values(
            array_filter(
                $all,
                static fn (array $gift): bool =>
                    (int) (
                        $gift['level']
                        ?? 0
                    ) <= $level
            )
        );

        $futureLevels = array_values(
            array_unique(
                array_map(
                    static fn (array $gift): int =>
                        (int) (
                            $gift['level']
                            ?? 0
                        ),
                    array_filter(
                        $all,
                        static fn (array $gift): bool =>
                            (int) (
                                $gift['level']
                                ?? 0
                            ) > $level
                    )
                )
            )
        );

        sort($futureLevels);

        $nextLevel =
            $futureLevels[0]
            ?? null;

        $nextGifts = $nextLevel === null
            ? []
            : array_values(
                array_filter(
                    $all,
                    static fn (array $gift): bool =>
                        (int) (
                            $gift['level']
                            ?? 0
                        ) === $nextLevel
                )
            );

        return [
            'supported' => true,
            'specialisation' => $specialisation,
            'specialisation_label' =>
                $this->catalogue->pathLabel(
                    $specialisation
                ),
            'gifts' => $gifts,
            'count' => count($gifts),
            'next_level' => $nextLevel,
            'next_gifts' => $nextGifts,
            'complete' => $nextLevel === null,
        ];
    }

    private function fallbackLabel(
        string $specialisation
    ): string {
        return ucwords(
            str_replace(
                '-',
                ' ',
                $specialisation
            )
        );
    }
}

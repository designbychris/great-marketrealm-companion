<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Living Ledger projection of the Bard's certified College Gifts.
 *
 * The gift catalogue remains the single mechanics source. This presenter only
 * answers which supplied gifts are live at the Bard's current level and which
 * College milestone comes next.
 */
final class BardCollegeGiftLedgerPresenter
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
            !== 'bard'
        ) {
            return [
                'supported' => false,
                'gifts' => [],
                'next_gifts' => [],
            ];
        }

        $college = $character
            ->callingPath()
            ->value();

        $level = $character
            ->level()
            ->value();

        if (
            $college === ''
            || ! $this->catalogue->supports($college)
        ) {
            return [
                'supported' => true,
                'college' => $college,
                'college_label' =>
                    $college === ''
                        ? 'College not yet chosen'
                        : $this->fallbackLabel($college),
                'gifts' => [],
                'count' => 0,
                'next_level' =>
                    $college === ''
                        ? 3
                        : null,
                'next_gifts' => [],
                'complete' => false,
            ];
        }

        $all = $this->catalogue->all($college);

        $gifts = array_values(
            array_filter(
                $all,
                static fn (array $gift): bool =>
                    (int) ($gift['level'] ?? 0)
                    <= $level
            )
        );

        $futureLevels = array_values(
            array_unique(
                array_map(
                    static fn (array $gift): int =>
                        (int) ($gift['level'] ?? 0),
                    array_filter(
                        $all,
                        static fn (array $gift): bool =>
                            (int) ($gift['level'] ?? 0)
                            > $level
                    )
                )
            )
        );

        sort($futureLevels);

        $nextLevel = $futureLevels[0] ?? null;

        $nextGifts = $nextLevel === null
            ? []
            : array_values(
                array_filter(
                    $all,
                    static fn (array $gift): bool =>
                        (int) ($gift['level'] ?? 0)
                        === $nextLevel
                )
            );

        return [
            'supported' => true,
            'college' => $college,
            'college_label' =>
                $this->catalogue->pathLabel($college),
            'gifts' => $gifts,
            'count' => count($gifts),
            'next_level' => $nextLevel,
            'next_gifts' => $nextGifts,
            'complete' => $nextLevel === null,
        ];
    }

    private function fallbackLabel(string $college): string
    {
        return ucwords(
            str_replace(
                '-',
                ' ',
                $college
            )
        );
    }
}

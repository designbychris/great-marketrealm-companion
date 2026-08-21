<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Relics\Services;

use GreatMarketrealmCompanion\Modules\Library\Relics\Repositories\HandbookRelicRegister;

defined('ABSPATH') || exit;

final class RelicRegisterPresenter
{
    public function __construct(
        private ?HandbookRelicRegister $register = null
    ) {
        $this->register ??= new HandbookRelicRegister();
    }

    /** @param array<string,string> $filters */
    public function present(array $filters = []): array
    {
        $q = strtolower(trim((string) ($filters['q'] ?? '')));
        $rarity = trim((string) ($filters['rarity'] ?? ''));
        $group = trim((string) ($filters['group'] ?? ''));

        $records = array_filter(
            $this->register->all(),
            static function ($record) use ($q, $rarity, $group): bool {
                if (
                    $rarity !== ''
                    && strtolower($record->rarity()) !== strtolower($rarity)
                ) {
                    return false;
                }

                if ($group !== '' && $record->group() !== $group) {
                    return false;
                }

                if ($q === '') {
                    return true;
                }

                $haystack = strtolower(
                    implode(
                        ' ',
                        array_merge(
                            [
                                $record->name(),
                                $record->itemType(),
                                $record->rarity(),
                                $record->attunement() ?? '',
                                $record->baseProfile() ?? '',
                                $record->flavour() ?? '',
                            ],
                            $record->mechanics()
                        )
                    )
                );

                return str_contains($haystack, $q);
            }
        );

        return [
            'filters' => [
                'q' => $filters['q'] ?? '',
                'rarity' => $rarity,
                'group' => $group,
            ],
            'results' => array_map(
                static fn ($record): array => $record->toArray(),
                array_values($records)
            ),
            'result_count' => count($records),
            'total_count' => count($this->register->all()),
            'rarities' => [
                'Common',
                'Uncommon',
                'Rare',
                'Very Rare',
                'Legendary',
            ],
            'groups' => [
                'protective-gear' => 'Protective Gear',
                'magic-item' => 'Magic Items',
                'magical-armour' => 'Magical Foil Armour',
                'legendary-armour' => 'Legendary Armour',
                'legendary-weapon' => 'Legendary Weapons',
            ],
        ];
    }
}

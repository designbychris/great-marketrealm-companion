<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Relics\Repositories;

use GreatMarketrealmCompanion\Modules\Library\Relics\Models\RelicRecord;

defined('ABSPATH') || exit;

final class HandbookRelicRegister
{
    /** @var array<string,RelicRecord>|null */
    private ?array $records = null;

    /** @return RelicRecord[] */
    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $key): ?RelicRecord
    {
        return $this->records()[sanitize_key($key)] ?? null;
    }

    /** @return RelicRecord[] */
    public function byGroup(string $group): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (RelicRecord $record): bool =>
                    $record->group() === $group
            )
        );
    }

    /** @return RelicRecord[] */
    public function byRarity(string $rarity): array
    {
        $needle = strtolower(trim($rarity));

        return array_values(
            array_filter(
                $this->all(),
                static fn (RelicRecord $record): bool =>
                    strtolower($record->rarity()) === $needle
            )
        );
    }

    /** @return array<string,RelicRecord> */
    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $source = require dirname(__DIR__) . '/Data/handbook-relics.php';
        $records = [];

        foreach ($source as $entry) {
            $record = new RelicRecord(
                (string) $entry['key'],
                (string) $entry['name'],
                (string) $entry['group'],
                (string) $entry['item_type'],
                (string) $entry['rarity'],
                isset($entry['attunement'])
                    ? (string) $entry['attunement']
                    : null,
                is_array($entry['mechanics'] ?? null)
                    ? $entry['mechanics']
                    : [],
                isset($entry['base_profile'])
                    ? (string) $entry['base_profile']
                    : null,
                isset($entry['flavour'])
                    ? (string) $entry['flavour']
                    : null
            );

            $records[$record->key()] = $record;
        }

        return $this->records = $records;
    }
}

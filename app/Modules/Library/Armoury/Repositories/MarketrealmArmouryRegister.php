<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories;

use GreatMarketrealmCompanion\Modules\Library\Armoury\Models\ArmouryRecord;

defined('ABSPATH') || exit;

final class MarketrealmArmouryRegister
{
    /** @var array<string,ArmouryRecord>|null */
    private ?array $records = null;

    /** @return ArmouryRecord[] */
    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $id): ?ArmouryRecord
    {
        return $this->records()[sanitize_key($id)] ?? null;
    }

    /** @return ArmouryRecord[] */
    public function byCategory(string $category): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (ArmouryRecord $record): bool =>
                    $record->category() === $category
            )
        );
    }

    /** @return ArmouryRecord[] */
    public function byProvenance(string $provenance): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (ArmouryRecord $record): bool =>
                    $record->provenance() === $provenance
            )
        );
    }

    /** @return array<string,ArmouryRecord> */
    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $source = require dirname(__DIR__) . '/Data/mundane-armoury.php';
        $records = [];

        foreach ($source as $entry) {
            $record = new ArmouryRecord(
                (string) $entry[0],
                (string) $entry[1],
                (string) $entry[2],
                (string) $entry[3],
                (float) $entry[4],
                (string) $entry[5],
                $entry[6] !== null ? (string) $entry[6] : null,
                $entry[7] !== null ? (string) $entry[7] : null,
                $entry[8] !== null ? (string) $entry[8] : null,
                $entry[9] !== null ? (int) $entry[9] : null,
                $entry[10] !== null ? (int) $entry[10] : null,
                (int) $entry[11],
                is_array($entry[12] ?? null) ? $entry[12] : [],
                $entry[13] !== null ? (string) $entry[13] : null
            );

            $records[$record->id()] = $record;
        }

        return $this->records = $records;
    }
}

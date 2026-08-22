<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;

defined('ABSPATH') || exit;

final class CanonicalBestiary
{
    /** @var array<string,CanonicalMonster>|null */
    private ?array $records = null;

    /** @return CanonicalMonster[] */
    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $id): ?CanonicalMonster
    {
        $key = str_starts_with($id, 'canonical:') ? substr($id, 10) : $id;
        return $this->records()[sanitize_key($key)] ?? null;
    }

    /** @return array<string,CanonicalMonster> */
    private function records(): array
    {
        if ($this->records !== null) { return $this->records; }
        $source = require dirname(__DIR__) . '/Data/dungeon-master-guide-monsters.php';
        $records = [];
        foreach ($source as $entry) {
            $record = new CanonicalMonster($entry);
            $records[$record->key()] = $record;
        }
        return $this->records = $records;
    }
}

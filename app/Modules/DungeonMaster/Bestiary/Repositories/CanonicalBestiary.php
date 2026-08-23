<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories;

use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalBestiarySteward;
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

    public function flush(): void
    {
        $this->records = null;
    }

    /** @return array<string,CanonicalMonster> */
    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $source = require dirname(__DIR__) . '/Data/dungeon-master-guide-monsters.php';
        $overrides = get_option(CanonicalBestiarySteward::OPTION, []);
        $overrides = is_array($overrides) ? $overrides : [];
        $records = [];

        foreach ($source as $entry) {
            $key = sanitize_key((string) ($entry['key'] ?? ''));
            $override = isset($overrides[$key]) && is_array($overrides[$key])
                ? $overrides[$key]
                : [];
            $record = new CanonicalMonster(array_merge($entry, $override, ['key' => $key]));
            $records[$record->key()] = $record;
        }

        return $this->records = $records;
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories;

use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalBestiarySteward;
use GreatMarketrealmCompanion\Modules\Administration\Workshop\MonsterWorkshop;
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
        $key = str_starts_with($id, 'canonical:') ? substr($id, 10) : (str_starts_with($id, 'steward:') ? substr($id, 8) : $id);
        return $this->records()[sanitize_key($key)] ?? null;
    }

    public function findCanonical(string $id): ?CanonicalMonster
    {
        $record = $this->find($id);
        return $record instanceof CanonicalMonster && $record->isCanonical() ? $record : null;
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
        $publications = require dirname(__DIR__) . '/Data/guild-field-guide-publications.php';
        $publications = is_array($publications) ? $publications : [];
        $overrides = get_option(CanonicalBestiarySteward::OPTION, []);
        $overrides = is_array($overrides) ? $overrides : [];
        $records = [];

        foreach ($source as $entry) {
            $key = sanitize_key((string) ($entry['key'] ?? ''));
            $publication = isset($publications[$key])
                ? [
                    'field_guide_visible' => true,
                    'player_description' => (string) $publications[$key],
                ]
                : [];
            $override = isset($overrides[$key]) && is_array($overrides[$key])
                ? $overrides[$key]
                : [];
            $record = new CanonicalMonster(array_merge(
                $entry,
                $publication,
                $override,
                ['key' => $key]
            ));
            $records[$record->key()] = $record;
        }

        $workshop = get_option(MonsterWorkshop::OPTION, []);
        if (is_array($workshop)) {
            foreach ($workshop as $key => $entry) {
                if (! is_array($entry) || ($entry['status'] ?? '') !== MonsterWorkshop::STATUS_PUBLISHED) {
                    continue;
                }
                $entry['key'] = sanitize_key((string) $key);
                $entry['origin'] = 'steward';
                $records[$entry['key']] = new CanonicalMonster($entry);
            }
        }

        return $this->records = $records;
    }
}

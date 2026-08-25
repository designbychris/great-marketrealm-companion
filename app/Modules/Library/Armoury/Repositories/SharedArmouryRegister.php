<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories;

use GreatMarketrealmCompanion\Modules\Administration\Workshop\EquipmentWorkshop;
use GreatMarketrealmCompanion\Modules\Library\Armoury\Models\ArmouryRecord;

defined('ABSPATH') || exit;

/** Effective Armoury: protected records plus published Steward-created equipment. */
final class SharedArmouryRegister
{
    public function __construct(private ?MarketrealmArmouryRegister $canonical = null, private ?EquipmentWorkshop $workshop = null)
    {
        $this->canonical ??= new MarketrealmArmouryRegister();
        $this->workshop ??= new EquipmentWorkshop();
    }

    /** @return ArmouryRecord[] */
    public function all(): array
    {
        $records = [];
        foreach ($this->canonical->all() as $record) $records[$record->id()] = $record;
        foreach ($this->workshop->published() as $entry) {
            $record = $this->record($entry);
            if ($record !== null && ! isset($records[$record->id()])) $records[$record->id()] = $record;
        }
        return array_values($records);
    }

    /** Resolve canonical, published, or archived items already held by a Character. Drafts never resolve. */
    public function find(string $id): ?ArmouryRecord
    {
        $id = sanitize_key($id);
        $canonical = $this->canonical->find($id);
        if ($canonical !== null) return $canonical;
        $entry = $this->workshop->find($id);
        if (! is_array($entry) || ($entry['status'] ?? '') === EquipmentWorkshop::STATUS_DRAFT) return null;
        return $this->record($entry);
    }

    /** @return ArmouryRecord[] */
    public function byCategory(string $category): array
    {
        return array_values(array_filter($this->all(), static fn (ArmouryRecord $record): bool => $record->category() === $category));
    }

    /** @param array<string,mixed> $entry */
    private function record(array $entry): ?ArmouryRecord
    {
        $id = sanitize_key((string) ($entry['key'] ?? ''));
        if ($id === '') return null;
        return new ArmouryRecord(
            $id,
            (string) ($entry['name'] ?? ''),
            (string) ($entry['category'] ?? 'gear'),
            (string) ($entry['description'] ?? ''),
            (float) ($entry['weight'] ?? 0),
            'steward-created',
            isset($entry['equip_slot']) ? (string) $entry['equip_slot'] : null,
            isset($entry['damage_die']) ? (string) $entry['damage_die'] : null,
            isset($entry['damage_type']) ? (string) $entry['damage_type'] : null,
            isset($entry['armour_base']) ? (int) $entry['armour_base'] : null,
            isset($entry['dexterity_cap']) ? (int) $entry['dexterity_cap'] : null,
            (int) ($entry['armour_bonus'] ?? 0),
            is_array($entry['properties'] ?? null) ? $entry['properties'] : [],
            isset($entry['range']) ? (string) $entry['range'] : null
        );
    }
}

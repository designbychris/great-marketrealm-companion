<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Services;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/** Builds Ledger-ready inventory summaries without coupling the Character entity to WordPress persistence. */
final class InventoryPresenter
{
    public function __construct(private ItemCatalogue $catalogue) {}

    public function present(Character $character, CharacterInventory $inventory): array
    {
        $rows = [];
        foreach ($inventory->entries() as $entry) {
            $item = $this->catalogue->find($entry->itemId());
            if ($item === null) { continue; }
            $rows[] = [
                'id' => $item->id(),
                'label' => $item->label(),
                'category' => $item->category(),
                'description' => $item->description(),
                'weight' => $item->weight(),
                'quantity' => $entry->quantity(),
                'total_weight' => round($item->weight() * $entry->quantity(), 2),
                'equipped' => $entry->equipped(),
                'equippable' => $item->isEquippable(),
                'slot' => $item->equipSlot(),
                'damage_die' => $item->damageDie(),
                'damage_type' => $item->damageType(),
                'properties' => $item->properties(),
            ];
        }

        $strength = $character->abilityScores()->strength()->value();
        $capacity = max(1, $strength * 15);
        $weight = $inventory->totalWeight($this->catalogue);

        return [
            'rows' => $rows,
            'total_weight' => $weight,
            'capacity' => $capacity,
            'load_percent' => min(100, (int) round(($weight / $capacity) * 100)),
            'catalogue' => $this->catalogue->all(),
            'equipped_count' => count($inventory->equipped()),
        ];
    }

    public function armourClass(Character $character, CharacterInventory $inventory): int
    {
        $dexterity = $character->abilityScores()->dexterity()->modifier();
        $base = 10 + $dexterity;
        $shield = 0;

        foreach ($inventory->equipped() as $entry) {
            $item = $this->catalogue->find($entry->itemId());
            if ($item === null) { continue; }
            if ($item->armourBase() !== null) {
                $dex = $dexterity;
                if ($item->dexterityCap() !== null) { $dex = min($dex, $item->dexterityCap()); }
                $base = $item->armourBase() + $dex;
            }
            $shield += $item->armourBonus();
        }

        return $base + $shield;
    }
}

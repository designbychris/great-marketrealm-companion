<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Models;

defined('ABSPATH') || exit;

/**
 * Character-owned inventory aggregate.
 */
final class CharacterInventory
{
    /** @var array<string, InventoryEntry> */
    private array $entries = [];

    /** @param InventoryEntry[] $entries */
    public function __construct(array $entries = [])
    {
        foreach ($entries as $entry) {
            if ($entry instanceof InventoryEntry && $entry->itemId() !== '') {
                $this->entries[$entry->itemId()] = $entry;
            }
        }
    }

    public static function empty(): self { return new self(); }

    /** @return InventoryEntry[] */
    public function entries(): array { return array_values($this->entries); }
    public function isEmpty(): bool { return $this->entries === []; }
    public function find(string $itemId): ?InventoryEntry { return $this->entries[$itemId] ?? null; }

    public function add(string $itemId, int $quantity = 1): self
    {
        $copy = clone $this;
        $current = $copy->entries[$itemId] ?? null;
        $copy->entries[$itemId] = $current instanceof InventoryEntry
            ? $current->withQuantity($current->quantity() + max(1, $quantity))
            : new InventoryEntry($itemId, max(1, $quantity));
        return $copy;
    }

    public function setQuantity(string $itemId, int $quantity): self
    {
        $copy = clone $this;
        $current = $copy->entries[$itemId] ?? null;
        if (! $current instanceof InventoryEntry) { return $copy; }
        if ($quantity <= 0) { unset($copy->entries[$itemId]); return $copy; }
        $copy->entries[$itemId] = $current->withQuantity($quantity);
        return $copy;
    }

    public function remove(string $itemId): self
    {
        $copy = clone $this;
        unset($copy->entries[$itemId]);
        return $copy;
    }

    public function equip(string $itemId, ItemCatalogue $catalogue): self
    {
        $copy = clone $this;
        $entry = $copy->entries[$itemId] ?? null;
        $definition = $catalogue->find($itemId);
        if (! $entry instanceof InventoryEntry || ! $definition instanceof ItemDefinition || ! $definition->isEquippable()) {
            return $copy;
        }

        $slot = $definition->equipSlot();
        foreach ($copy->entries as $key => $candidate) {
            $candidateDefinition = $catalogue->find($key);
            if ($candidateDefinition instanceof ItemDefinition && $candidateDefinition->equipSlot() === $slot) {
                $copy->entries[$key] = $candidate->withEquipped(false);
            }
        }
        $copy->entries[$itemId] = $entry->withEquipped(true);
        return $copy;
    }

    public function unequip(string $itemId): self
    {
        $copy = clone $this;
        $entry = $copy->entries[$itemId] ?? null;
        if ($entry instanceof InventoryEntry) { $copy->entries[$itemId] = $entry->withEquipped(false); }
        return $copy;
    }

    /** @return InventoryEntry[] */
    public function equipped(): array
    {
        return array_values(array_filter($this->entries, static fn (InventoryEntry $entry): bool => $entry->equipped()));
    }

    public function totalWeight(ItemCatalogue $catalogue): float
    {
        $weight = 0.0;
        foreach ($this->entries as $entry) {
            $definition = $catalogue->find($entry->itemId());
            if ($definition instanceof ItemDefinition) { $weight += $definition->weight() * $entry->quantity(); }
        }
        return round($weight, 2);
    }

    public function toArray(): array
    {
        return array_map(static fn (InventoryEntry $entry): array => $entry->toArray(), $this->entries());
    }

    public static function fromArray(array $data): self
    {
        return new self(array_map(static fn (array $entry): InventoryEntry => InventoryEntry::fromArray($entry), array_values(array_filter($data, 'is_array'))));
    }
}

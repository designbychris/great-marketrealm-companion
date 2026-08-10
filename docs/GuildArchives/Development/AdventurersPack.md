# Character Lifecycle Initiative — Phase III.3: The Adventurer's Pack

Phase III.3 introduces character-owned equipment and inventory to the Open Ledger.

## Domain model

Inventory is deliberately kept as a character-owned aggregate rather than being added to the `Character` constructor. This protects the established registration lifecycle while allowing equipment systems to grow independently.

The first domain layer contains:

- `ItemDefinition` — catalogue metadata and future combat hooks;
- `ItemCatalogue` — the curated Marketrealm equipment registry;
- `InventoryEntry` — quantity, equipped state and notes;
- `CharacterInventory` — stacking, quantity, equip/unequip and carrying weight;
- `CharacterInventoryRepository` — WordPress character-meta persistence;
- `InventoryPresenter` — Ledger presentation, carrying capacity and equipment-aware Armour Class.

## First catalogue

The Phase III.3 shelves intentionally stay small while covering each important behaviour: weapons, armour, shield, tools, gear and consumables.

Weapon definitions already expose damage dice, damage type and properties so Phase III.4 can feed them directly into Guild Dice attack and damage rolls.

Armour definitions expose base AC, Dexterity cap and bonuses. The Open Ledger can therefore display equipment-aware AC as soon as armour or a shield is equipped.

## Open Ledger

The new **Equipment** tab contains two pages:

1. **Auby's Packing Register** — carried items, quantities, pack load, equipped state and item management;
2. **Quartermaster's Counter** — the starter catalogue and Add to Adventurer's Pack form.

Inventory mutations deep-link back to the Equipment tab using `gmrc_ledger_tab=equipment`.

## Next hand-off

Phase III.4 can build Attacks & Combat using the equipped weapon definitions without changing the inventory schema.

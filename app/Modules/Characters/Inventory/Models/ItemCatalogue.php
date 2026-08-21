<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Models;

use GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories\MarketrealmArmouryRegister;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Curated Phase III.3 equipment catalogue.
 */
final class ItemCatalogue
{
    /** @var array<string, ItemDefinition> */
    private array $items = [];

    public function __construct(
        ?MarketrealmArmouryRegister $armoury = null
    ) {
        $this->registerDefaults();
        $this->registerArmoury(
            $armoury ?? new MarketrealmArmouryRegister()
        );
    }

    public function add(ItemDefinition $item): void { $this->items[$item->id()] = $item; }
    public function find(string $id): ?ItemDefinition { return $this->items[$id] ?? null; }
    /** @return ItemDefinition[] */
    public function all(): array { return array_values($this->items); }

    /** @return array<string, ItemDefinition[]> */
    public function grouped(): array
    {
        $groups = [];
        foreach ($this->items as $item) { $groups[$item->category()][] = $item; }
        return $groups;
    }


    private function registerArmoury(
        MarketrealmArmouryRegister $armoury
    ): void {
        foreach ($armoury->all() as $record) {
            if ($this->find($record->id()) !== null) {
                continue;
            }

            $this->add(
                new ItemDefinition(
                    $record->id(),
                    $record->label(),
                    $record->category(),
                    $record->description(),
                    $record->weight(),
                    $record->equipSlot(),
                    $record->damageDie(),
                    $record->damageType(),
                    $record->armourBase(),
                    $record->dexterityCap(),
                    $record->armourBonus(),
                    $record->properties(),
                    $record->range()
                )
            );
        }
    }

    private function registerDefaults(): void
    {
        $this->add(new ItemDefinition('market-cleaver', 'Market Cleaver', 'weapon', 'A dependable butcher’s cleaver balanced for adventuring.', 2.0, 'main-hand', '1d6', 'slashing', null, null, 0, ['light']));
        $this->add(new ItemDefinition('paring-knife', 'Paring Knife', 'weapon', 'Small, quick and surprisingly useful when negotiations spoil.', 1.0, 'main-hand', '1d4', 'piercing', null, null, 0, ['finesse', 'light']));
        $this->add(new ItemDefinition('rolling-pin', 'Battle Rolling Pin', 'weapon', 'A hardwood pin reinforced with brass end caps.', 3.0, 'main-hand', '1d6', 'bludgeoning', null, null, 0, ['versatile']));
        $this->add(new ItemDefinition('produce-leathers', 'Produce Leathers', 'armour', 'Layered market leathers with reinforced apron panels.', 10.0, 'body', null, null, 11, null));
        $this->add(new ItemDefinition('pantry-mail', 'Pantry Mail', 'armour', 'Interlocking pantry tags and metal scales.', 28.0, 'body', null, null, 14, 2));
        $this->add(new ItemDefinition('serving-tray-shield', 'Serving Tray Shield', 'shield', 'A broad silvered tray with a stout leather grip.', 6.0, 'off-hand', null, null, null, null, 2));
        $this->add(new ItemDefinition('adventurers-satchel', 'Adventurer’s Satchel', 'gear', 'A weathered pack with loops, pockets and emergency snack space.', 5.0));
        $this->add(new ItemDefinition('guild-lantern', 'Guild Lantern', 'gear', 'A brass lantern bearing the Guild flame.', 2.0));
        $this->add(new ItemDefinition('hemp-twine', 'Hemp Twine (50 ft)', 'gear', 'Strong enough for parcels, traps and questionable climbing plans.', 5.0));
        $this->add(new ItemDefinition('grocers-scales', 'Grocer’s Scales', 'tool', 'Pocket balance scales for produce, coin and suspicious mushrooms.', 3.0));
        $this->add(new ItemDefinition('restorative-jam', 'Restorative Jam', 'consumable', 'A sealed jar of bright berry preserve used as a field restorative.', 0.5));
        $this->add(new ItemDefinition('emergency-biscuit', 'Emergency Biscuit', 'consumable', 'Guild regulations insist this is for emergencies only. Auby disagrees.', 0.2));
    }
}

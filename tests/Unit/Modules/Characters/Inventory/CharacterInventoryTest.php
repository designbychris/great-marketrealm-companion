<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Inventory;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use PHPUnit\Framework\TestCase;

final class CharacterInventoryTest extends TestCase
{
    public function testItemsCanBeAddedAndStacked(): void
    {
        $inventory = CharacterInventory::empty()
            ->add('emergency-biscuit', 2)
            ->add('emergency-biscuit', 3);

        self::assertSame(
            5,
            $inventory->find('emergency-biscuit')?->quantity()
        );
    }

    public function testEquippingAWeaponReplacesTheSameSlot(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()
            ->add('market-cleaver')
            ->add('paring-knife')
            ->equip('market-cleaver', $catalogue)
            ->equip('paring-knife', $catalogue);

        self::assertFalse(
            $inventory->find('market-cleaver')?->equipped()
            ?? true
        );

        self::assertTrue(
            $inventory->find('paring-knife')?->equipped()
            ?? false
        );
    }

    public function testInventoryRoundTripsThroughArrayStorage(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()
            ->add('produce-leathers')
            ->equip('produce-leathers', $catalogue);

        $restored = CharacterInventory::fromArray(
            $inventory->toArray()
        );

        self::assertTrue(
            $restored->find('produce-leathers')?->equipped()
            ?? false
        );
    }
}

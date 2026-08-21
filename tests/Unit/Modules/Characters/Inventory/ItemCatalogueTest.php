<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Inventory;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use PHPUnit\Framework\TestCase;

final class ItemCatalogueTest extends TestCase
{
    public function testCatalogueContainsWeaponsArmourAndConsumables(): void
    {
        $catalogue = new ItemCatalogue();

        self::assertSame('1d6', $catalogue->find('market-cleaver')?->damageDie());
        self::assertSame(11, $catalogue->find('produce-leathers')?->armourBase());
        self::assertSame('consumable', $catalogue->find('emergency-biscuit')?->category());
    }

    public function testArmouryExpansionPreservesLegacyItems(): void
    {
        $catalogue = new ItemCatalogue();

        self::assertSame(
            'Market Cleaver',
            $catalogue->find('market-cleaver')?->label()
        );
        self::assertSame(
            'Produce Leathers',
            $catalogue->find('produce-leathers')?->label()
        );
        self::assertSame(
            'Emergency Biscuit',
            $catalogue->find('emergency-biscuit')?->label()
        );
    }

    public function testArmouryExpansionAddsStandardWeaponsAndArmour(): void
    {
        $catalogue = new ItemCatalogue();

        self::assertSame(
            '1d8',
            $catalogue->find('longsword')?->damageDie()
        );
        self::assertSame(
            'Ranged · 150/600 ft',
            $catalogue->find('longbow')?->range()
        );
        self::assertSame(
            18,
            $catalogue->find('plate-armour')?->armourBase()
        );
        self::assertSame(
            2,
            $catalogue->find('shield')?->armourBonus()
        );
    }

}

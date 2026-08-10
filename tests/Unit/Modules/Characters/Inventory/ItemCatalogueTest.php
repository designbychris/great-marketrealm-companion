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
}

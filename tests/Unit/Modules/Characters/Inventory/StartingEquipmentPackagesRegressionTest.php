<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Inventory;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;
use PHPUnit\Framework\TestCase;

final class StartingEquipmentPackagesRegressionTest extends TestCase
{
    public function testEveryGrandCatalogueCallingHasAtLeastTwoStartingKits(): void
    {
        $register = new StartingEquipmentPackageRegister();
        foreach (['artificer','barbarian','bard','cleric','druid','fighter','monk','paladin','ranger','rogue','sorcerer','warlock','wizard'] as $class) {
            self::assertGreaterThanOrEqual(2, count($register->forClass($class)), $class . ' needs choice coverage.');
        }
    }

    public function testEveryPackageItemResolvesThroughSharedArmouryCatalogue(): void
    {
        $register = new StartingEquipmentPackageRegister();
        $catalogue = new ItemCatalogue();
        foreach ($register->all() as $package) {
            self::assertNotEmpty($package->items());
            foreach ($package->items() as $itemId => $quantity) {
                self::assertNotNull($catalogue->find($itemId), $package->id() . ' contains unknown item ' . $itemId);
                self::assertGreaterThan(0, $quantity);
            }
        }
    }

    public function testPackageRegisterIsTestSafeWithoutWordPressOptions(): void
    {
        $register = new StartingEquipmentPackageRegister();
        self::assertCount(26, $register->all());
        self::assertSame('fighter', $register->find('fighter-sword')?->classKey());
    }

    public function testCharacterCreationOffersAndSnapshotsStartingEquipment(): void
    {
        $root = dirname(__DIR__, 5);
        $controller = file_get_contents($root . '/app/Modules/Characters/Controllers/CharacterController.php');
        $view = file_get_contents($root . '/app/Modules/Characters/Views/create.php');
        self::assertStringContainsString("'startingEquipmentPackages'", (string) $controller);
        self::assertStringContainsString('StartingEquipmentGrantService', (string) $controller);
        self::assertStringContainsString('name="starting_equipment_package"', (string) $view);
        self::assertStringContainsString('data-catalogue-child="starting-equipment"', (string) $view);
    }

    public function testStoreRequestAcceptsDedicatedPackageIdentity(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents($root . '/app/Modules/Characters/Requests/StoreCharacterRequest.php');
        self::assertStringContainsString("'starting_equipment_package'", (string) $source);
        self::assertStringContainsString('startingEquipmentPackage()', (string) $source);
    }

    public function testStewardsOfficeOwnsPackageSaveAndResetActions(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents($root . '/app/Providers/AdministrationServiceProvider.php');
        $office = file_get_contents($root . '/app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('gmrc_save_starting_equipment_package', (string) $provider);
        self::assertStringContainsString('gmrc_reset_starting_equipment_package', (string) $provider);
        self::assertStringContainsString("'section' => 'starting-equipment'", (string) $office);
    }

    public function testPackageSourceDoesNotPretendMissingHandbookTableIsCanonical(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents($root . '/app/Modules/Characters/Inventory/Data/starting-equipment-packages.php');
        self::assertStringContainsString('does not currently provide a complete', (string) $source);
        self::assertStringContainsString('Companion certified starting kit', file_get_contents($root . '/app/Modules/Characters/Inventory/Models/StartingEquipmentPackage.php'));
    }
}

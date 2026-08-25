<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardEquipmentWorkshopRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testDraftItemStaysOutOfSharedArmoury(): void
    {
        $shared = $this->source('app/Modules/Library/Armoury/Repositories/SharedArmouryRegister.php');
        self::assertStringContainsString('foreach ($this->workshop->published() as $entry)', $shared);
        self::assertStringContainsString("=== EquipmentWorkshop::STATUS_DRAFT", $shared);
    }

    public function testPublishedGearJoinsSharedArmouryAndCharacterCatalogue(): void
    {
        $shared = $this->source('app/Modules/Library/Armoury/Repositories/SharedArmouryRegister.php');
        $catalogue = $this->source('app/Modules/Characters/Inventory/Models/ItemCatalogue.php');
        self::assertStringContainsString('$this->workshop->published()', $shared);
        self::assertStringContainsString('SharedArmouryRegister', $catalogue);
        self::assertStringContainsString('$this->registerArmoury($this->armoury)', $catalogue);
    }

    public function testArchivedItemIsHiddenFromNewSelectionButStillResolves(): void
    {
        $shared = $this->source('app/Modules/Library/Armoury/Repositories/SharedArmouryRegister.php');
        self::assertStringContainsString('$this->workshop->published()', $shared);
        self::assertStringContainsString('$entry = $this->workshop->find($id)', $shared);
        self::assertStringContainsString("STATUS_DRAFT) return null", $shared);
    }

    public function testPublishedWeaponRequiresMechanicalDamageFields(): void
    {
        $workshop = $this->source('app/Modules/Administration/Workshop/EquipmentWorkshop.php');
        self::assertStringContainsString('$category === \'weapon\'', $workshop);
        self::assertStringContainsString('valid damage die and damage type', $workshop);
    }

    public function testPublishedArmourRequiresBodySlotAndArmourBase(): void
    {
        $workshop = $this->source('app/Modules/Administration/Workshop/EquipmentWorkshop.php');
        self::assertStringContainsString('$category === \'armour\'', $workshop);
        self::assertStringContainsString('body slot and an armour base', $workshop);
    }

    public function testPublishedShieldRequiresOffHandBonus(): void
    {
        $workshop = $this->source('app/Modules/Administration/Workshop/EquipmentWorkshop.php');
        self::assertStringContainsString('$category === \'shield\'', $workshop);
        self::assertStringContainsString('off-hand slot and a non-zero armour bonus', $workshop);
    }

    public function testPublishedWeaponProjectsDamageAndProperties(): void
    {
        $shared = $this->source('app/Modules/Library/Armoury/Repositories/SharedArmouryRegister.php');
        foreach (['damage_die', 'damage_type', 'properties', 'range'] as $field) {
            self::assertStringContainsString($field, $shared);
        }
    }

    public function testProtectedCanonicalArmouryStillWins(): void
    {
        $shared = $this->source('app/Modules/Library/Armoury/Repositories/SharedArmouryRegister.php');
        self::assertStringContainsString('foreach ($this->canonical->all() as $record)', $shared);
        self::assertStringContainsString('! isset($records[$record->id()])', $shared);
        self::assertStringContainsString('$canonical = $this->canonical->find($id)', $shared);
        self::assertStringContainsString('if ($canonical !== null) return $canonical', $shared);
    }

    public function testWorkshopUsesStableStewardItemKeys(): void
    {
        $workshop = $this->source('app/Modules/Administration/Workshop/EquipmentWorkshop.php');
        self::assertStringContainsString("'steward-item-'", $workshop);
        self::assertStringContainsString('$this->uniqueKey($name, $records)', $workshop);
        self::assertStringContainsString('is_array($existing) ? sanitize_key($key)', $workshop);
    }

    public function testAdministrationProviderWiresEquipmentWorkshop(): void
    {
        $provider = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('EquipmentWorkshop::class', $provider);
        self::assertStringContainsString('admin_post_gmrc_save_steward_equipment', $provider);
        self::assertStringContainsString("section' => 'equipment-workshop", $provider);
    }

    public function testWorkshopAndArmouryViewsExplainPublicationProvenance(): void
    {
        $workshop = $this->source('app/Modules/Administration/Views/equipment-workshop.php');
        $armoury = $this->source('app/Modules/Library/Views/armoury/index.php');
        self::assertStringContainsString('Equipment &amp; Item Workshop', $workshop);
        self::assertStringContainsString('Steward creation', $armoury);
        self::assertStringNotContainsString('III.16.19C', $workshop);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}

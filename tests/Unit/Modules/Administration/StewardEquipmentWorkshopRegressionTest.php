<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use GreatMarketrealmCompanion\Modules\Administration\Workshop\EquipmentWorkshop;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories\SharedArmouryRegister;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class StewardEquipmentWorkshopRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        update_option(EquipmentWorkshop::OPTION, [], false);
    }

    protected function tearDown(): void
    {
        update_option(EquipmentWorkshop::OPTION, [], false);
        parent::tearDown();
    }

    public function testDraftItemStaysOutOfSharedArmoury(): void
    {
        $key = (new EquipmentWorkshop())->save('', ['name' => 'Turnip Hammer', 'status' => 'draft']);
        self::assertNull((new SharedArmouryRegister())->find($key));
        self::assertNotContains($key, array_map(static fn ($r) => $r->id(), (new SharedArmouryRegister())->all()));
    }

    public function testPublishedGearJoinsSharedArmouryAndCharacterCatalogue(): void
    {
        $key = (new EquipmentWorkshop())->save('', $this->gear());
        self::assertSame('Turnip Hammer', (new SharedArmouryRegister())->find($key)?->label());
        self::assertSame('Turnip Hammer', (new ItemCatalogue())->find($key)?->label());
        self::assertContains($key, array_map(static fn ($i) => $i->id(), (new ItemCatalogue())->all()));
    }

    public function testArchivedItemIsHiddenFromNewSelectionButStillResolves(): void
    {
        $workshop = new EquipmentWorkshop();
        $key = $workshop->save('', $this->gear());
        $workshop->save($key, array_merge($this->gear(), ['status' => 'archived']));
        self::assertNotContains($key, array_map(static fn ($r) => $r->id(), (new SharedArmouryRegister())->all()));
        self::assertSame('Turnip Hammer', (new SharedArmouryRegister())->find($key)?->label());
        self::assertSame('Turnip Hammer', (new ItemCatalogue())->find($key)?->label());
        self::assertNotContains($key, array_map(static fn ($i) => $i->id(), (new ItemCatalogue())->all()));
    }

    public function testPublishedWeaponRequiresMechanicalDamageFields(): void
    {
        $this->expectException(RuntimeException::class);
        (new EquipmentWorkshop())->save('', ['name' => 'Bad Blade', 'status' => 'published', 'category' => 'weapon', 'description' => 'No dice.', 'weight' => '2']);
    }

    public function testPublishedArmourRequiresBodySlotAndArmourBase(): void
    {
        $this->expectException(RuntimeException::class);
        (new EquipmentWorkshop())->save('', ['name' => 'Bad Apron', 'status' => 'published', 'category' => 'armour', 'description' => 'Uncertified.', 'weight' => '10', 'equip_slot' => 'main-hand']);
    }

    public function testPublishedShieldRequiresOffHandBonus(): void
    {
        $this->expectException(RuntimeException::class);
        (new EquipmentWorkshop())->save('', ['name' => 'Bad Tray', 'status' => 'published', 'category' => 'shield', 'description' => 'Uncertified.', 'weight' => '5', 'equip_slot' => 'off-hand', 'armour_bonus' => '0']);
    }

    public function testPublishedWeaponProjectsDamageAndProperties(): void
    {
        $key = (new EquipmentWorkshop())->save('', [
            'name' => 'Carrot Rapier', 'status' => 'published', 'category' => 'weapon', 'description' => 'Pointedly orange.', 'weight' => '2',
            'equip_slot' => 'main-hand', 'damage_die' => '1d8', 'damage_type' => 'piercing', 'properties' => 'finesse, light', 'range' => 'Melee · 5 ft',
        ]);
        $item = (new ItemCatalogue())->find($key);
        self::assertSame('1d8', $item?->damageDie());
        self::assertSame('piercing', $item?->damageType());
        self::assertContains('finesse', $item?->properties() ?? []);
    }

    public function testProtectedCanonicalArmouryStillWins(): void
    {
        update_option(EquipmentWorkshop::OPTION, ['longsword' => array_merge($this->gear(), ['key' => 'longsword', 'name' => 'Fake Longsword'])], false);
        self::assertNotSame('Fake Longsword', (new SharedArmouryRegister())->find('longsword')?->label());
    }

    public function testWorkshopUsesStableStewardItemKeys(): void
    {
        $key = (new EquipmentWorkshop())->save('', $this->gear());
        self::assertStringStartsWith('steward-item-', $key);
        self::assertSame($key, (new EquipmentWorkshop())->save($key, array_merge($this->gear(), ['name' => 'Renamed Hammer'])));
    }

    public function testAdministrationProviderWiresEquipmentWorkshop(): void
    {
        $source = file_get_contents(GMRC_PATH . 'app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('EquipmentWorkshop::class', (string) $source);
        self::assertStringContainsString("admin_post_gmrc_save_steward_equipment", (string) $source);
        self::assertStringContainsString("section' => 'equipment-workshop", (string) $source);
    }

    public function testWorkshopAndArmouryViewsExplainPublicationProvenance(): void
    {
        $workshop = file_get_contents(GMRC_PATH . 'app/Modules/Administration/Views/equipment-workshop.php');
        $armoury = file_get_contents(GMRC_PATH . 'app/Modules/Library/Views/armoury/index.php');
        self::assertStringContainsString('Equipment &amp; Item Workshop', (string) $workshop);
        self::assertStringContainsString('Steward creation', (string) $armoury);
        self::assertStringNotContainsString('III.16.19C', (string) $workshop);
    }

    /** @return array<string,mixed> */
    private function gear(): array
    {
        return ['name' => 'Turnip Hammer', 'status' => 'published', 'category' => 'gear', 'description' => 'A very serious turnip hammer.', 'weight' => '3.5'];
    }
}

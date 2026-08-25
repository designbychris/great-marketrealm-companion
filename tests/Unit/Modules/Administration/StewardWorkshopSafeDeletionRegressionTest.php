<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardWorkshopSafeDeletionRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testAdministrationProviderRegistersDedicatedDeletionAction(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString("admin_post_gmrc_delete_steward_record", $source);
        self::assertStringContainsString('check_admin_referer(\'gmrc_delete_steward_\' . $type . \'_\' . $key', $source);
    }

    public function testDeletionIsCapabilityGuardedBeforeMutation(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        $method = $this->method($source, 'deleteStewardRecord');
        self::assertStringContainsString('$this->guard();', $method);
        self::assertStringContainsString('assertDeletable($type, $key)', $method);
    }

    public function testOnlyStewardKeysMayCrossDeletionGuard(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString('str_starts_with($key, \'steward-\')', $source);
        self::assertStringContainsString('Only Steward-authored Workshop records may be permanently deleted.', $source);
    }

    public function testCallingDependenciesCoverClassAndCallingPath(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'_gmrc_class'", $source);
        self::assertStringContainsString("'_gmrc_subclass'", $source);
        self::assertStringContainsString("CallingWorkshop::OPTION", $source);
    }

    public function testSpellDependenciesCoverPersistedSpellbooks(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'_gmrc_spellbook'", $source);
        self::assertStringContainsString('$book[\'spells\']', $source);
        self::assertStringContainsString('$book[\'cantrips\']', $source);
    }

    public function testBackgroundDependenciesCoverCharacterBackgroundIdentity(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'_gmrc_background'", $source);
    }

    public function testEquipmentDependenciesCoverSatchelItemIds(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'_gmrc_inventory'", $source);
        self::assertStringContainsString('$entry[\'item_id\']', $source);
    }

    public function testMonsterDependenciesCoverEncounterSnapshots(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'gmrc_encounter'", $source);
        self::assertStringContainsString("'_gmrc_encounter_monster_groups'", $source);
        self::assertStringContainsString('$group[\'monster_id\']', $source);
    }

    public function testAllWorkshopRepositoriesExposePermanentDeleteMutation(): void
    {
        foreach (['Monster', 'Spell', 'Background', 'Equipment', 'Calling'] as $name) {
            $source = $this->source('app/Modules/Administration/Workshop/' . $name . 'Workshop.php');
            self::assertStringContainsString('public function delete(string $key): void', $source, $name);
            self::assertStringContainsString('unset($records[$key]);', $source, $name);
        }
    }

    public function testDangerZoneUsesDedicatedNonceAndExplicitPermanentDeleteCopy(): void
    {
        $source = $this->source('app/Modules/Administration/Views/_steward-delete.php');
        self::assertStringContainsString('gmrc_delete_steward_record', $source);
        self::assertStringContainsString('gmrc_steward_delete_nonce', $source);
        self::assertStringContainsString('Archive for normal retirement.', $source);
        self::assertStringContainsString('Delete permanently', $source);
        self::assertStringContainsString('window.confirm', $source);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }

    private function method(string $source, string $name): string
    {
        $start = strpos($source, 'public function ' . $name);
        self::assertNotFalse($start);
        return substr($source, (int) $start, 2200);
    }
}

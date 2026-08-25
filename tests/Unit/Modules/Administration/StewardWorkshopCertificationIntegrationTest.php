<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardWorkshopCertificationIntegrationTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testAllFiveWorkshopFamiliesShareTheCertifiedLifecycle(): void
    {
        foreach (['Monster', 'Spell', 'Background', 'Equipment', 'Calling'] as $name) {
            $source = $this->source('app/Modules/Administration/Workshop/' . $name . 'Workshop.php');
            self::assertStringContainsString("STATUS_DRAFT = 'draft'", $source, $name);
            self::assertStringContainsString("STATUS_PUBLISHED = 'published'", $source, $name);
            self::assertStringContainsString("STATUS_ARCHIVED = 'archived'", $source, $name);
        }
    }

    public function testEveryWorkshopHasStableReadPublishAndDeleteOperations(): void
    {
        foreach (['Monster', 'Spell', 'Background', 'Equipment', 'Calling'] as $name) {
            $source = $this->source('app/Modules/Administration/Workshop/' . $name . 'Workshop.php');
            self::assertStringContainsString('public function all(): array', $source, $name);
            self::assertStringContainsString('public function published(): array', $source, $name);
            self::assertStringContainsString('public function find(string $key): ?array', $source, $name);
            self::assertStringContainsString('public function delete(string $key): void', $source, $name);
        }
    }

    public function testAllWorkshopSaveActionsRemainDedicatedAdminPostRoutes(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        foreach (['monster', 'spell', 'background', 'equipment', 'calling'] as $type) {
            self::assertStringContainsString('admin_post_gmrc_save_steward_' . $type, $source, $type);
        }
        self::assertStringContainsString('admin_post_gmrc_delete_steward_record', $source);
    }

    public function testWorkshopMutationsRemainCapabilityAndNonceGuarded(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        foreach (['saveStewardMonster', 'saveStewardSpell', 'saveStewardBackground', 'saveStewardEquipment', 'saveStewardCalling', 'deleteStewardRecord'] as $method) {
            $body = $this->method($source, $method);
            self::assertStringContainsString('$this->guard();', $body, $method);
            self::assertStringContainsString('check_admin_referer(', $body, $method);
        }
    }

    public function testCanonicalAndStewardNamespacesRemainSeparated(): void
    {
        foreach (['Monster', 'Spell', 'Background', 'Equipment', 'Calling'] as $name) {
            $source = $this->source('app/Modules/Administration/Workshop/' . $name . 'Workshop.php');
            self::assertStringContainsString('steward-', $source, $name);
        }
        $guard = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("str_starts_with(" . '$key' . ", 'steward-')", $guard);
    }

    public function testSafeDeletionStillCoversEveryPersistentDependencyFamily(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        foreach (['_gmrc_class', '_gmrc_subclass', '_gmrc_spellbook', '_gmrc_background', '_gmrc_inventory', '_gmrc_encounter_monster_groups'] as $marker) {
            self::assertStringContainsString($marker, $source, $marker);
        }
    }

    public function testBackgroundWorkshopIsNeverRenderedDuringAssetEnqueueing(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        $enqueue = $this->method($source, 'enqueueAssets');
        self::assertStringContainsString("if (" . '$section' . " === 'background-workshop')", $enqueue);
        self::assertStringNotContainsString("Views/background-workshop.php", $enqueue);
    }

    public function testBackgroundWorkshopStillRendersThroughTheOfficeRouter(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        $render = $this->method($source, 'renderOffice', 9000);
        self::assertStringContainsString("if (" . '$section' . " === 'background-workshop')", $render);
        self::assertStringContainsString("Views/background-workshop.php", $render);
    }

    public function testCertificationProjectionCoversExactlyFiveWorkshopFamilies(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopCertification.php');
        foreach (['Monsters', 'Spells', 'Backgrounds', 'Equipment', 'Callings & Paths'] as $label) {
            self::assertStringContainsString("'" . $label . "'", $source);
        }
        self::assertStringContainsString("'certified' => count(" . '$rows' . ") === 5", $source);
    }

    public function testCertificationProjectionCountsAllThreeLifecycleStates(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopCertification.php');
        self::assertStringContainsString("'draft' => 0", $source);
        self::assertStringContainsString("'published' => 0", $source);
        self::assertStringContainsString("'archived' => 0", $source);
        self::assertStringContainsString("'records' => 0", $source);
    }

    public function testStewardsOfficeDisplaysWorkshopHealthWithoutInternalPhaseLanguage(): void
    {
        $source = $this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('Workshop Certification', $source);
        self::assertStringContainsString('Workshop system certified', $source);
        self::assertStringContainsString('5 authoring rooms registered', $source);
        self::assertStringNotContainsString('III.16.19E', $source);
    }

    public function testCertificationPassDoesNotRelaxArchiveFirstDeletionPolicy(): void
    {
        $report = $this->source('app/Modules/Administration/Workshop/StewardWorkshopCertification.php');
        $delete = $this->source('app/Modules/Administration/Views/_steward-delete.php');
        self::assertStringContainsString('Archive for normal retirement.', $report);
        self::assertStringContainsString('Archive for normal retirement.', $delete);
        self::assertStringContainsString('dependency-guarded', $report);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }

    private function method(string $source, string $name, int $length = 3500): string
    {
        $start = strpos($source, 'public function ' . $name);
        self::assertNotFalse($start);
        return substr($source, (int) $start, $length);
    }
}

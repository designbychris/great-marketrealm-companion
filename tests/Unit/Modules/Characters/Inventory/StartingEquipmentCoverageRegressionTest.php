<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Inventory;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\StartingEquipmentCoverage;
use PHPUnit\Framework\TestCase;

final class StartingEquipmentCoverageRegressionTest extends TestCase
{
    public function testCoverageCertifiesAllSupportedCallingsAndPackages(): void
    {
        $report = (new StartingEquipmentCoverage(new StartingEquipmentPackageRegister(), new ItemCatalogue()))->report();
        self::assertTrue($report['certified']);
        self::assertSame(13, $report['certified_callings']);
        self::assertSame(13, $report['calling_count']);
        self::assertSame(26, $report['certified_packages']);
        self::assertSame(26, $report['package_count']);
        self::assertSame(0, $report['missing_armoury_links']);
    }

    public function testEveryCallingHasAStableDefaultAndChoiceCoverage(): void
    {
        $report = (new StartingEquipmentCoverage(new StartingEquipmentPackageRegister(), new ItemCatalogue()))->report();
        foreach (StartingEquipmentCoverage::CALLINGS as $calling) {
            self::assertTrue($report['callings'][$calling]['certified'], $calling . ' should be certified.');
            self::assertGreaterThanOrEqual(2, $report['callings'][$calling]['package_count']);
            self::assertNotSame('', $report['callings'][$calling]['default_package']);
        }
    }

    public function testEveryPackageHasExactlyOneDefaultPerCalling(): void
    {
        $report = (new StartingEquipmentCoverage(new StartingEquipmentPackageRegister(), new ItemCatalogue()))->report();
        foreach (StartingEquipmentCoverage::CALLINGS as $calling) {
            $defaults = 0;
            foreach ((new StartingEquipmentPackageRegister())->forClass($calling) as $package) {
                $defaults += ! empty($report['packages'][$package->id()]['is_default']) ? 1 : 0;
            }
            self::assertSame(1, $defaults, $calling . ' should have one deterministic default.');
        }
    }

    public function testBackgroundEquipmentPolicyDoesNotInventUnsupportedStartingGear(): void
    {
        $report = (new StartingEquipmentCoverage(new StartingEquipmentPackageRegister(), new ItemCatalogue()))->report();
        self::assertStringContainsString('No background equipment is granted', $report['background_policy']);
    }

    public function testStewardViewSurfacesCoverageAndCertificationStatus(): void
    {
        $root = dirname(__DIR__, 5);
        $provider = file_get_contents($root . '/app/Providers/AdministrationServiceProvider.php');
        $view = file_get_contents($root . '/app/Modules/Administration/Views/starting-equipment.php');
        self::assertStringContainsString('StartingEquipmentCoverage::class', (string) $provider);
        self::assertStringContainsString('Certification &amp; Coverage', (string) $view);
        self::assertStringContainsString('missing Armoury links', (string) $view);
        self::assertStringContainsString('Coverage certified', (string) $view);
    }
}

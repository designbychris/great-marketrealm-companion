<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardFolkWorkshopRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);
    }

    public function testWorkshopUsesSeparateStewardOptionAndCertifiedLifecycle(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/FolkWorkshop.php');
        self::assertStringContainsString('gmrc_steward_folk', $source);
        self::assertStringContainsString("STATUS_DRAFT = 'draft'", $source);
        self::assertStringContainsString("STATUS_PUBLISHED = 'published'", $source);
        self::assertStringContainsString("STATUS_ARCHIVED = 'archived'", $source);
    }

    public function testPublishedFolkRequiresBoundedIdentityMechanics(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/FolkWorkshop.php');
        self::assertStringContainsString('range(0, 120, 5)', $source);
        self::assertStringContainsString("['Small', 'Medium', 'Small or Medium']", $source);
        self::assertStringContainsString('$description === \'\'', $source);
    }

    public function testStewardFolkIdentityCannotReplaceCanonicalRace(): void
    {
        self::assertStringContainsString('Race::canonicalSupports($key)', $this->source('app/Modules/Administration/Workshop/FolkWorkshop.php'));
    }

    public function testRaceValueObjectResolvesPublishedAndArchivedStewardFolk(): void
    {
        $source = $this->source('app/Modules/Characters/Models/ValueObjects/Race.php');
        self::assertStringContainsString('gmrc_steward_folk', $source);
        self::assertStringContainsString('$status !== \'published\'', $source);
        self::assertStringContainsString('$status === \'archived\'', $source);
    }

    public function testNewCharacterValidationUsesOnlyCreationEligibleFolk(): void
    {
        self::assertStringContainsString('Race::creationIdentifiers()', $this->source('app/Modules/Characters/Requests/StoreCharacterRequest.php'));
    }

    public function testCharacterCatalogueMergesPublishedFolkAndHeritages(): void
    {
        $source = $this->source('app/Modules/Characters/Catalogue/Repositories/CharacterCatalogueRepository.php');
        self::assertStringContainsString('gmrc_steward_folk', $source);
        self::assertStringContainsString("\$record['heritages']", $source);
        self::assertStringContainsString("(\$record['status'] ?? '') === 'published'", $source);
    }

    public function testFolkHeritagesUseStableStewardIdentityAndParent(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/FolkWorkshop.php');
        self::assertStringContainsString("'steward-heritage-'", $source);
        self::assertStringContainsString("'parent' => \$folkKey", $source);
    }

    public function testRaceRegistryProjectsPublishedStewardReferenceDetails(): void
    {
        $source = $this->source('app/Services/Characters/RaceRegistry.php');
        self::assertStringContainsString('gmrc_steward_folk', $source);
        self::assertStringContainsString("->speed((int) (\$record['speed'] ?? 30))", $source);
        self::assertStringContainsString("->creatureType((string) (\$record['creature_type'] ?? 'Humanoid'))", $source);
    }

    public function testAdministrationProviderRegistersDedicatedFolkSaveRoute(): void
    {
        $source = $this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('admin_post_gmrc_save_steward_folk', $source);
        self::assertStringContainsString('saveStewardFolk', $source);
        self::assertStringContainsString('FolkWorkshop::class', $source);
    }

    public function testSafeDeletionProtectsCharacterRaceAndHeritageReferences(): void
    {
        $source = $this->source('app/Modules/Administration/Workshop/StewardWorkshopDeletionGuard.php');
        self::assertStringContainsString("'_gmrc_race'", $source);
        self::assertStringContainsString("'_gmrc_heritage'", $source);
        self::assertStringContainsString("'folk'", $source);
    }

    public function testOfficeAndCertificationExposeSixthWorkshopWithoutPhaseLanguage(): void
    {
        $office = $this->source('app/Modules/Administration/Views/stewards-office.php');
        $workshop = $this->source('app/Modules/Administration/Views/folk-workshop.php');
        $certification = $this->source('app/Modules/Administration/Workshop/StewardWorkshopCertification.php');
        self::assertStringContainsString('Folk &amp; Heritage Workshop', $office);
        self::assertStringContainsString('6 authoring rooms registered', $office);
        self::assertStringContainsString("'Folk & Heritages'", $certification);
        self::assertStringContainsString('count($rows) === 6', $certification);
        self::assertStringNotContainsString('III.16.20', $workshop);
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }
}

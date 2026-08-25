<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardCallingWorkshopRegressionTest extends TestCase
{
    private string $root;
    protected function setUp(): void { $this->root = dirname(__DIR__, 4); }
    private function source(string $path): string { $value=file_get_contents($this->root.'/'.$path); self::assertIsString($value); return $value; }

    public function testWorkshopUsesSeparateStewardOptionAndLifecycle(): void { $s=$this->source('app/Modules/Administration/Workshop/CallingWorkshop.php'); self::assertStringContainsString("gmrc_steward_callings",$s); self::assertStringContainsString("STATUS_PUBLISHED",$s); self::assertStringContainsString("STATUS_ARCHIVED",$s); }
    public function testPublishedCallingRequiresCertifiedHitDie(): void { self::assertStringContainsString('in_array($hitDie, self::HIT_DICE, true)', $this->source('app/Modules/Administration/Workshop/CallingWorkshop.php')); }
    public function testPublishedCallingRequiresExactlyTwoSavingThrows(): void { self::assertStringContainsString('count($saves) !== 2', $this->source('app/Modules/Administration/Workshop/CallingWorkshop.php')); }
    public function testStewardIdentityCannotReplaceCanonicalClass(): void { self::assertStringContainsString('CharacterClass::canonicalSupports($key)', $this->source('app/Modules/Administration/Workshop/CallingWorkshop.php')); }
    public function testCharacterClassReadsOnlyPublishedStewardCallings(): void { $s=$this->source('app/Modules/Characters/Models/ValueObjects/CharacterClass.php'); self::assertStringContainsString("gmrc_steward_callings",$s); self::assertStringContainsString("'published'",$s); }
    public function testCharacterCreationValidationUsesEffectiveClassIdentifiers(): void { self::assertStringContainsString('CharacterClass::identifiers()', $this->source('app/Modules/Characters/Requests/StoreCharacterRequest.php')); }
    public function testCharacterCatalogueMergesPublishedCallingsAndPaths(): void { $s=$this->source('app/Modules/Characters/Catalogue/Repositories/CharacterCatalogueRepository.php'); self::assertStringContainsString("gmrc_steward_callings",$s); self::assertStringContainsString('$record[\'paths\']',$s); }
    public function testStewardCallingPathHasProgressionDefinition(): void { $s=$this->source('app/Modules/Characters/Progression/Paths/Definitions/StewardCallingPathProgression.php'); self::assertStringContainsString("'selection_level'",$s); self::assertStringContainsString("'choice_key'",$s); }
    public function testWorkshopIsRegisteredBehindAdministratorPostAction(): void { $s=$this->source('app/Providers/AdministrationServiceProvider.php'); self::assertStringContainsString('gmrc_save_steward_calling',$s); self::assertStringContainsString('CallingWorkshop::class',$s); }
    public function testOfficeLinksToCallingWorkshop(): void { $s=$this->source('app/Modules/Administration/Views/stewards-office.php'); self::assertStringContainsString('Class &amp; Calling Path Workshop',$s); self::assertStringContainsString("'section' => 'calling-workshop'",$s); }
    public function testProductionWorkshopDoesNotExposeInternalPhaseNumber(): void { self::assertStringNotContainsString('III.16.19D', $this->source('app/Modules/Administration/Views/calling-workshop.php')); }
}

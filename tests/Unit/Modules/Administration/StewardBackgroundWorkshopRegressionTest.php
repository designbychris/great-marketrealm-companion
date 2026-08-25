<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardBackgroundWorkshopRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void { $this->root = dirname(__DIR__, 4); }

    public function testAdministrationRegistersBackgroundWorkshop(): void
    {
        $source=$this->source('app/Providers/AdministrationServiceProvider.php');
        self::assertStringContainsString('BackgroundWorkshop::class', $source);
        self::assertStringContainsString("admin_post_gmrc_save_steward_background", $source);
        self::assertStringContainsString("section === 'background-workshop'", $source);
    }

    public function testStewardsOfficeLinksToBackgroundWorkshop(): void
    {
        $source=$this->source('app/Modules/Administration/Views/stewards-office.php');
        self::assertStringContainsString('Background Workshop', $source);
        self::assertStringContainsString('Open Background Workshop', $source);
    }

    public function testWorkshopHasProtectedPublicationLifecycle(): void
    {
        $source=$this->source('app/Modules/Administration/Workshop/BackgroundWorkshop.php');
        self::assertStringContainsString("STATUS_DRAFT = 'draft'", $source);
        self::assertStringContainsString("STATUS_PUBLISHED = 'published'", $source);
        self::assertStringContainsString("STATUS_ARCHIVED = 'archived'", $source);
        self::assertStringContainsString("'gmrc_steward_backgrounds'", $source);
    }

    public function testPublishedBackgroundRequiresCertifiedMechanics(): void
    {
        $source=$this->source('app/Modules/Administration/Workshop/BackgroundWorkshop.php');
        self::assertStringContainsString('exactly two recognised skills and one recognised tool proficiency', $source);
        self::assertStringContainsString('SkillProficiencies::proficient', $source);
        self::assertStringContainsString('ToolProficiency::supports', $source);
    }

    public function testStableStewardIdentityCannotOverwriteCanon(): void
    {
        $source=$this->source('app/Modules/Administration/Workshop/BackgroundWorkshop.php');
        self::assertStringContainsString("'steward-background-'", $source);
        self::assertStringContainsString('$this->canonical->find($key)', $source);
    }

    public function testSharedBackgroundMechanicsRegisterIncludesOnlyPublishedStewardRecords(): void
    {
        $source=$this->source('app/Modules/Library/Backgrounds/Repositories/BackgroundMechanicsRegister.php');
        self::assertStringContainsString("STEWARD_OPTION = 'gmrc_steward_backgrounds'", $source);
        self::assertStringContainsString("(\$entry['status'] ?? '') !== 'published'", $source);
        self::assertStringContainsString('stewardRecords()', $source);
    }

    public function testCustomBackgroundValueRequiresCertifiedSnapshot(): void
    {
        $source=$this->source('app/Modules/Characters/Models/ValueObjects/Background.php');
        self::assertStringContainsString('$this->skillSnapshot === null || $this->toolSnapshot === null', $source);
        self::assertStringContainsString('?string $label = null', $source);
        self::assertStringContainsString('$labelSnapshot', $source);
    }

    public function testCharacterRegistrationAcceptsOnlyResolvedPublishedStewardBackgrounds(): void
    {
        $source=$this->source('app/Modules/Characters/Requests/Concerns/ResolvesRegistrationInput.php');
        self::assertStringContainsString("BackgroundMechanicsRegister())->find($value) === null", $source);
        self::assertStringContainsString('Choose a recognised background from the Guild Register.', $source);
    }

    public function testCharacterCreatorAndEditorExposePublishedStewardBackgrounds(): void
    {
        foreach(['app/Modules/Characters/Views/create.php','app/Modules/Characters/Views/edit.php'] as $path){
            $source=$this->source($path);
            self::assertStringContainsString("str_starts_with(\$record->key(), 'steward-background-')", $source);
            self::assertStringContainsString('Background::fromStringWithMechanics', $source);
        }
    }

    public function testCharacterSnapshotPersistsStewardBackgroundLabel(): void
    {
        $source=$this->source('app/Modules/Characters/Repositories/CharacterRepository.php');
        self::assertStringContainsString('_gmrc_background_label', $source);
        self::assertStringContainsString('$character->background()->label()', $source);
        self::assertStringContainsString('is_string($label)', $source);
    }

    public function testReferenceOnlyFieldsDoNotPretendToGrantUncertifiedMechanics(): void
    {
        $source=$this->source('app/Modules/Administration/Views/background-workshop.php');
        self::assertStringContainsString('Starting equipment / possessions', $source);
        self::assertStringContainsString('Languages / language notes', $source);
        self::assertStringContainsString('reference-only in this phase', $source);
    }

    private function source(string $relative): string
    {
        $source=file_get_contents($this->root.'/'.$relative);
        self::assertIsString($source);
        return $source;
    }
}

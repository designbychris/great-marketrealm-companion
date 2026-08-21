<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\HandbookBackgroundRegister;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\BackgroundReferenceCatalogue;
use PHPUnit\Framework\TestCase;

final class ExpandedBackgroundsRegressionTest extends TestCase
{
    public function testHandbookRegistersExactlyFiveOptionalMarketrealmBackgrounds(): void
    {
        $register = new HandbookBackgroundRegister();
        self::assertSame(
            ['crateborn-noble', 'backshelf-forager', 'discount-bin-survivor', 'cleaners-acolyte', 'cart-ranger'],
            array_map(static fn ($background): string => $background->key(), $register->all())
        );
    }

    public function testCanonicalBackgroundNamesRemainSourceFaithful(): void
    {
        self::assertSame(
            ['Crateborn Noble', 'Backshelf Forager', 'Discount Bin Survivor', 'Cleaner’s Acolyte', 'Cart Ranger'],
            array_map(static fn ($background): string => $background->name(), (new HandbookBackgroundRegister())->all())
        );
    }

    public function testCanonicalFeaturesRemainSourceFaithful(): void
    {
        $register = new HandbookBackgroundRegister();
        $expected = [
            'crateborn-noble' => 'Produce of Privilege',
            'backshelf-forager' => "Scavenger's Eye",
            'discount-bin-survivor' => 'Stickered But Strong',
            'cleaners-acolyte' => 'Sanitized Mind',
            'cart-ranger' => 'Aisle Scout',
        ];
        foreach ($expected as $key => $feature) {
            self::assertSame($feature, $register->find($key)?->featureName());
            self::assertNotSame('', $register->find($key)?->featureDetail());
        }
    }

    public function testCanonicalSkillsMatchTheHandbook(): void
    {
        $register = new HandbookBackgroundRegister();
        $expected = [
            'crateborn-noble' => ['persuasion', 'history'],
            'backshelf-forager' => ['survival', 'investigation'],
            'discount-bin-survivor' => ['intimidation', 'stealth'],
            'cleaners-acolyte' => ['arcana', 'religion'],
            'cart-ranger' => ['athletics', 'nature'],
        ];
        foreach ($expected as $key => $skills) {
            self::assertSame($skills, $register->find($key)?->skills());
        }
    }

    public function testCanonicalToolsMatchTheHandbook(): void
    {
        $register = new HandbookBackgroundRegister();
        $expected = [
            'crateborn-noble' => ['cartographers-tools'],
            'backshelf-forager' => ['herbalism-kit'],
            'discount-bin-survivor' => ['tinkers-tools'],
            'cleaners-acolyte' => ['alchemists-supplies'],
            'cart-ranger' => ['navigators-tools'],
        ];
        foreach ($expected as $key => $tools) {
            self::assertSame($tools, $register->find($key)?->tools());
        }
    }

    public function testHandbookToolLabelsArePreservedSeparatelyFromInternalIds(): void
    {
        $register = new HandbookBackgroundRegister();
        self::assertSame('Cartographer’s tools', $register->find('crateborn-noble')?->toolLabel());
        self::assertSame('Alchemist’s Supplies', $register->find('cleaners-acolyte')?->toolLabel());
        self::assertSame('Navigator’s Tools', $register->find('cart-ranger')?->toolLabel());
    }

    public function testMissingLanguagesAndEquipmentRemainExplicitSourceGaps(): void
    {
        foreach ((new HandbookBackgroundRegister())->all() as $background) {
            self::assertContains('languages-not-stated-in-handbook', $background->sourceIssues());
            self::assertContains('equipment-not-stated-in-handbook', $background->sourceIssues());
        }
    }

    public function testBackgroundReferenceCatalogueIsNowRegisteredWithFiveEntries(): void
    {
        $catalogue = new BackgroundReferenceCatalogue();
        self::assertSame('registered', $catalogue->status());
        self::assertSame('III.13.3', $catalogue->phase());
        self::assertCount(5, $catalogue->entries());
        self::assertSame(5, $catalogue->summary()['entry_count']);
    }

    public function testCharacterGeneratorNowSupportsAllFiveCanonicalBackgrounds(): void
    {
        foreach (['crateborn-noble', 'backshelf-forager', 'discount-bin-survivor', 'cleaners-acolyte', 'cart-ranger'] as $background) {
            self::assertTrue(Background::supports($background));
            self::assertSame(0, Background::fromString($background)->languageChoices());
        }
    }

    public function testCharacterBackgroundsCarryCanonicalSkillsAndTools(): void
    {
        $crateborn = Background::fromString('crateborn-noble');
        $cleaner = Background::fromString('cleaners-acolyte');
        $cart = Background::fromString('cart-ranger');
        self::assertSame(['persuasion', 'history'], $crateborn->skillProficiencies()->proficiencies());
        self::assertSame(['cartographers-tools'], $crateborn->toolProficiencyIdentifiers());
        self::assertSame(['alchemists-supplies'], $cleaner->toolProficiencyIdentifiers());
        self::assertSame(['navigators-tools'], $cart->toolProficiencyIdentifiers());
    }

    public function testThreeNewToolProficienciesAreFirstClassSupportedValues(): void
    {
        $expected = [
            'cartographers-tools' => "Cartographer's Tools",
            'alchemists-supplies' => "Alchemist's Supplies",
            'navigators-tools' => "Navigator's Tools",
        ];
        foreach ($expected as $tool => $label) {
            self::assertTrue(ToolProficiency::supports($tool));
            self::assertSame($label, ToolProficiency::fromString($tool)->label());
        }
    }

    public function testExistingEightBackgroundIdentifiersRemainSupported(): void
    {
        foreach (['market-runner', 'shelf-scholar', 'waste-warden', 'guild-artisan', 'folk-hero', 'sage', 'soldier', 'criminal'] as $background) {
            self::assertTrue(Background::supports($background));
        }
        self::assertCount(13, Background::all());
    }

    public function testCreationAndEditSurfacesReceiveCanonicalFeatureReferences(): void
    {
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');
        $create = $this->source('app/Modules/Characters/Views/create.php');
        $edit = $this->source('app/Modules/Characters/Views/edit.php');
        self::assertStringContainsString('HandbookBackgroundRegister', $controller);
        self::assertStringContainsString("'backgroundReferences'", $controller);
        self::assertStringContainsString('feature_name', $create);
        self::assertStringContainsString('feature_detail', $create);
        self::assertStringContainsString('Handbook feature', $edit);
    }

    public function testGuildLibraryNowOpensARealBackgroundRegister(): void
    {
        $routes = $this->source('app/Modules/Library/Routes.php');
        $library = $this->source('app/Modules/Library/Views/index.php');
        $backgrounds = $this->source('app/Modules/Library/Views/backgrounds/index.php');
        self::assertStringContainsString("'/library/backgrounds'", $routes);
        self::assertStringContainsString('Open Background Register', $library);
        self::assertStringContainsString('The Background Register', $backgrounds);
        self::assertStringContainsString('data-background-reference', $backgrounds);
    }

    public function testExpandedBackgroundPhaseDoesNotTouchSpellOrCallingMechanics(): void
    {
        $backgroundRegister = $this->source(
            'app/Modules/Library/Backgrounds/Repositories/HandbookBackgroundRegister.php'
        );
        foreach (['HandbookSpellRegister', 'Spellbook', 'ClassProgressionCatalogue', 'PathGiftCatalogue', 'SharedSpellSlotReserveService'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $backgroundRegister);
        }
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}

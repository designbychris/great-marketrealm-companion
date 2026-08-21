<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Library\Armoury\Repositories\MarketrealmArmouryRegister;
use GreatMarketrealmCompanion\Modules\Library\Catalogues\ArmouryReferenceCatalogue;
use PHPUnit\Framework\TestCase;

final class MarketrealmArmouryRegressionTest extends TestCase
{
    public function testArmouryRegisterContainsBroadMundaneSelection(): void
    {
        $register = new MarketrealmArmouryRegister();

        self::assertGreaterThanOrEqual(
            45,
            count($register->all())
        );
        self::assertGreaterThanOrEqual(
            30,
            count($register->byCategory('weapon'))
        );
        self::assertGreaterThanOrEqual(
            10,
            count($register->byCategory('armour'))
        );
    }

    public function testRegisterDistinguishesHandbookMentionedFromStandardCompatible(): void
    {
        $register = new MarketrealmArmouryRegister();

        self::assertGreaterThan(
            0,
            count($register->byProvenance('handbook-mentioned'))
        );
        self::assertGreaterThan(
            0,
            count($register->byProvenance('standard-compatible'))
        );

        self::assertSame(
            'handbook-mentioned',
            $register->find('rapier')?->provenance()
        );
        self::assertSame(
            'standard-compatible',
            $register->find('greatsword')?->provenance()
        );
    }

    public function testHandbookMentionedStartingEquipmentNamesRemainVisible(): void
    {
        $register = new MarketrealmArmouryRegister();

        foreach ([
            'dagger',
            'shortsword',
            'rapier',
            'longsword',
            'shortbow',
            'scimitar',
            'leather-armour',
            'shield',
            'explorers-pack',
        ] as $id) {
            self::assertNotNull(
                $register->find($id),
                'Missing handbook-mentioned item: ' . $id
            );
        }
    }

    public function testArmouryContainsNoRelicMagicOrArtifactCategories(): void
    {
        $register = new MarketrealmArmouryRegister();

        foreach ($register->all() as $record) {
            self::assertNotContains(
                $record->category(),
                [
                    'magic',
                    'magical',
                    'relic',
                    'artifact',
                    'artefact',
                ]
            );
        }
    }

    public function testCharacterCatalogueConsumesSharedArmouryWithoutDroppingLegacyItems(): void
    {
        $catalogue = new ItemCatalogue();

        self::assertNotNull(
            $catalogue->find('market-cleaver')
        );
        self::assertNotNull(
            $catalogue->find('longbow')
        );
        self::assertNotNull(
            $catalogue->find('plate-armour')
        );
        self::assertNotNull(
            $catalogue->find('explorers-pack')
        );
    }

    public function testRangedWeaponUsesDexterityAndItsRecordedRange(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()
            ->add('longbow')
            ->equip('longbow', $catalogue);

        $attacks = (
            new AttackPresenter($catalogue)
        )->present(
            $this->character(18, 12),
            $inventory
        );

        self::assertCount(1, $attacks);
        self::assertSame(
            'Dexterity',
            $attacks[0]['ability']
        );
        self::assertSame(
            'Ranged · 150/600 ft',
            $attacks[0]['range']
        );
    }

    public function testFinesseMeleeBehaviourRemainsIntact(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()
            ->add('rapier')
            ->equip('rapier', $catalogue);

        $attacks = (
            new AttackPresenter($catalogue)
        )->present(
            $this->character(12, 18),
            $inventory
        );

        self::assertSame(
            'Dexterity',
            $attacks[0]['ability']
        );
        self::assertSame(
            'Melee · 5 ft',
            $attacks[0]['range']
        );
    }

    public function testHeavyArmourUsesZeroDexterityCapInCurrentAcPipeline(): void
    {
        $catalogue = new ItemCatalogue();

        self::assertSame(
            18,
            $catalogue
                ->find('plate-armour')
                ?->armourBase()
        );
        self::assertSame(
            0,
            $catalogue
                ->find('plate-armour')
                ?->dexterityCap()
        );
    }

    public function testArmouryCatalogueIsRegisteredInPhaseThirteenFour(): void
    {
        $catalogue =
            new ArmouryReferenceCatalogue();

        self::assertSame(
            'armoury',
            $catalogue->key()
        );
        self::assertSame(
            'III.13.4',
            $catalogue->phase()
        );
        self::assertSame(
            'registered',
            $catalogue->status()
        );
        self::assertGreaterThan(
            0,
            count($catalogue->entries())
        );
    }

    public function testGuildLibraryExposesDedicatedArmouryRouteAndController(): void
    {
        $routes = $this->source(
            'app/Modules/Library/Routes.php'
        );
        $controller = $this->source(
            'app/Modules/Library/Controllers/'
            . 'LibraryController.php'
        );

        self::assertStringContainsString(
            "'/library/armoury'",
            $routes
        );
        self::assertStringContainsString(
            "[LibraryController::class, 'armoury']",
            $routes
        );
        self::assertStringContainsString(
            "'library.armoury.index'",
            $controller
        );
    }

    public function testGuildLibraryLandingPageOpensMarketrealmArmoury(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/index.php'
        );

        self::assertStringContainsString(
            "'library/armoury'",
            $view
        );
        self::assertStringContainsString(
            'Open Marketrealm Armoury',
            $view
        );
    }

    public function testArmouryViewExplainsProvenanceInsteadOfClaimingEverythingIsCanonical(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/'
            . 'armoury/index.php'
        );

        self::assertStringContainsString(
            'Handbook-mentioned',
            $view
        );
        self::assertStringContainsString(
            'Standard-compatible',
            $view
        );
        self::assertStringContainsString(
            'It is not',
            $view
        );
        self::assertStringContainsString(
            'Marketrealm-handbook canon',
            $view
        );
    }

    public function testArmouryViewKeepsRelicsForNextPhase(): void
    {
        $view = $this->source(
            'app/Modules/Library/Views/'
            . 'armoury/index.php'
        );

        self::assertStringContainsString(
            'Phase III.13.5',
            $view
        );
        self::assertStringContainsString(
            'Magical relics and artefacts are deliberately absent',
            $view
        );
    }

    public function testArmouryPresentationIsResponsiveAndHighContrastSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/library/'
            . 'guild-library.css'
        );

        self::assertStringContainsString(
            '.gmrc-armoury__grid',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 1050px)',
            $css
        );
        self::assertStringContainsString(
            '@media (max-width: 680px)',
            $css
        );
        self::assertStringContainsString(
            '.gmrc-armoury a:focus-visible',
            $css
        );
        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testExistingInventoryStorageShapeRemainsIndependentOfLibraryRecords(): void
    {
        $inventory = $this->source(
            'app/Modules/Characters/Inventory/'
            . 'Models/CharacterInventory.php'
        );

        self::assertStringNotContainsString(
            'MarketrealmArmouryRegister',
            $inventory
        );
        self::assertStringNotContainsString(
            'ArmouryRecord',
            $inventory
        );
    }

    private function character(
        int $strength,
        int $dexterity
    ): Character {
        return Character::create(
            CharacterId::generate(),
            CharacterName::fromString(
                'Quartermaster Test'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('fighter'),
            HitPoints::full(10),
            AbilityScores::fromScores(
                AbilityScore::fromInt($strength),
                AbilityScore::fromInt($dexterity),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            )
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
            $this->root()
            . '/'
            . $relative
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use GreatMarketrealmCompanion\Modules\Library\Catalogues\SpellReferenceCatalogue;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\HandbookSpellRegister;
use PHPUnit\Framework\TestCase;

final class SpellRegisterRegressionTest extends TestCase
{
    public function testHandbookImportsSeventyTwoCanonicalSpellIdentities(): void
    {
        $register = new HandbookSpellRegister();

        self::assertCount(72, $register->all());
        self::assertSame(74, $register->sourceVariantCount());
    }

    public function testRegisterDistinguishesRenamedAndOriginalMarketrealmMagic(): void
    {
        $register = new HandbookSpellRegister();

        self::assertCount(30, $register->byKind('renamed'));
        self::assertCount(42, $register->byKind('marketrealm-original'));

        self::assertSame(
            'Cure Wounds',
            $register->find('cure-meats')?->originalSpell()
        );
        self::assertSame(
            'Magic Missile',
            $register->find('mystery-mustard-missile')?->originalSpell()
        );
        self::assertNull(
            $register->find('spork-barrage')?->originalSpell()
        );
    }

    public function testMissingHandbookMetadataIsNotSilentlyInvented(): void
    {
        $cure = (new HandbookSpellRegister())->find('cure-meats');

        self::assertNotNull($cure);
        self::assertNull($cure->level());
        self::assertNull($cure->school());
        self::assertSame([], $cure->accessLabels());
        self::assertContains(
            'level-not-stated-in-handbook',
            $cure->sourceIssues()
        );
    }

    public function testBreadWallKeepsBothConflictingHandbookVariants(): void
    {
        $bread = (new HandbookSpellRegister())->find('bread-wall');

        self::assertNotNull($bread);
        self::assertCount(2, $bread->variants());
        self::assertSame([2, 2], array_column($bread->variants(), 'level'));
        self::assertContains(
            'conflicting-source-variants',
            $bread->sourceIssues()
        );
        self::assertStringContainsString(
            'full cover',
            $bread->variants()[0]['source_text']
        );
        self::assertStringContainsString(
            'three-quarters cover',
            $bread->variants()[1]['source_text']
        );
    }

    public function testVacuumSealKeepsThirdAndFourthLevelVariantsSeparate(): void
    {
        $seal = (new HandbookSpellRegister())->find('vacuum-seal');

        self::assertNotNull($seal);
        self::assertCount(2, $seal->variants());
        self::assertSame([3, 4], array_column($seal->variants(), 'level'));
        self::assertContains(
            'conflicting-source-variants',
            $seal->sourceIssues()
        );
    }

    public function testSourceTypoRemainsVisibleInsteadOfBeingCorrectedSilently(): void
    {
        $oven = (new HandbookSpellRegister())->find('oven-of-annihilation');

        self::assertNotNull($oven);
        self::assertContains('Arificer', $oven->accessLabels());
        self::assertContains(
            'source-label-preserved:Arificer',
            $oven->sourceIssues()
        );
    }

    public function testRegisterLoadsHandbookDataWithoutGlobalPluginPathConstant(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Library/Spells/Repositories/'
            . 'HandbookSpellRegister.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'dirname(__DIR__)',
            $source
        );
        self::assertStringNotContainsString(
            'GMRC_PATH',
            $source
        );
    }

    public function testCatalogueMovesFromFoundationToRegisteredWithoutCharacterIntegration(): void
    {
        $catalogue = new SpellReferenceCatalogue();
        $summary = $catalogue->summary();

        self::assertSame('III.13.1A', $summary['phase']);
        self::assertSame('registered', $summary['status']);
        self::assertSame(72, $summary['entry_count']);
        self::assertSame(30, $summary['renamed_count']);
        self::assertSame(42, $summary['marketrealm_original_count']);
        self::assertSame(74, $summary['source_variant_count']);
    }

    public function testSpellRegisterDoesNotReachIntoCharacterPersistence(): void
    {
        foreach ([
            'app/Modules/Library/Spells/Repositories/HandbookSpellRegister.php',
            'app/Modules/Library/Catalogues/SpellReferenceCatalogue.php',
        ] as $relative) {
            $source = file_get_contents($this->root() . '/' . $relative);
            self::assertIsString($source);
            self::assertStringNotContainsString('CharacterRepository', $source);
            self::assertStringNotContainsString('CharacterController', $source);
            self::assertStringNotContainsString('ArcanePantryPresenter', $source);
        }
    }

    private function root(): string
    {
        return dirname(__DIR__, 4);
    }
}

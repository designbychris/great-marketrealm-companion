<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;

use GreatMarketrealmCompanion\Modules\Characters\Catalogue\HeritageGuidance;
use PHPUnit\Framework\TestCase;

final class CanonicalHeritageNormalisationTest extends TestCase
{
    public function testBananariCarriesCanonicalHandbookMechanics(): void
    {
        $root = dirname(__DIR__, 5);
        $catalogue = json_decode(
            (string) file_get_contents($root . '/resources/catalogue/players-handbook.v1.json'),
            true
        );
        self::assertIsArray($catalogue);

        $bananari = null;
        foreach ($catalogue['heritages'] as $heritage) {
            if (($heritage['key'] ?? '') === 'bananari') {
                $bananari = $heritage;
                break;
            }
        }

        self::assertIsArray($bananari);
        $guidance = HeritageGuidance::normalize($bananari);
        self::assertSame(2, $guidance['ability_modifiers']['dexterity']);
        self::assertSame(1, $guidance['ability_modifiers']['intelligence']);
        self::assertSame('Medium', $guidance['size']);
        self::assertSame('35 ft', $guidance['speed']);
        self::assertSame(
            ['Slippery Skin', 'Quick Peel', 'Flexible Logic'],
            array_column($guidance['features'], 'name')
        );
        self::assertSame(
            ['Acrobatics', 'Sleight of Hand'],
            $guidance['proficiency_choices'][0]['from']
        );
    }

    public function testLegacyTraitArraysRemainListsInsteadOfBecomingArrayText(): void
    {
        self::assertSame(
            ['Sweet Scent', 'Fruitful Vitality'],
            HeritageGuidance::traits(['traits' => ['Sweet Scent', 'Fruitful Vitality']])
        );
    }

    public function testRepositoryNormalisesCanonicalAndStewardHeritagesThroughOneContract(): void
    {
        $root = dirname(__DIR__, 5);
        $source = (string) file_get_contents(
            $root . '/app/Modules/Characters/Catalogue/Repositories/CharacterCatalogueRepository.php'
        );

        self::assertStringContainsString('withHeritageGuidance($heritage, $parent)', $source);
        self::assertStringContainsString('withHeritageGuidance($heritage, $record)', $source);
        self::assertStringContainsString('HeritageGuidance::normalize($heritage)', $source);
        self::assertStringContainsString("'parent_traits'", $source);
    }
}

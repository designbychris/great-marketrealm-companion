<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Integration;

use PHPUnit\Framework\TestCase;

final class LightWroughtByMagicRegressionTest extends TestCase
{
    private function root(string $path): string
    {
        return dirname(__DIR__, 5) . '/' . $path;
    }

    public function test_shelfshine_is_canonical_marketrealm_light_cantrip(): void
    {
        $spells = require $this->root('app/Modules/Library/Spells/Data/handbook-spells.php');
        $shelfshine = null;
        foreach ($spells as $spell) {
            if (($spell['key'] ?? '') === 'shelfshine') { $shelfshine = $spell; break; }
        }

        self::assertIsArray($shelfshine);
        self::assertSame('Shelfshine', $shelfshine['name']);
        self::assertSame('Light', $shelfshine['original_spell']);
        self::assertSame(0, $shelfshine['level']);
        self::assertStringContainsString('20-foot radius', $shelfshine['variants'][0]['source_text']);
        self::assertStringContainsString('additional 20 feet', $shelfshine['variants'][0]['source_text']);
    }

    public function test_tabletop_projection_has_structured_companion_certified_illumination(): void
    {
        $source = file_get_contents($this->root('app/Modules/Characters/Arcana/Services/SpellIlluminationCatalogue.php'));
        $presenter = file_get_contents($this->root('app/Modules/Characters/Arcana/Services/ArcanePantryPresenter.php'));

        self::assertStringContainsString("'bright_feet' => 20", $source);
        self::assertStringContainsString("'dim_feet' => 20", $source);
        self::assertStringContainsString("'duration_seconds' => 3600", $source);
        self::assertStringContainsString("'illumination' =>", $presenter);
    }
}

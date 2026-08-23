<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class LivingBestiaryRegressionTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = dirname(__DIR__, 4);
    }

    public function testCanonicalMonsterExposesLivingBestiaryRulesProfile(): void
    {
        $model = $this->source('app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php');
        foreach (['savingThrows', 'skills', 'damageResistances', 'damageImmunities', 'damageVulnerabilities', 'conditionImmunities', 'senses', 'languages', 'spellcasting', 'reactions', 'legendaryActions', 'mythicActions', 'lairActions'] as $method) {
            self::assertStringContainsString('function ' . $method . '()', $model);
        }
    }

    public function testGuideBaselineContainsRichPickledBasiliskAndCroissantDragonRecords(): void
    {
        $data = $this->source('app/Modules/DungeonMaster/Bestiary/Data/dungeon-master-guide-monsters.php');
        foreach (['6d8+18', 'Brine Gaze (Recharge 5–6)', 'Damage Resistances', 'Bakerycant', 'Butterflame Breath', 'Whip of Butter', 'Pastry Flare'] as $needle) {
            if ($needle === 'Damage Resistances') {
                $needle = "'damage_resistances'";
            }
            self::assertStringContainsString($needle, $data);
        }
    }

    public function testFieldFolioRendersStructuredAndAdvancedRulesSections(): void
    {
        $view = $this->source('app/Modules/DungeonMaster/Views/monsters/canonical.php');
        foreach (['Canonical rules profile', 'Damage Resistances', 'Condition Immunities', 'Spellcasting', 'Reactions', 'Legendary Actions', 'Mythic Features', 'Lair Actions'] as $label) {
            self::assertStringContainsString($label, $view);
        }
    }

    public function testStewardOverridesCanTuneLivingBestiaryFields(): void
    {
        $service = $this->source('app/Modules/Administration/CanonicalRecords/CanonicalBestiarySteward.php');
        $view = $this->source('app/Modules/Administration/Views/canonical-records.php');
        foreach (['damage_resistances', 'condition_immunities', 'spellcasting', 'legendary_actions', 'mythic_actions', 'lair_actions'] as $field) {
            self::assertStringContainsString("'" . $field . "'", $service);
        }

        foreach (['damage_resistances', 'condition_immunities'] as $field) {
            self::assertStringContainsString("'" . $field . "' => [", $view);
        }

        foreach (['spellcasting', 'legendary_actions', 'mythic_actions', 'lair_actions'] as $field) {
            self::assertStringContainsString('name="' . $field . '"', $view);
        }

        self::assertStringContainsString('name="<?php echo esc_attr($name); ?>"', $view);
        self::assertStringContainsString('Restore Dungeon Master Guide baseline', $view);
    }

    public function testLivingBestiaryStylesStructuredRulesWithoutLosingForcedColors(): void
    {
        $css = $this->source('assets/css/modules/dungeon-master/monster-ledger.css');
        self::assertStringContainsString('gmrc-canonical-folio__rules', $css);
        self::assertStringContainsString('gmrc-canonical-folio__legendary', $css);
        self::assertStringContainsString('forced-colors: active', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}

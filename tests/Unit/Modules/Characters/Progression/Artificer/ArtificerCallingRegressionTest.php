<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Artificer;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\ArtificerProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\ArtificerSpellcastingProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArtificerCallingRegressionTest extends TestCase
{
    public function testArtificerUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())->forLevel(
            CharacterClass::fromString('artificer'),
            2
        );

        self::assertSame('artificer', $entry['class']);
        self::assertSame('reference', $entry['catalogue_status']);
    }

    public function testLevelOneFoundationsRemainTinkeringAndSpellcasting(): void
    {
        $foundations = (new ArtificerProgression())->foundations(
            CharacterClass::fromString('artificer')
        );

        self::assertSame(
            ['magical-tinkering', 'spellcasting'],
            array_column($foundations, 'key')
        );
    }

    public function testInfusionsBeginAtLevelTwo(): void
    {
        $entry = (new ArtificerProgression())->forLevel(
            CharacterClass::fromString('artificer'),
            2
        );

        self::assertSame(
            ['infuse-item'],
            array_column($entry['automatic'], 'key')
        );
    }

    public function testCoreInventorMilestonesRemainCertified(): void
    {
        $progression = new ArtificerProgression();
        $artificer = CharacterClass::fromString('artificer');
        $expected = [
            3 => 'right-tool-for-the-job',
            6 => 'tool-expertise',
            7 => 'flash-of-genius',
            10 => 'magic-item-adept',
            11 => 'spell-storing-item',
            14 => 'magic-item-savant',
            18 => 'magic-item-master',
            20 => 'soul-of-artifice',
        ];

        foreach ($expected as $level => $key) {
            self::assertContains(
                $key,
                array_column(
                    $progression->forLevel($artificer, $level)['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthMilestonesRemainDelegated(): void
    {
        $progression = new ArtificerProgression();
        $artificer = CharacterClass::fromString('artificer');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression->forLevel($artificer, $level)['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testSpecialisationGiftMilestonesRemainDelegatedForLaterSlice(): void
    {
        $progression = new ArtificerProgression();
        $artificer = CharacterClass::fromString('artificer');

        foreach ([3, 5, 9, 15] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression->forLevel($artificer, $level)['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testArtificerUsesPreparedIntelligenceHalfCasterModel(): void
    {
        $entry = (new ArtificerSpellcastingProgression())->forLevel(
            CharacterClass::fromString('artificer'),
            2
        );

        self::assertSame('prepared-spells', $entry['model']);
        self::assertNull($entry['spells_known']);
        self::assertSame(0, $entry['spells_learned']);
        self::assertSame(
            'half-artificer-level + intelligence-modifier',
            $entry['spells_prepared_formula']
        );
        self::assertSame(1, $entry['minimum_spells_prepared']);
    }

    public function testArtificerCantripProgressionRemainsTwoThreeFour(): void
    {
        $definition = new ArtificerSpellcastingProgression();
        $artificer = CharacterClass::fromString('artificer');

        self::assertSame(2, $definition->forLevel($artificer, 2)['cantrips_known']);
        self::assertSame(3, $definition->forLevel($artificer, 10)['cantrips_known']);
        self::assertSame(4, $definition->forLevel($artificer, 14)['cantrips_known']);
        self::assertSame(1, $definition->forLevel($artificer, 10)['cantrips_learned']);
        self::assertSame(1, $definition->forLevel($artificer, 14)['cantrips_learned']);
    }

    public function testArtificerHalfCastingReachesFifthCircleAtSeventeen(): void
    {
        $definition = new ArtificerSpellcastingProgression();
        $artificer = CharacterClass::fromString('artificer');

        self::assertSame(1, $definition->forLevel($artificer, 2)['maximum_spell_level']);
        self::assertSame(2, $definition->forLevel($artificer, 5)['maximum_spell_level']);
        self::assertSame(3, $definition->forLevel($artificer, 9)['maximum_spell_level']);
        self::assertSame(4, $definition->forLevel($artificer, 13)['maximum_spell_level']);
        self::assertSame(5, $definition->forLevel($artificer, 17)['maximum_spell_level']);
    }

    public function testSpellcastingCatalogueRecognisesArtificer(): void
    {
        $catalogue = new SpellcastingProgressionCatalogue();
        $artificer = CharacterClass::fromString('artificer');

        self::assertTrue($catalogue->supports($artificer));
        self::assertSame(
            'artificer',
            $catalogue->forLevel($artificer, 5)['class']
        );
    }

    public function testCapabilityAuditSeesArtificerAsSpellcastingPathSpecialist(): void
    {
        $profile = (new ClassCapabilityCatalogue())->forClass(
            CharacterClass::fromString('artificer')
        );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );
        self::assertTrue($profile->hasSpecialistAdvancement());
        self::assertTrue($profile->hasSpellcastingProgression());
        self::assertTrue($profile->hasCallingPathProgression());
    }

    public function testArtificerSpecialistDefinitionPrecedesRegisteredFallback(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/Models/ClassProgressionCatalogue.php'
        );

        self::assertStringContainsString('new ArtificerProgression()', $source);
        self::assertLessThan(
            strpos($source, 'new RegisteredCallingProgression()'),
            strpos($source, 'new ArtificerProgression()')
        );
    }

    public function testExistingArtificerIdentityRemainsD8WithConstitutionAndIntelligenceSaves(): void
    {
        $artificer = CharacterClass::fromString('artificer');

        self::assertSame(8, $artificer->hitDie());
        self::assertSame(
            ['constitution', 'intelligence'],
            $artificer->savingThrowProficiencies()
        );
    }

    public function testArtificerProgressionRejectsForeignCalling(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ArtificerProgression())->forLevel(
            CharacterClass::fromString('fighter'),
            2
        );
    }

    public function testArtificerSpellcastingRejectsForeignCalling(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ArtificerSpellcastingProgression())->forLevel(
            CharacterClass::fromString('wizard'),
            2
        );
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);
        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

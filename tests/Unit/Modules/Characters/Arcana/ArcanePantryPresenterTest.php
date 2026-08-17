<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Arcana;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Services\ArcanePantryPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class ArcanePantryPresenterTest extends TestCase
{
    public function testWizardUsesIntelligenceForSpellMath(): void
    {
        $arcana = $this->present('wizard', 16);

        self::assertSame('Intelligence', $arcana['casting_ability']);
        self::assertSame(5, $arcana['spell_attack']);
        self::assertSame(13, $arcana['save_dc']);
        self::assertSame(2, $arcana['slots'][0]['total']);
    }

    public function testWizardEntriesAreIndexedBySpellLevelAndFeatures(): void
    {
        $average = AbilityScore::fromInt(10);
        $intelligence = AbilityScore::fromInt(16);

        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(2700),
            HitPoints::fromValues(22, 22),
            AbilityScores::fromScores(
                $average,
                $average,
                $average,
                $intelligence,
                $average,
                $average
            )
        );

        $arcana = (new ArcanePantryPresenter(
            new ArcaneAbilityCatalogue()
        ))->present($character);

        self::assertSame(
            ['cantrips', 'level-1', 'level-2', 'features'],
            array_column($arcana['shelves'], 'key')
        );
        self::assertSame(
            ['Cantrips', 'Level 1', 'Level 2', 'Features'],
            array_column($arcana['shelves'], 'label')
        );
        self::assertSame(
            ['Produce Spark'],
            array_column($arcana['shelves'][0]['entries'], 'label')
        );
        self::assertContains(
            'Pantry Ward',
            array_column($arcana['shelves'][1]['entries'], 'label')
        );
        self::assertContains(
            'Aisle Step',
            array_column($arcana['shelves'][2]['entries'], 'label')
        );
        self::assertContains(
            'Pantry Recovery',
            array_column($arcana['shelves'][3]['entries'], 'label')
        );
        self::assertNotContains(
            'level-3',
            array_column($arcana['shelves'], 'key')
        );
    }

    public function testWizardCantripFormulaScalesAtCharacterLevelFive(): void
    {
        $average = AbilityScore::fromInt(10);
        $intelligence = AbilityScore::fromInt(16);

        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(5),
            Experience::fromInt(6500),
            HitPoints::fromValues(26, 26),
            AbilityScores::fromScores(
                $average,
                $average,
                $average,
                $intelligence,
                $average,
                $average
            )
        );

        $arcana = (new ArcanePantryPresenter(
            new ArcaneAbilityCatalogue()
        ))->present($character);

        $byId = [];

        foreach ($arcana['entries'] as $entry) {
            $byId[$entry['id']] = $entry;
        }

        self::assertSame('2d10', $byId['produce-spark']['formula']);
        self::assertSame('1d10', $byId['produce-spark']['base_formula']);
        self::assertSame(
            'character-level',
            $byId['produce-spark']['roll_scaling']['source']
        );
        self::assertSame(
            5,
            $byId['produce-spark']['roll_scaling']['resolved_at']
        );
        self::assertSame(
            [1 => '3d4', 2 => '4d4', 3 => '5d4', 4 => '6d4',
             5 => '7d4', 6 => '8d4', 7 => '9d4', 8 => '10d4',
             9 => '11d4'],
            $byId['market-missile']['roll_scaling']['slot_options']
        );
        self::assertSame(
            '3d4',
            $byId['market-missile']['formula']
        );
        self::assertSame(
            'creature',
            $byId['produce-spark']['target_mode']
        );
        self::assertSame(
            '',
            $byId['produce-spark']['default_target_kind']
        );
    }

    public function testGrocerReceivesClassFeaturesWithoutSpellSlots(): void
    {
        $arcana = $this->present('grocer', 10);

        self::assertNull($arcana['casting_ability']);
        self::assertSame([], $arcana['slots']);
        self::assertFalse($arcana['has_spells']);

        $labels = array_column($arcana['entries'], 'label');

        self::assertContains('Fresh Stock', $labels);
        self::assertContains('Emergency Restock', $labels);
    }

    public function testHealingAbilitiesCarryFormulaAndModifier(): void
    {
        $arcana = $this->present('cleric', 16);
        $healing = null;

        foreach ($arcana['entries'] as $entry) {
            if ($entry['id'] === 'restorative-preserve') {
                $healing = $entry;
                break;
            }
        }

        self::assertIsArray($healing);
        self::assertSame('healing', $healing['roll_kind']);
        self::assertSame('1d8', $healing['formula']);
        self::assertSame(3, $healing['roll_modifier']);
    }

    /** @return array<string,mixed> */
    private function present(
        string $class,
        int $castingScore
    ): array {
        $average = AbilityScore::fromInt(10);
        $casting = AbilityScore::fromInt($castingScore);

        $scores = AbilityScores::fromScores(
            $average,
            $average,
            $average,
            $class === 'wizard' ? $casting : $average,
            $class === 'cleric' ? $casting : $average,
            $average
        );

        $character = Character::create(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Test Adventurer'),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            HitPoints::full(8),
            $scores
        );

        return (new ArcanePantryPresenter(
            new ArcaneAbilityCatalogue()
        ))->present($character);
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\SpellbookFolio;
use PHPUnit\Framework\TestCase;

final class SpellbookFolioTest extends TestCase
{
    public function testWizardLevelTwoRequiresTwoExistingMarketrealmSpells(): void
    {
        $folio = (new SpellbookFolio())->build($this->character(), 2);
        self::assertNotNull($folio);
        $state = $folio->toArray();
        self::assertFalse($state['ready']);
        self::assertSame(2, $state['facts']['choice_minimum']);
        $choiceKeys = array_column(
            $state['choices'],
            'key'
        );

        self::assertContains(
            'pantry-ward',
            $choiceKeys
        );
        self::assertContains(
            'market-missile',
            $choiceKeys
        );
        self::assertGreaterThanOrEqual(
            8,
            count($choiceKeys)
        );
        self::assertSame(
            [1],
            array_values(
                array_unique(
                    array_column(
                        $state['choices'],
                        'spell_level'
                    )
                )
            )
        );
    }

    public function testTwoRecordedSpellChoicesMakeFolioReady(): void
    {
        $folio = (new SpellbookFolio())->build(
            $this->character(),
            2,
            ['pantry-ward', 'market-missile']
        );
        self::assertNotNull($folio);
        self::assertTrue($folio->toArray()['ready']);
    }


    public function testMarketrealmCatalogueStocksLevelTwoWizardStudies(): void
    {
        $catalogue = new \GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue();

        $wizardSpells = array_values(
            array_filter(
                $catalogue->forClass('wizard'),
                static fn ($ability): bool =>
                    $ability->kind() === 'spell'
                    && $ability->spellLevel() === 2
            )
        );

        $ids = array_map(
            static fn ($ability): string =>
                $ability->id(),
            $wizardSpells
        );

        self::assertGreaterThanOrEqual(
            8,
            count($ids)
        );
        self::assertContains(
            'aisle-step',
            $ids
        );
        self::assertContains(
            'cold-aisle-shard',
            $ids
        );
    }

    public function testLevelFourWizardHasEnoughNewSpellsForLevelFive(): void
    {
        $average = AbilityScore::fromInt(10);

        $wizard = Character::reconstitute(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(6500),
            HitPoints::fromValues(22, 22),
            AbilityScores::fromScores(
                $average,
                $average,
                AbilityScore::fromInt(14),
                AbilityScore::fromInt(16),
                $average,
                $average
            )
        );

        $wizard->learnArcana(
            [
                'pantry-ward',
                'market-missile',
                'aisle-step',
                'stockroom-veil',
                'price-freeze',
                'crate-levitation',
            ],
            ['produce-spark']
        );

        $folio = (new SpellbookFolio())->build(
            $wizard,
            5
        );

        self::assertNotNull($folio);

        $state = $folio->toArray();

        self::assertFalse($state['ready']);
        self::assertSame(
            2,
            $state['facts']['choice_minimum']
        );
        self::assertSame(
            0,
            $state['facts']['catalogue_shortfall']
        );
        self::assertGreaterThanOrEqual(
            2,
            $state['facts']['available_choices']
        );
        self::assertContains(
            3,
            array_column(
                $state['choices'],
                'spell_level'
            )
        );
        self::assertContains(
            'stockroom-fireball',
            array_column(
                $state['choices'],
                'key'
            )
        );
    }

    public function testWizardCatalogueHasBreathingRoomThroughThirdCircle(): void
    {
        $catalogue = new \GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue();

        $counts = [
            1 => 0,
            2 => 0,
            3 => 0,
        ];

        foreach ($catalogue->forClass('wizard') as $ability) {
            if (
                $ability->kind() === 'spell'
                && isset($counts[$ability->spellLevel()])
            ) {
                $counts[$ability->spellLevel()]++;
            }
        }

        self::assertGreaterThanOrEqual(
            8,
            $counts[1]
        );
        self::assertGreaterThanOrEqual(
            8,
            $counts[2]
        );
        self::assertGreaterThanOrEqual(
            8,
            $counts[3]
        );
    }

    private function character(): Character
    {
        return Character::create(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            HitPoints::full(8),
            AbilityScores::fromScores(
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(14),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            )
        );
    }
}

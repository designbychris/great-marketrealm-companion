<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\DruidProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleGroveRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleSpellCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidGroveArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\DruidSpellcastingProgression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DruidFinalSealRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function circles(): array
    {
        return [
            'circle-of-eating-fresh',
            'circle-of-the-groveflame',
            'circle-of-the-deep-soil',
            'circle-of-the-compost',
            'circle-of-curdle',
            'circle-of-the-churn',
        ];
    }

    public function testDruidRemainsSpecialistSpellcastingPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasSpellcastingProgression()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );
    }

    public function testDruidFoundationsRemainDruidicAndSpellcasting(): void
    {
        $foundations = (
            new DruidProgression()
        )->foundations(
            CharacterClass::fromString('druid')
        );

        self::assertSame(
            ['druidic', 'spellcasting'],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testWildShapeAndCircleStillBeginAtLevelTwo(): void
    {
        $entry = (
            new DruidProgression()
        )->forLevel(
            CharacterClass::fromString('druid'),
            2
        );

        self::assertSame(
            ['wild-shape', 'druid-circle'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        $path = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertSame(
            2,
            $path['selection_level']
        );
    }

    public function testExactlySixMarketrealmCirclesRemainRegistered(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('druid')
        );

        self::assertCount(
            6,
            $candidates
        );

        self::assertSame(
            $this->circles(),
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testEveryCircleRetainsTwoSixTenFourteenGiftMilestones(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ($this->circles() as $circle) {
            self::assertTrue(
                $catalogue->supports($circle)
            );

            self::assertSame(
                [2, 6, 10, 14],
                array_column(
                    $catalogue->all($circle),
                    'level'
                )
            );
        }
    }

    public function testDruidRemainsPreparedWisdomFullCaster(): void
    {
        $entry = (
            new DruidSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('druid'),
            10
        );

        self::assertSame(
            'prepared-spells',
            $entry['model']
        );

        self::assertNull(
            $entry['spells_known']
        );

        self::assertSame(
            'druid-level + wisdom-modifier',
            $entry['spells_prepared_formula']
        );

        self::assertSame(
            4,
            $entry['cantrips_known']
        );

        self::assertSame(
            5,
            $entry['maximum_spell_level']
        );
    }

    public function testDruidFullCastingStillReachesNinthCircleAtSeventeen(): void
    {
        $entry = (
            new DruidSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('druid'),
            17
        );

        self::assertSame(
            9,
            $entry['maximum_spell_level']
        );
    }

    public function testLevelOneGroveRegisterRemainsUsefulBeforeCircleSelection(): void
    {
        $register = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertFalse(
            $register[
                'wild_shape'
            ]['unlocked']
        );

        self::assertFalse(
            $register[
                'circle'
            ]['chosen']
        );

        self::assertSame(
            2,
            $register[
                'spellcasting'
            ]['cantrips_known']
        );

        self::assertSame(
            2,
            $register[
                'next_milestone'
            ]['level']
        );
    }

    public function testWildShapeRemainsTwoUsesPerShortOrLongRestFromLevelTwo(): void
    {
        $reserve = (
            new DruidPrimalReserveService()
        )->reserves(
            $this->druid(2),
            ActiveClassResourceState::fresh()
        )[0];

        self::assertSame(
            DruidPrimalReserveService::WILD_SHAPE,
            $reserve['resource']
        );

        self::assertSame(
            2,
            $reserve['maximum']
        );

        self::assertSame(
            'short-or-long-rest',
            $reserve['refresh']
        );
    }

    public function testArchdruidWildShapeRemainsUnlimited(): void
    {
        $reserve = (
            new DruidPrimalReserveService()
        )->reserves(
            $this->druid(20),
            ActiveClassResourceState::fresh()
        )[0];

        self::assertTrue(
            $reserve['unlimited']
        );

        self::assertNull(
            $reserve['maximum']
        );
    }

    public function testShortRestRestoresWildShapeButNotLongRestOnlyCircleUses(): void
    {
        $druid = $this->druid(
            14,
            'circle-of-the-groveflame'
        );

        $state = ActiveClassResourceState::fromArray([
            DruidPrimalReserveService::WILD_SHAPE => 1,
            DruidPrimalReserveService::SCORCHING_BLOOM => 1,
            DruidPrimalReserveService::PUNGENT_FLAME => 1,
        ]);

        $rested = (
            new DruidPrimalReserveService()
        )->shortRest(
            $druid,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::WILD_SHAPE
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                DruidPrimalReserveService::SCORCHING_BLOOM
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::PUNGENT_FLAME
            )
        );
    }

    public function testPrimalLongRestKeepsSpellSlotOwnershipSeparate(): void
    {
        $druid = $this->druid(
            10,
            'circle-of-the-churn'
        );

        $state = ActiveClassResourceState::fromArray([
            DruidPrimalReserveService::WILD_SHAPE => 1,
            DruidPrimalReserveService::GLACIAL_GROWTH => 1,
            'spell-slot-1' => 1,
        ]);

        $rested = (
            new DruidPrimalReserveService()
        )->longRest(
            $druid,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::WILD_SHAPE
            )
        );

        self::assertSame(
            0,
            $rested->expended(
                DruidPrimalReserveService::GLACIAL_GROWTH
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testSharedSpellSlotRestCompletesLongRestCycle(): void
    {
        $druid = $this->druid(
            5,
            'circle-of-the-compost'
        );

        $state = ActiveClassResourceState::fromArray([
            'spell-slot-1' => 1,
        ]);

        $rested = (
            new SharedSpellSlotReserveService()
        )->longRest(
            $druid,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testCompostSurgeRetainsPbUsesAndTwoReactionOptions(): void
    {
        $druid = $this->druid(
            2,
            'circle-of-the-compost',
            16
        );

        $reserves = (
            new DruidPrimalReserveService()
        )->reserves(
            $druid,
            ActiveClassResourceState::fresh()
        );

        $compost = array_values(
            array_filter(
                $reserves,
                static fn (
                    array $reserve
                ): bool =>
                    ($reserve['resource'] ?? '')
                    === DruidPrimalReserveService::COMPOST_SURGE
            )
        )[0];

        self::assertSame(
            $druid
                ->proficiencyBonus()
                ->value(),
            $compost['maximum']
        );

        $arts = (
            new DruidGroveArtsPresenter()
        )->present(
            $druid
        )['arts'];

        self::assertSame(
            [
                'reclaim-vitality',
                'recycle-into-harm',
            ],
            array_column(
                $arts[1]['choices'],
                'key'
            )
        );
    }

    public function testCompostSurgeHealingAndDamageRemainSourceFaithful(): void
    {
        $druid = $this->druid(
            7,
            'circle-of-the-compost',
            16
        );

        $surge = (
            new DruidGroveArtsPresenter()
        )->present(
            $druid
        )['arts'][1];

        self::assertSame(
            '1d6',
            $surge['choices'][0]['formula']
        );

        self::assertSame(
            $druid
                ->abilityScores()
                ->wisdom()
                ->modifier(),
            $surge['choices'][0]['modifier']
        );

        self::assertSame(
            '7',
            $surge['choices'][1]['static_value']
        );

        self::assertArrayNotHasKey(
            'formula',
            $surge['choices'][1]
        );
    }

    public function testCompostElementalStillSpendsWildShapeAndKeepsSplitDamage(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                14,
                'circle-of-the-compost'
            )
        )['arts'][4];

        self::assertSame(
            DruidPrimalReserveService::WILD_SHAPE,
            $art['resource']
        );

        self::assertSame(
            ['2d10', '2d6'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );
    }

    public function testMarketrealmOdditiesRemainProtected(): void
    {
        $presenter =
            new DruidGroveArtsPresenter();

        $fresh = $presenter->present(
            $this->druid(
                2,
                'circle-of-eating-fresh'
            )
        )['arts'][0];

        self::assertSame(
            '1 HP per round',
            $fresh[
                'static'
            ]['Natural-terrain healing']
        );

        $soil = $presenter->present(
            $this->druid(
                14,
                'circle-of-the-deep-soil'
            )
        )['arts'][3];

        self::assertSame(
            'DC 16',
            $soil['static']['Dexterity save']
        );

        $curdle = $presenter->present(
            $this->druid(
                6,
                'circle-of-curdle'
            )
        )['arts'][1];

        self::assertSame(
            '-1',
            $curdle['static']['AC penalty']
        );

        $churn = $presenter->present(
            $this->druid(
                14,
                'circle-of-the-churn'
            )
        )['arts'][3];

        self::assertSame(
            'Maximized',
            $churn[
                'static'
            ]['Healing/cold spells']
        );
    }

    public function testChurnCircleSpellTableRemainsExact(): void
    {
        self::assertSame(
            [
                ['level'=>3,'spells'=>['Ice Knife','Goodberry']],
                ['level'=>5,'spells'=>['Lesser Restoration',"Snilloc's Snowball Swarm"]],
                ['level'=>7,'spells'=>['Aura of Vitality','Sleet Storm']],
                ['level'=>9,'spells'=>['Freedom of Movement','Ice Storm']],
            ],
            (
                new DruidCircleSpellCatalogue()
            )->forCircle(
                'circle-of-the-churn'
            )
        );
    }

    public function testOtherCirclesStillDoNotInventCircleSpellTables(): void
    {
        self::assertSame(
            [],
            (
                new DruidCircleSpellCatalogue()
            )->forCircle(
                'circle-of-the-compost'
            )
        );
    }

    public function testBacteriaBloomStillHasNoInventedUseCounter(): void
    {
        $resources = array_column(
            (
                new DruidPrimalReserveService()
            )->reserves(
                $this->druid(
                    14,
                    'circle-of-curdle'
                ),
                ActiveClassResourceState::fresh()
            ),
            'resource'
        );

        self::assertNotContains(
            'druid-bacteria-bloom',
            $resources
        );
    }

    public function testFrozenCurdKeepsFreeUseSeparateFromWildShape(): void
    {
        $resources = array_column(
            (
                new DruidPrimalReserveService()
            )->reserves(
                $this->druid(
                    2,
                    'circle-of-the-churn'
                ),
                ActiveClassResourceState::fresh()
            ),
            'resource'
        );

        self::assertSame(
            [
                DruidPrimalReserveService::WILD_SHAPE,
                DruidPrimalReserveService::FROZEN_CURD,
            ],
            $resources
        );
    }

    public function testGroveArtsRemainLevelGated(): void
    {
        $presenter =
            new DruidGroveArtsPresenter();

        self::assertCount(
            2,
            $presenter->present(
                $this->druid(
                    2,
                    'circle-of-the-compost'
                )
            )['arts']
        );

        self::assertCount(
            3,
            $presenter->present(
                $this->druid(
                    6,
                    'circle-of-the-compost'
                )
            )['arts']
        );

        self::assertCount(
            5,
            $presenter->present(
                $this->druid(
                    14,
                    'circle-of-the-compost'
                )
            )['arts']
        );
    }

    public function testGroveArtsUseSharedDiceworksContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-druid-grove-arts',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-modifier=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-kind=',
            $view
        );

        self::assertStringNotContainsString(
            'data-druid-diceworks',
            $view
        );
    }

    public function testDruidActionsReusePrimalSpendAndRestRoutes(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/characters/{id}/primal/spend',
            $routes
        );

        self::assertStringContainsString(
            '/characters/{id}/primal/rest',
            $routes
        );

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-druid-primal-spend=',
            $view
        );

        self::assertStringContainsString(
            'data-druid-grove-use=',
            $view
        );
    }

    public function testFinalSealHardensLongLabelsAndVeryNarrowScreens(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.10E — The Druid's Final Seal",
            $css
        );

        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 380px)',
            $css
        );

        self::assertStringContainsString(
            'min-height: 2.75rem',
            $css
        );
    }

    public function testFinalSealPreservesReducedMotionAndForcedColours(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );

        self::assertStringContainsString(
            'ButtonText',
            $css
        );
    }

    public function testPrimalReserveServiceStillRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new DruidPrimalReserveService()
        )->reserves(
            $this->character(
                'wizard',
                5,
                ''
            ),
            ActiveClassResourceState::fresh()
        );
    }

    private function druid(
        int $level,
        string $circle = '',
        int $wisdom = 10
    ): Character {
        return $this->character(
            'druid',
            $level,
            $circle,
            $wisdom
        );
    }

    private function character(
        string $class,
        int $level,
        string $circle,
        int $wisdom = 10
    ): Character {
        $scores = AbilityScores::average()
            ->withWisdom(
                AbilityScore::fromInt(
                    $wisdom
                )
            );

        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Druid Final Seal'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            $scores,
            callingPath:
                CallingPath::fromString(
                    $circle
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
        return dirname(__DIR__, 6);
    }
}

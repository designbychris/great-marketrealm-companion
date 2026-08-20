<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\DruidPrimalReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidGroveArtsPresenter;
use PHPUnit\Framework\TestCase;

final class DruidGroveArtsRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new DruidGroveArtsPresenter()
            )->present(
                $this->character(
                    'wizard',
                    2,
                    ''
                )
            )['supported']
        );
    }

    public function testDruidWithoutCircleHasNoGroveArts(): void
    {
        $presented = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(1)
        );

        self::assertTrue(
            $presented['supported']
        );

        self::assertSame(
            [],
            $presented['arts']
        );
    }

    public function testLevelTwoCompostDruidGetsRotboundAffinityAndCompostSurge(): void
    {
        $arts = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-compost'
            )
        )['arts'];

        self::assertSame(
            [
                'rotbound-affinity',
                'compost-surge',
            ],
            array_column(
                $arts,
                'key'
            )
        );
    }

    public function testCompostSurgePreservesBothReactionChoices(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-compost'
            )
        )['arts'][1];

        self::assertSame(
            [
                'reclaim-vitality',
                'recycle-into-harm',
            ],
            array_column(
                $art['choices'],
                'key'
            )
        );

        self::assertSame(
            DruidPrimalReserveService::COMPOST_SURGE,
            $art['resource']
        );
    }

    public function testCompostSurgeHealingUsesOneD6PlusWisdom(): void
    {
        $druid = $this->druid(
            2,
            'circle-of-the-compost',
            16
        );

        $choice = (
            new DruidGroveArtsPresenter()
        )->present(
            $druid
        )['arts'][1]['choices'][0];

        self::assertSame(
            '1d6',
            $choice['formula']
        );

        self::assertSame(
            $druid
                ->abilityScores()
                ->wisdom()
                ->modifier(),
            $choice['modifier']
        );

        self::assertSame(
            'healing',
            $choice['kind']
        );
    }

    public function testCompostSurgeDamageIsStaticDruidLevelNotInventedDice(): void
    {
        $choice = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                7,
                'circle-of-the-compost'
            )
        )['arts'][1]['choices'][1];

        self::assertSame(
            '7',
            $choice['static_value']
        );

        self::assertArrayNotHasKey(
            'formula',
            $choice
        );
    }

    public function testMulchbornUsesSuppliedTwoD8AndCalculatedSaveDc(): void
    {
        $druid = $this->druid(
            6,
            'circle-of-the-compost',
            16
        );

        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $druid
        )['arts'][2];

        self::assertSame(
            '2d8',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            'poison',
            $art['rolls'][0]['damage_type']
        );

        self::assertSame(
            (string) (
                8
                + $druid
                    ->abilityScores()
                    ->wisdom()
                    ->modifier()
                + $druid
                    ->proficiencyBonus()
                    ->value()
            ),
            $art['static']['Save DC']
        );
    }

    public function testBloomOfDecayKeepsDamageAndHealingSeparate(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                10,
                'circle-of-the-compost'
            )
        )['arts'][3];

        self::assertSame(
            ['4d6', '1d6'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );

        self::assertSame(
            ['damage', 'healing'],
            array_column(
                $art['rolls'],
                'kind'
            )
        );

        self::assertSame(
            [
                DruidPrimalReserveService::BLIGHT,
                DruidPrimalReserveService::INSECT_PLAGUE,
            ],
            array_column(
                $art['choices'],
                'resource'
            )
        );
    }

    public function testCompostElementalSpendsWildShapeAndPreservesSlamDice(): void
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

        self::assertSame(
            '28',
            $art['static']['Temporary HP']
        );
    }

    public function testGroveflameScorchingBloomRollsFourD8Fire(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                14,
                'circle-of-the-groveflame'
            )
        )['arts'][3];

        self::assertSame(
            '4d8',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            'fire',
            $art['rolls'][0]['damage_type']
        );
    }

    public function testDeepSoilPreservesFixedDcSixteen(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                14,
                'circle-of-the-deep-soil'
            )
        )['arts'][3];

        self::assertSame(
            'DC 16',
            $art['static']['Dexterity save']
        );
    }

    public function testCurdleBacteriaBloomCalculatesTemporaryHpWithoutInventedUses(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                14,
                'circle-of-curdle',
                16
            )
        )['arts'][3];

        self::assertSame(
            '17',
            $art['static']['Ally temporary HP']
        );

        self::assertNull(
            $art['resource']
        );
    }

    public function testChurnFrozenCurdOffersFreeAndWildShapeRoutes(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-churn'
            )
        )['arts'][0];

        self::assertSame(
            [
                DruidPrimalReserveService::FROZEN_CURD,
                DruidPrimalReserveService::WILD_SHAPE,
            ],
            array_column(
                $art['choices'],
                'resource'
            )
        );
    }

    public function testCreammotherUsesSuppliedOneD6TemporaryHp(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                6,
                'circle-of-the-churn'
            )
        )['arts'][1];

        self::assertSame(
            '1d6',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            'healing',
            $art['rolls'][0]['kind']
        );
    }

    public function testEatingFreshKeepsOneHpPerRoundStatic(): void
    {
        $art = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-eating-fresh'
            )
        )['arts'][0];

        self::assertSame(
            '1 HP per round',
            $art[
                'static'
            ]['Natural-terrain healing']
        );

        self::assertSame(
            [],
            $art['rolls']
        );
    }

    public function testPresenterOnlyShowsCurrentLevelArts(): void
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

    public function testPresenterCarriesLivePrimalReserveState(): void
    {
        $presented = (
            new DruidGroveArtsPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-compost'
            ),
            ActiveClassResourceState::fromArray([
                DruidPrimalReserveService::COMPOST_SURGE => 1,
            ])
        );

        $reserves = $presented[
            'primal_reserves'
        ];

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
            1,
            $compost['expended']
        );
    }

    public function testControllerSuppliesGroveArtsToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'DruidGroveArtsPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'groveArts' => \$groveArts",
            $controller
        );
    }

    public function testLedgerUsesCorrectSharedDiceworksContract(): void
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
            'data-guild-roll="<?php echo esc_attr(',
            $view
        );
    }

    public function testLedgerReusesPrimalSpendRouteForGroveArts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            ". '/primal/spend'",
            $view
        );

        self::assertStringContainsString(
            'data-druid-grove-use=',
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_primal_',
            $view
        );
    }

    public function testGroveArtsUiIsResponsiveReducedMotionAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-druid-grove-arts',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 720px)',
            $css
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
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
                'Smelly Grove Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            $scores,
            callingPath:
                CallingPath::fromString($circle)
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

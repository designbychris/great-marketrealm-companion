<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Cleric;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\ClericSacredReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services\ClericDivineArtsPresenter;
use PHPUnit\Framework\TestCase;

final class ClericDivineArtsRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->character(
                    'wizard',
                    5,
                    ''
                )
            )['supported']
        );
    }

    public function testLevelOneClericWithoutDomainHasNoDivineArtsYet(): void
    {
        $presented = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(1)
        );

        self::assertTrue(
            $presented['supported']
        );

        self::assertSame(
            [],
            $presented['arts']
        );
    }

    public function testTurnUndeadAppearsAtTwoAndUsesSharedChannelDivinity(): void
    {
        $art = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(2)
        )['arts'][0];

        self::assertSame(
            'turn-undead',
            $art['key']
        );

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $art['resource']
        );
    }

    public function testDestroyUndeadTracksCurrentThreshold(): void
    {
        $arts = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(17)
        )['arts'];

        $destroy = array_values(
            array_filter(
                $arts,
                static fn (
                    array $art
                ): bool =>
                    ($art['key'] ?? '')
                    === 'destroy-undead'
            )
        )[0];

        self::assertSame(
            'CR 4',
            $destroy[
                'static'
            ]['Current threshold']
        );
    }

    public function testDivineInterventionDoesNotInventResourceCounter(): void
    {
        $arts = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(10)
        )['arts'];

        $intervention = array_values(
            array_filter(
                $arts,
                static fn (
                    array $art
                ): bool =>
                    ($art['key'] ?? '')
                    === 'divine-intervention'
            )
        )[0];

        self::assertNull(
            $intervention['resource']
        );
    }

    public function testSweetSanctuaryCalculatesTempHpFromLevelAndWisdom(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    5,
                    'domain-of-sweetness',
                    16
                )
            )['arts'],
            'sweet-sanctuary'
        );

        self::assertSame(
            '8',
            $art[
                'static'
            ]['Temporary HP']
        );
    }

    public function testSugarburstUsesSharedChannelAndOneD6AllyTempHp(): void
    {
        $arts = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(
                2,
                'domain-of-sweetness'
            )
        )['arts'];

        $sugarburst = array_values(
            array_filter(
                $arts,
                static fn (
                    array $art
                ): bool =>
                    ($art['key'] ?? '')
                    === 'sugarburst'
            )
        )[0];

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $sugarburst['resource']
        );

        self::assertSame(
            '1d6',
            $sugarburst['rolls'][0]['formula']
        );
    }

    public function testSweetnessDivineStrikeImprovesAtFourteen(): void
    {
        $presenter =
            new ClericDivineArtsPresenter();

        $early = $this->art(
            $presenter->present(
                $this->cleric(
                    8,
                    'domain-of-sweetness'
                )
            )['arts'],
            'sticky-smite'
        );

        $late = $this->art(
            $presenter->present(
                $this->cleric(
                    14,
                    'domain-of-sweetness'
                )
            )['arts'],
            'sticky-smite'
        );

        self::assertSame(
            '1d8',
            $early['rolls'][0]['formula']
        );

        self::assertSame(
            '2d8',
            $late['rolls'][0]['formula']
        );
    }

    public function testOrderUpUsesChannelDivinityWithoutInventedRoll(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    2,
                    'domain-of-the-golden-arches'
                )
            )['arts'],
            'order-up'
        );

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $art['resource']
        );

        self::assertSame(
            [],
            $art['rolls']
        );
    }

    public function testHolyButterstormIsRealAndUsesSplitDamageDice(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    17,
                    'domain-of-dairy'
                )
            )['arts'],
            'holy-butterstorm'
        );

        self::assertTrue(
            $art['celebratory']
        );

        self::assertSame(
            ClericSacredReserveService::HOLY_BUTTERSTORM,
            $art['resource']
        );

        self::assertSame(
            ['6d8', '2d8'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );

        self::assertSame(
            ['radiant', 'fire'],
            array_column(
                $art['rolls'],
                'damage_type'
            )
        );

        self::assertSame(
            'UNLEASH HOLY BUTTERSTORM',
            $art['resource_action']
        );
    }

    public function testCulturedSmiteOffersRadiantAndColdWithoutDoubleSpending(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    8,
                    'domain-of-dairy'
                )
            )['arts'],
            'cultured-smite'
        );

        self::assertSame(
            ['radiant', 'cold'],
            array_column(
                $art['rolls'],
                'damage_type'
            )
        );

        self::assertNull(
            $art['resource']
        );
    }

    public function testSearingSeasoningPreservesThreeDamageChoices(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    6,
                    'domain-of-seasoning'
                )
            )['arts'],
            'searing-seasoning'
        );

        self::assertSame(
            ['fire', 'poison', 'acid'],
            array_column(
                $art['rolls'],
                'damage_type'
            )
        );

        self::assertSame(
            ['1d8', '1d8', '1d8'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );
    }

    public function testBlessedBrineUsesChannelDivinityAndOneD6Healing(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    2,
                    'domain-of-cultivation'
                )
            )['arts'],
            'blessed-brine'
        );

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $art['resource']
        );

        self::assertSame(
            '1d6',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            'healing',
            $art['rolls'][0]['kind']
        );
    }

    public function testCultivatedPotencyDisplaysCurrentWisdomModifier(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    8,
                    'domain-of-cultivation',
                    16
                )
            )['arts'],
            'cultivated-potency'
        );

        self::assertSame(
            '+3',
            $art[
                'static'
            ]['Current Wisdom bonus']
        );
    }

    public function testFermentTouchPreservesHealCorpseAndEnemyBranches(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    1,
                    'domain-of-fermentation',
                    16
                )
            )['arts'],
            'ferment-touch'
        );

        self::assertSame(
            [
                'ferment-touch-heal',
                'ferment-touch-preserve',
                'ferment-touch-sour',
            ],
            array_column(
                $art['choices'],
                'key'
            )
        );

        self::assertSame(
            '1d8',
            $art['choices'][0]['formula']
        );

        self::assertSame(
            3,
            $art['choices'][0]['modifier']
        );

        self::assertArrayNotHasKey(
            'formula',
            $art['choices'][1]
        );
    }

    public function testFermentTouchEnemyDamageScalesAtFiveElevenSeventeen(): void
    {
        $presenter =
            new ClericDivineArtsPresenter();

        $expected = [
            1 => '1d8',
            5 => '2d8',
            11 => '3d8',
            17 => '4d8',
        ];

        foreach (
            $expected
            as $level => $formula
        ) {
            $art = $this->art(
                $presenter->present(
                    $this->cleric(
                        $level,
                        'domain-of-fermentation'
                    )
                )['arts'],
                'ferment-touch'
            );

            self::assertSame(
                $formula,
                $art['choices'][2]['formula']
            );
        }
    }

    public function testFunkOfTheDivineUsesChannelAndTwoD10PlusClericLevel(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    7,
                    'domain-of-fermentation'
                )
            )['arts'],
            'funk-of-the-divine'
        );

        self::assertSame(
            ClericSacredReserveService::CHANNEL_DIVINITY,
            $art['resource']
        );

        self::assertSame(
            '2d10',
            $art['choices'][0]['formula']
        );

        self::assertSame(
            7,
            $art['choices'][0]['modifier']
        );

        self::assertSame(
            ['radiant', 'poison'],
            array_column(
                $art['choices'],
                'damage_type'
            )
        );
    }

    public function testMotherCultureKeepsHealingAndDamageSeparate(): void
    {
        $art = $this->art(
            (
                new ClericDivineArtsPresenter()
            )->present(
                $this->cleric(
                    17,
                    'domain-of-fermentation'
                )
            )['arts'],
            'mother-culture'
        );

        self::assertSame(
            '2d6',
            $art['rolls'][0]['formula']
        );

        self::assertSame(
            ['4d6', '4d6'],
            array_column(
                $art['choices'],
                'formula'
            )
        );

        self::assertSame(
            ['radiant', 'poison'],
            array_column(
                $art['choices'],
                'damage_type'
            )
        );
    }

    public function testPresenterOnlyShowsArtsUnlockedAtCurrentLevel(): void
    {
        $presenter =
            new ClericDivineArtsPresenter();

        self::assertCount(
            1,
            $presenter->present(
                $this->cleric(
                    1,
                    'domain-of-dairy'
                )
            )['arts']
        );

        self::assertCount(
            3,
            $presenter->present(
                $this->cleric(
                    2,
                    'domain-of-dairy'
                )
            )['arts']
        );
    }

    public function testPresenterCarriesLiveSacredReserveState(): void
    {
        $presented = (
            new ClericDivineArtsPresenter()
        )->present(
            $this->cleric(
                2,
                'domain-of-sweetness'
            ),
            ActiveClassResourceState::fromArray([
                ClericSacredReserveService::CHANNEL_DIVINITY => 1,
            ])
        );

        $channel = array_values(
            array_filter(
                $presented[
                    'sacred_reserves'
                ],
                static fn (
                    array $reserve
                ): bool =>
                    ($reserve['resource'] ?? '')
                    === ClericSacredReserveService::CHANNEL_DIVINITY
            )
        )[0];

        self::assertSame(
            1,
            $channel['expended']
        );

        self::assertSame(
            0,
            $channel['remaining']
        );
    }

    public function testControllerSuppliesDivineArtsToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'ClericDivineArtsPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'divineArts' => \$divineArts",
            $controller
        );
    }

    public function testLedgerUsesSharedGuildDiceworksContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-cleric-divine-arts',
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
            'data-cleric-diceworks',
            $view
        );
    }

    public function testLedgerReusesDevotionSpendRouteForFiniteArts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            ". '/devotion/spend'",
            $view
        );

        self::assertStringContainsString(
            'data-cleric-divine-use=',
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_devotion_',
            $view
        );
    }

    public function testHolyButterstormGetsDedicatedButtonTreatment(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'gmrc-button--holy-butterstorm',
            $view
        );

        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-button--holy-butterstorm',
            $css
        );
    }

    public function testDivineArtsUiIsResponsiveReducedMotionAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-cleric-divine-arts',
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

    /**
     * @param array<int,array<string,mixed>> $arts
     * @return array<string,mixed>
     */
    private function art(
        array $arts,
        string $key
    ): array {
        foreach ($arts as $art) {
            if (
                ($art['key'] ?? '')
                === $key
            ) {
                return $art;
            }
        }

        self::fail(
            'Expected Divine Art not found: '
            . $key
        );
    }

    private function cleric(
        int $level,
        string $domain = '',
        int $wisdom = 10
    ): Character {
        return $this->character(
            'cleric',
            $level,
            $domain,
            $wisdom
        );
    }

    private function character(
        string $class,
        int $level,
        string $domain,
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
                'Divine Arts Tester'
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
                    $domain
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

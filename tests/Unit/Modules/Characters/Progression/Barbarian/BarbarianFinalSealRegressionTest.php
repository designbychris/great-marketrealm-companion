<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassConditionState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\BarbarianRageReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\BarbarianProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianPrimalActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BarbarianFinalSealRegressionTest extends TestCase
{
    public function testBarbarianRemainsSpecialistWithoutSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString(
                    'barbarian'
                )
            );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );

        self::assertFalse(
            $profile->hasSpellcastingProgression()
        );
    }

    public function testCoreBarbarianMilestonesRemainStable(): void
    {
        $progression = new BarbarianProgression();
        $barbarian = CharacterClass::fromString(
            'barbarian'
        );

        $expectations = [
            2 => [
                'reckless-attack',
                'danger-sense',
            ],
            5 => [
                'extra-attack',
                'fast-movement',
            ],
            7 => ['feral-instinct'],
            9 => ['brutal-critical'],
            11 => ['relentless-rage'],
            13 => ['brutal-critical'],
            15 => ['persistent-rage'],
            17 => ['brutal-critical'],
            18 => ['indomitable-might'],
            20 => ['primal-champion'],
        ];

        foreach (
            $expectations
            as $level => $keys
        ) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $barbarian,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthAndPathGiftDelegationsRemainSeparated(): void
    {
        $progression = new BarbarianProgression();
        $barbarian = CharacterClass::fromString(
            'barbarian'
        );

        foreach (
            [4, 6, 8, 12, 16, 19]
            as $level
        ) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $barbarian,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }

        foreach (
            [3, 6, 10, 14]
            as $level
        ) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $barbarian,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testLevelThreeOwnsPrimalPathAndFirstGift(): void
    {
        $entry = (
            new BarbarianProgression()
        )->forLevel(
            CharacterClass::fromString(
                'barbarian'
            ),
            3
        );

        self::assertSame(
            [
                'path',
                'path-gifts',
            ],
            array_column(
                $entry['delegated'],
                'folio'
            )
        );

        $path = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'barbarian'
            )
        );

        self::assertIsArray($path);

        self::assertSame(
            3,
            $path['selection_level']
        );
    }

    public function testAllEightPrimalPathsKeepFourGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach (
            $this->paths()
            as $path
        ) {
            self::assertTrue(
                $catalogue->supports($path)
            );

            self::assertSame(
                [3, 6, 10, 14],
                array_column(
                    $catalogue->all($path),
                    'level'
                )
            );
        }
    }

    public function testButcheredRageBenchmarkKeepsItsCertifiedGiftOrder(): void
    {
        self::assertSame(
            [
                'bloodied-cleaver',
                'butchers-instinct',
                'carving-frenzy',
                'slaughterhouse-fury',
            ],
            array_column(
                (new PathGiftCatalogue())
                    ->all(
                        'path-of-the-butchered-rage'
                    ),
                'key'
            )
        );
    }

    public function testEveryPrimalPathKeepsChoiceGuidance(): void
    {
        $choices = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'barbarian'
            )
        );

        self::assertCount(
            8,
            $choices
        );

        foreach ($choices as $choice) {
            self::assertNotSame(
                '',
                $choice['identity']
            );

            self::assertNotSame(
                '',
                $choice['playstyle']
            );

            self::assertNotSame(
                '',
                $choice['best_for']
            );

            self::assertCount(
                4,
                $choice['gift_preview']
            );
        }
    }

    public function testRageMaximumTracksCriticalLevels(): void
    {
        $service =
            new BarbarianRageReserveService();

        foreach ([
            1 => 2,
            3 => 3,
            6 => 4,
            12 => 5,
            17 => 6,
            20 => 0,
        ] as $level => $maximum) {
            self::assertSame(
                $maximum,
                $service->maximum(
                    $this->barbarian($level)
                )
            );
        }

        self::assertTrue(
            $service->unlimited(
                $this->barbarian(20)
            )
        );
    }

    public function testRageDamageHasSingleCertifiedScalingAuthority(): void
    {
        $service =
            new BarbarianRageReserveService();

        self::assertSame(
            2,
            $service->damageBonus(
                $this->barbarian(8)
            )
        );

        self::assertSame(
            3,
            $service->damageBonus(
                $this->barbarian(9)
            )
        );

        self::assertSame(
            4,
            $service->damageBonus(
                $this->barbarian(16)
            )
        );

        $registerSource = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Primal/Services/'
            . 'BarbarianRageRegisterPresenter.php'
        );

        $actionSource = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Primal/Services/'
            . 'BarbarianPrimalActionPresenter.php'
        );

        self::assertStringNotContainsString(
            'private function rageDamageBonus',
            $registerSource
        );

        self::assertStringNotContainsString(
            'private function rageDamageBonus',
            $actionSource
        );
    }

    public function testSpentRageSurvivesLevelIncreaseWithoutStaleMaximum(): void
    {
        $spent = ActiveClassResourceState::fromArray([
            'rage' => 2,
        ]);

        $register = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->barbarian(6),
            $spent,
            ActiveClassConditionState::fresh()
        );

        self::assertSame(
            4,
            $register['rage']['maximum']
        );

        self::assertSame(
            2,
            $register['rage']['remaining']
        );

        self::assertSame(
            2,
            $register['rage']['expended']
        );
    }

    public function testEnterEndAndLongRestKeepDistinctRageSemantics(): void
    {
        $service =
            new BarbarianRageReserveService();

        $entered = $service->enter(
            $this->barbarian(3),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
        );

        self::assertTrue(
            $entered['conditions']->active(
                'rage'
            )
        );

        self::assertSame(
            1,
            $entered['resources']->expended(
                'rage'
            )
        );

        $ended = $service->end(
            $this->barbarian(3),
            $entered['conditions']
        );

        self::assertFalse(
            $ended->active('rage')
        );

        self::assertSame(
            1,
            $entered['resources']->expended(
                'rage'
            )
        );

        $rested = $service->longRest(
            $this->barbarian(3),
            $entered['resources'],
            $entered['conditions']
        );

        self::assertSame(
            0,
            $rested['resources']->expended(
                'rage'
            )
        );

        self::assertFalse(
            $rested['conditions']->active(
                'rage'
            )
        );
    }

    public function testUnlimitedRageNeverSpendsFiniteReserve(): void
    {
        $next = (
            new BarbarianRageReserveService()
        )->enter(
            $this->barbarian(20),
            ActiveClassResourceState::fresh(),
            ActiveClassConditionState::fresh()
        );

        self::assertSame(
            0,
            $next['resources']->expended(
                'rage'
            )
        );

        self::assertTrue(
            $next['conditions']->active(
                'rage'
            )
        );
    }

    public function testRageRegisterKeepsCertifiedPathAndOnlyPersistedGifts(): void
    {
        $register = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->barbarian(
                10,
                'path-of-the-butchered-rage',
                [
                    'bloodied-cleaver',
                    'butchers-instinct',
                ]
            )
        );

        self::assertSame(
            'Path of the Butchered Rage',
            $register['path']['label']
        );

        self::assertSame(
            [
                'bloodied-cleaver',
                'butchers-instinct',
            ],
            array_column(
                $register['path_gifts'],
                'key'
            )
        );

        self::assertNotContains(
            'carving-frenzy',
            array_column(
                $register['path_gifts'],
                'key'
            )
        );
    }

    public function testPrimalActionsKeepRealCharacterModifiersAndState(): void
    {
        $actions = (
            new BarbarianPrimalActionPresenter()
        )->present(
            $this->barbarian(11),
            ActiveClassConditionState::fresh()
                ->activate('rage')
        );

        self::assertTrue(
            $actions['rage_active']
        );

        self::assertSame(
            'advantage',
            $actions['actions'][2][
                'roll'
            ]['default_mode']
        );

        self::assertTrue(
            $actions['actions'][4][
                'available'
            ]
        );

        self::assertSame(
            'constitution',
            $actions['actions'][4][
                'roll'
            ]['ability']
        );
    }

    public function testPrimalSystemsRemainIsolatedFromFighter(): void
    {
        $fighter = $this->character(
            'fighter',
            11
        );

        self::assertFalse(
            (
                new BarbarianRageRegisterPresenter()
            )->present(
                $fighter
            )['supported']
        );

        self::assertFalse(
            (
                new BarbarianPrimalActionPresenter()
            )->present(
                $fighter
            )['supported']
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BarbarianRageReserveService()
        )->maximum($fighter);
    }

    public function testRageRoutesAndNonceContractRemainProtected(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        $provider = $this->source(
            'app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        foreach ([
            "'/characters/{id}/rage/enter'",
            "'/characters/{id}/rage/end'",
            "'/characters/{id}/rage/rest'",
        ] as $route) {
            self::assertStringContainsString(
                $route,
                $routes
            );
        }

        self::assertStringContainsString(
            '#^characters/([^/]+)/rage/(?:enter|end|rest)$#',
            $provider
        );

        self::assertStringContainsString(
            "'gmrc_character_rage_'",
            $provider
        );
    }

    public function testActiveStateRepositoriesRemainOwnerScoped(): void
    {
        $resourceRepo = $this->source(
            'app/Modules/Characters/ActivePlay/'
            . 'Repositories/'
            . 'ActiveClassResourceRepository.php'
        );

        $conditionRepo = $this->source(
            'app/Modules/Characters/ActivePlay/'
            . 'Repositories/'
            . 'ActiveClassConditionRepository.php'
        );

        self::assertStringContainsString(
            "'author' => get_current_user_id()",
            $resourceRepo
        );

        self::assertStringContainsString(
            "'author' => get_current_user_id()",
            $conditionRepo
        );

        self::assertStringContainsString(
            "'_gmrc_active_class_resources'",
            $resourceRepo
        );

        self::assertStringContainsString(
            "'_gmrc_active_class_conditions'",
            $conditionRepo
        );
    }

    public function testFinalBarbarianSurfaceKeepsAccessibilityAndMotionBoundaries(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-rage-register-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-rage-reserves-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-primal-actions-title"',
            $view
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

    public function testDangerSenseKeepsSharedDiceworksAdvantageContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        $dice = $this->source(
            'assets/js/modules/characters/'
            . 'guild-dice.js'
        );

        self::assertStringContainsString(
            'data-roll-default-mode=',
            $view
        );

        self::assertStringContainsString(
            'dataset.rollDefaultMode',
            $dice
        );

        self::assertStringContainsString(
            'selection.defaultMode',
            $dice
        );

        self::assertStringContainsString(
            'preferredMode',
            $dice
        );
    }

    /**
     * @return array<int,string>
     */
    private function paths(): array
    {
        return [
            'path-of-the-great-tony',
            'path-of-the-expired',
            'path-of-the-marbled-rage',
            'path-of-the-rind',
            'path-of-the-butchered-rage',
            'path-of-the-sugarrush',
            'path-of-the-pickled-rage',
            'path-of-the-butterbound',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function barbarian(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Seal Barbarian'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'barbarian'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(30),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $path
                ),
            pathGifts:
                PathGifts::fromArray(
                    $gifts
                )
        );
    }

    private function character(
        string $class,
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Barbarian Isolation Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(30),
            AbilityScores::average()
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

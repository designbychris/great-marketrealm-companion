<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\FighterBattleReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\FighterProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FighterFinalSealRegressionTest extends TestCase
{
    public function testFighterRemainsSpecialistWithoutSpellcastingDependency(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString(
                    'fighter'
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

    public function testCoreFighterMilestonesRemainStableAcrossLevels(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString(
            'fighter'
        );

        $expectations = [
            2 => ['action-surge'],
            3 => [],
            5 => ['extra-attack'],
            9 => ['indomitable'],
            11 => ['extra-attack'],
            13 => ['indomitable'],
            17 => [
                'action-surge',
                'indomitable',
            ],
            20 => ['extra-attack'],
        ];

        foreach (
            $expectations
            as $level => $keys
        ) {
            $entry = $progression->forLevel(
                $fighter,
                $level
            );

            self::assertSame(
                $keys,
                array_column(
                    $entry['automatic'],
                    'key'
                )
            );
        }
    }

    public function testFighterGrowthAndPathGiftDelegationsRemainSeparated(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString(
            'fighter'
        );

        foreach (
            [4, 6, 8, 12, 14, 16, 19]
            as $level
        ) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $fighter,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }

        foreach (
            [7, 10, 15, 18]
            as $level
        ) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $fighter,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testLevelThreeOwnsBothMartialPathAndFirstPathGift(): void
    {
        $entry = (
            new FighterProgression()
        )->forLevel(
            CharacterClass::fromString(
                'fighter'
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
                'fighter'
            )
        );

        self::assertIsArray($path);

        self::assertSame(
            3,
            $path['selection_level']
        );
    }

    public function testAllSixMartialPathsKeepFullFiveGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach (
            $this->paths()
            as $path
        ) {
            $gifts = $catalogue->all(
                $path
            );

            self::assertCount(
                5,
                $gifts
            );

            self::assertSame(
                [3, 7, 10, 15, 18],
                array_column(
                    $gifts,
                    'level'
                )
            );
        }
    }

    public function testMartialRegisterTracksAttackProgressionAtBoundaries(): void
    {
        $presenter =
            new FighterMartialRegisterPresenter();

        $expected = [
            1 => 1,
            4 => 1,
            5 => 2,
            10 => 2,
            11 => 3,
            19 => 3,
            20 => 4,
        ];

        foreach (
            $expected
            as $level => $attacks
        ) {
            self::assertSame(
                $attacks,
                $presenter->present(
                    $this->fighter($level)
                )['attacks_per_action']
            );
        }
    }

    public function testBattleReserveMaximumsTrackCriticalFighterLevels(): void
    {
        $service =
            new FighterBattleReserveService();

        self::assertSame(
            1,
            $service->maximum(
                $this->fighter(1),
                'second-wind'
            )
        );

        self::assertSame(
            0,
            $service->maximum(
                $this->fighter(1),
                'action-surge'
            )
        );

        self::assertSame(
            1,
            $service->maximum(
                $this->fighter(2),
                'action-surge'
            )
        );

        self::assertSame(
            2,
            $service->maximum(
                $this->fighter(17),
                'action-surge'
            )
        );

        self::assertSame(
            1,
            $service->maximum(
                $this->fighter(9),
                'indomitable'
            )
        );

        self::assertSame(
            2,
            $service->maximum(
                $this->fighter(13),
                'indomitable'
            )
        );

        self::assertSame(
            3,
            $service->maximum(
                $this->fighter(17),
                'indomitable'
            )
        );
    }

    public function testSpentReserveSurvivesLevelIncreaseWithoutStaleMaximum(): void
    {
        $spent = ActiveClassResourceState::fresh()
            ->spend(
                'action-surge',
                1
            );

        $register = (
            new FighterMartialRegisterPresenter()
        )->present(
            $this->fighter(17),
            $spent
        );

        self::assertSame(
            2,
            $register[
                'resources'
            ][1]['maximum']
        );

        self::assertSame(
            1,
            $register[
                'resources'
            ][1]['remaining']
        );

        self::assertSame(
            1,
            $register[
                'resources'
            ][1]['expended']
        );
    }

    public function testShortAndLongRestBoundariesRemainDistinct(): void
    {
        $service =
            new FighterBattleReserveService();

        $spent = ActiveClassResourceState::fromArray([
            'second-wind' => 1,
            'action-surge' => 1,
            'indomitable' => 1,
        ]);

        $short = $service->shortRest(
            $this->fighter(17),
            $spent
        );

        self::assertSame(
            0,
            $short->expended(
                'second-wind'
            )
        );

        self::assertSame(
            0,
            $short->expended(
                'action-surge'
            )
        );

        self::assertSame(
            1,
            $short->expended(
                'indomitable'
            )
        );

        $long = $service->longRest(
            $this->fighter(17),
            $spent
        );

        self::assertSame(
            [],
            $long->toArray()
        );
    }

    public function testSecondWindActionAlwaysUsesCertifiedFighterLevel(): void
    {
        $presenter =
            new FighterMartialActionPresenter();

        foreach (
            [1, 4, 9, 17, 20]
            as $level
        ) {
            $action = $presenter
                ->present(
                    $this->fighter($level)
                )['resources'][
                    'second-wind'
                ];

            self::assertSame(
                '1d10',
                $action['roll']['formula']
            );

            self::assertSame(
                $level,
                $action['roll']['modifier']
            );
        }
    }

    public function testActionSurgeNeverInventsADiceRoll(): void
    {
        $action = (
            new FighterMartialActionPresenter()
        )->present(
            $this->fighter(17)
        )['resources']['action-surge'];

        self::assertNull(
            $action['roll']
        );

        self::assertSame(
            'Use Action Surge',
            $action['button_label']
        );
    }

    public function testIndomitableKeepsSixRealSavingThrowRerolls(): void
    {
        $action = (
            new FighterMartialActionPresenter()
        )->present(
            $this->fighter(9)
        )['resources']['indomitable'];

        self::assertCount(
            6,
            $action['save_rerolls']
        );

        self::assertSame(
            [
                'strength',
                'dexterity',
                'constitution',
                'intelligence',
                'wisdom',
                'charisma',
            ],
            array_column(
                $action['save_rerolls'],
                'ability'
            )
        );
    }

    public function testCertifiedPathGiftsOnlyAppearWhenPersisted(): void
    {
        $fighter = $this->fighter(
            10,
            'the-carver',
            [
                'carvers-flourish',
                'engraved-guard',
            ]
        );

        $register = (
            new FighterMartialRegisterPresenter()
        )->present($fighter);

        self::assertSame(
            [
                'carvers-flourish',
                'engraved-guard',
            ],
            array_column(
                $register[
                    'path'
                ]['gifts'],
                'key'
            )
        );

        self::assertNotContains(
            'signature-cut',
            array_column(
                $register[
                    'path'
                ]['gifts'],
                'key'
            )
        );
    }

    public function testFighterSystemsRemainIsolatedFromWizard(): void
    {
        $wizard = $this->character(
            'wizard',
            9
        );

        self::assertFalse(
            (
                new FighterMartialRegisterPresenter()
            )->present(
                $wizard
            )['supported']
        );

        self::assertFalse(
            (
                new FighterMartialActionPresenter()
            )->present(
                $wizard
            )['supported']
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new FighterBattleReserveService()
        )->maximum(
            $wizard,
            'action-surge'
        );
    }

    public function testFighterResourceCommandsRemainPostNonceProtected(): void
    {
        $routes = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/'
            . 'Routes.php'
        );

        $provider = file_get_contents(
            $this->root()
            . '/app/Providers/'
            . 'FrontendServiceProvider.php'
        );

        self::assertIsString($routes);
        self::assertIsString($provider);

        self::assertStringContainsString(
            "'/characters/{id}/resources/spend'",
            $routes
        );

        self::assertStringContainsString(
            "'/characters/{id}/resources/refresh'",
            $routes
        );

        self::assertStringContainsString(
            'gmrc_character_resources_',
            $provider
        );
    }

    public function testMartialRegisterKeepsAccessibleResponsiveFinalSurface(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/'
            . 'show.php'
        );

        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertIsString($view);
        self::assertIsString($css);

        self::assertStringContainsString(
            'aria-labelledby="gmrc-martial-register-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-battle-reserves-rest-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-label="Indomitable saving throw rerolls"',
            $view
        );

        self::assertStringContainsString(
            '@media (max-width: 560px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testMartialRegisterHasNoDuplicateLocalIndomitableAuthority(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Progression/'
            . 'Martial/Services/'
            . 'FighterMartialRegisterPresenter.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'FighterBattleReserveService',
            $source
        );

        self::assertStringNotContainsString(
            'private function indomitableUses',
            $source
        );
    }

    /**
     * @return array<int,string>
     */
    private function paths(): array
    {
        return [
            'discontinued-lineage',
            'butcher',
            'the-carver',
            'cutlery-knight',
            'the-vineblade',
            'shelf-sentinel',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function fighter(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Seal Fighter'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'fighter'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
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
                'Isolation Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average()
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

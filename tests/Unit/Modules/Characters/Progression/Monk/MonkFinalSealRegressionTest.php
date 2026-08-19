<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\MonkProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplinePolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkDisciplineRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkMartialTechniquePresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;

final class MonkFinalSealRegressionTest extends MonkTestCase
{
    public function testMonkRemainsSpecialistWithoutBaselineSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('monk')
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

    public function testCoreMonkMilestonesRemainStable(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        $expected = [
            2 => [
                'discipline',
                'unarmoured-movement',
            ],
            3 => ['deflect-missiles'],
            4 => ['slow-fall'],
            5 => [
                'extra-attack',
                'stunning-strike',
            ],
            6 => ['disciplined-strikes'],
            7 => [
                'evasion',
                'stillness-of-mind',
            ],
            10 => ['purity-of-body'],
            13 => ['tongue-of-sun-and-moon'],
            14 => ['diamond-soul'],
            15 => ['timeless-body'],
            18 => ['empty-body'],
            20 => ['perfect-self'],
        ];

        foreach ($expected as $level => $keys) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthAndWayGiftDelegationsRemainSeparated(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }

        foreach ([3, 6, 11, 17] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testAllSixWaysKeepFourGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->ways() as $way) {
            self::assertTrue(
                $catalogue->supports($way)
            );

            self::assertSame(
                [3, 6, 11, 17],
                array_column(
                    $catalogue->all($way),
                    'level'
                )
            );
        }
    }

    public function testAllSixWaysKeepDecisionGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('monk')
        );

        self::assertCount(6, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                '',
                trim((string) $candidate['identity'])
            );

            self::assertNotSame(
                '',
                trim((string) $candidate['playstyle'])
            );

            self::assertNotSame(
                '',
                trim((string) $candidate['best_for'])
            );

            self::assertCount(
                4,
                $candidate['gift_preview']
            );
        }
    }

    public function testDisciplinePolicyRemainsSingleMaximumAndDcAuthority(): void
    {
        $policy = new MonkDisciplinePolicy();
        $monk = $this->monk(5);

        self::assertSame(
            5,
            $policy->maximum($monk)
        );

        self::assertSame(
            8
            + $monk->proficiencyBonus()->value()
            + $monk->abilityScores()->wisdom()->modifier(),
            $policy->saveDc($monk)
        );

        self::assertSame(
            10,
            $policy->movementBonusFeet(
                $monk
            )
        );
    }

    public function testDisciplineReservePersistsExpenditureNotMaximum(): void
    {
        $monk = $this->monk(5);
        $service = new MonkDisciplineReserveService();

        $state = $service->spendTechnique(
            $monk,
            ActiveClassResourceState::fresh(),
            'flurry-of-blows'
        );

        self::assertSame(
            1,
            $state->expended('discipline')
        );

        self::assertSame(
            4,
            $service->remaining(
                $monk,
                $state
            )
        );

        self::assertSame(
            5,
            $service->remaining(
                $this->monk(6),
                $state
            )
        );
    }

    public function testBothRestTypesRestoreSharedDisciplineReserve(): void
    {
        $monk = $this->monk(5);
        $service = new MonkDisciplineReserveService();

        $spent = $service->spendTechnique(
            $monk,
            ActiveClassResourceState::fresh(),
            'patient-defense'
        );

        self::assertSame(
            5,
            $service->remaining(
                $monk,
                $service->shortRest(
                    $monk,
                    $spent
                )
            )
        );

        self::assertSame(
            5,
            $service->remaining(
                $monk,
                $service->longRest(
                    $monk,
                    $spent
                )
            )
        );
    }

    public function testCoreTechniqueCostsAndUnlocksRemainStable(): void
    {
        $levelTwo = (
            new MonkMartialTechniquePresenter()
        )->present(
            $this->monk(2)
        );

        foreach (
            [
                'flurry-of-blows',
                'patient-defense',
                'step-of-the-wind',
            ]
            as $key
        ) {
            $technique = $this->technique(
                $levelTwo,
                $key
            );

            self::assertTrue(
                $technique['unlocked']
            );

            self::assertSame(
                1,
                $technique['cost']
            );
        }

        self::assertFalse(
            $this->technique(
                $levelTwo,
                'stunning-strike'
            )['unlocked']
        );

        self::assertTrue(
            $this->technique(
                (
                    new MonkMartialTechniquePresenter()
                )->present(
                    $this->monk(5)
                ),
                'stunning-strike'
            )['unlocked']
        );
    }

    public function testDeflectMissilesKeepsCertifiedDiceworksReduction(): void
    {
        $monk = $this->monk(4);

        $deflect = $this->technique(
            (
                new MonkMartialTechniquePresenter()
            )->present($monk),
            'deflect-missiles'
        );

        self::assertSame(
            '1d10',
            $deflect['roll']['formula']
        );

        self::assertSame(
            $monk
                ->abilityScores()
                ->dexterity()
                ->modifier()
            + 4,
            $deflect['roll']['modifier']
        );

        self::assertSame(
            'damage reduction',
            $deflect['roll']['result_suffix']
        );

        self::assertSame(
            1,
            $deflect['follow_up']['cost']
        );
    }

    public function testSlowFallRemainsFreeAndLevelScaled(): void
    {
        $slowFall = $this->technique(
            (
                new MonkMartialTechniquePresenter()
            )->present(
                $this->monk(4)
            ),
            'slow-fall'
        );

        self::assertSame(
            0,
            $slowFall['cost']
        );

        self::assertSame(
            'Reduce 20',
            $slowFall['badge']
        );

        self::assertNull(
            $slowFall['roll']
        );
    }

    public function testStunningStrikeUsesRealDisciplineSaveDc(): void
    {
        $monk = $this->monk(5);

        $state = (
            new MonkMartialTechniquePresenter()
        )->present($monk);

        $stunning = $this->technique(
            $state,
            'stunning-strike'
        );

        self::assertSame(
            'DC ' . $state['save_dc'],
            $stunning['badge']
        );

        self::assertStringContainsString(
            'does not roll for the target',
            $stunning['detail']
        );
    }

    public function testReserveServiceRejectsForeignCallingAndLockedTechnique(): void
    {
        $service = new MonkDisciplineReserveService();

        try {
            $service->spendTechnique(
                $this->character('fighter', 5),
                ActiveClassResourceState::fresh(),
                'flurry-of-blows'
            );

            self::fail(
                'Fighter should not spend Monk Discipline.'
            );
        } catch (InvalidArgumentException) {
            self::assertTrue(true);
        }

        $this->expectException(
            InvalidArgumentException::class
        );

        $service->spendTechnique(
            $this->monk(4),
            ActiveClassResourceState::fresh(),
            'stunning-strike'
        );
    }

    public function testDisciplineFormsRemainOnApplicationPostBridge(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $view
        );

        self::assertStringContainsString(
            '/discipline/spend',
            $view
        );

        self::assertStringContainsString(
            '/discipline/rest',
            $view
        );

        self::assertStringContainsString(
            "'gmrc_character_discipline_'",
            $view
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'discipline/(?:spend|rest)',
            $provider
        );
    }

    public function testLedgerKeepsNamedTechniquesInsteadOfGenericSpendOnly(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Martial Techniques',
            $view
        );

        self::assertStringContainsString(
            'data-discipline-technique=',
            $view
        );

        self::assertStringContainsString(
            'Flurry of Blows',
            $view
        );

        self::assertStringContainsString(
            'Stunning Strike',
            $view
        );

        self::assertStringContainsString(
            'Roll Reduction',
            $view
        );
    }

    public function testMonkSurfaceKeepsAccessibilityAndResponsiveBoundaries(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-discipline-register-title"',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-monk-techniques-title"',
            $view
        );

        $css = $this->source(
            'assets/css/modules/characters/arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-monk-techniques',
            $css
        );

        self::assertStringContainsString(
            ':focus-visible',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 840px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testDisciplineRegisterCarriesReserveAndTechniqueStateTogether(): void
    {
        $monk = $this->monk(4);
        $resources =
            ActiveClassResourceState::fromArray([
                'discipline' => 1,
            ]);

        $register = (
            new MonkDisciplineRegisterPresenter()
        )->present(
            $monk,
            $resources
        );

        self::assertSame(
            4,
            $register['discipline']['maximum']
        );

        self::assertSame(
            3,
            $register['discipline']['remaining']
        );

        self::assertSame(
            3,
            $register[
                'martial_techniques'
            ]['remaining_discipline']
        );
    }

    /** @return array<int,string> */
    private function ways(): array
    {
        return [
            'way-of-the-spun-cloud',
            'way-of-the-neon-crunch',
            'way-of-the-vacuum-seal',
            'way-of-the-simmering-soul',
            'way-of-the-whirling-utensil',
            'way-of-the-spongecake-soul',
        ];
    }

    private function technique(
        array $state,
        string $key
    ): array {
        foreach (
            $state['techniques']
            as $technique
        ) {
            if (
                ($technique['key'] ?? '')
                === $key
            ) {
                return $technique;
            }
        }

        self::fail(
            'Expected certified Monk technique was not present.'
        );
    }
}

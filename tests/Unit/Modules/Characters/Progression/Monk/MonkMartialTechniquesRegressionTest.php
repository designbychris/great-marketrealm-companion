<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\MonkDisciplineReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Discipline\Services\MonkMartialTechniquePresenter;
use InvalidArgumentException;

final class MonkMartialTechniquesRegressionTest extends MonkTestCase
{
    public function testLevelOneHasNoUsableDisciplineTechniques(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(1));

        self::assertTrue($state['supported']);
        self::assertSame(0, $state['remaining_discipline']);

        foreach ($state['techniques'] as $technique) {
            self::assertFalse($technique['unlocked']);
        }
    }

    public function testLevelTwoUnlocksCoreDisciplineTechniques(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(2));

        $unlocked = array_values(
            array_filter(
                $state['techniques'],
                static fn (array $technique): bool =>
                    ! empty($technique['unlocked'])
            )
        );

        self::assertSame(
            [
                'flurry-of-blows',
                'patient-defense',
                'step-of-the-wind',
            ],
            array_column($unlocked, 'key')
        );
    }

    public function testCoreLevelTwoTechniquesCostOneDiscipline(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(2));

        foreach (array_slice($state['techniques'], 0, 3) as $technique) {
            self::assertSame(1, $technique['cost']);
            self::assertSame('discipline-spend', $technique['kind']);
        }
    }

    public function testTechniqueAvailabilityTracksRemainingReserve(): void
    {
        $state = ActiveClassResourceState::fromArray([
            'discipline' => 2,
        ]);

        $presented = (new MonkMartialTechniquePresenter())
            ->present($this->monk(2), $state);

        self::assertSame(0, $presented['remaining_discipline']);

        foreach (array_slice($presented['techniques'], 0, 3) as $technique) {
            self::assertFalse($technique['available']);
        }
    }

    public function testSpendingNamedTechniqueConsumesSharedReserve(): void
    {
        $monk = $this->monk(4);
        $service = new MonkDisciplineReserveService();

        $state = $service->spendTechnique(
            $monk,
            ActiveClassResourceState::fresh(),
            'flurry-of-blows'
        );

        self::assertSame(1, $state->expended('discipline'));
        self::assertSame(3, $service->remaining($monk, $state));
    }

    public function testTechniqueCannotBeUsedBeforeItsLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MonkDisciplineReserveService())
            ->spendTechnique(
                $this->monk(4),
                ActiveClassResourceState::fresh(),
                'stunning-strike'
            );
    }

    public function testUnknownTechniqueIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MonkDisciplineReserveService())
            ->spendTechnique(
                $this->monk(5),
                ActiveClassResourceState::fresh(),
                'mystery-punch'
            );
    }

    public function testDeflectMissilesUnlocksAtThreeWithRealReductionRoll(): void
    {
        $monk = $this->monk(3);
        $state = (new MonkMartialTechniquePresenter())
            ->present($monk);

        $deflect = $this->technique(
            $state,
            'deflect-missiles'
        );

        self::assertTrue($deflect['unlocked']);
        self::assertSame('1d10', $deflect['roll']['formula']);
        self::assertSame(
            $monk->abilityScores()->dexterity()->modifier()
                + $monk->level()->value(),
            $deflect['roll']['modifier']
        );
        self::assertSame(
            'damage reduction',
            $deflect['roll']['result_suffix']
        );
    }

    public function testReturningDeflectedMissileUsesDisciplineButKeepsQualificationAtTable(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(3));

        $followUp = $this->technique(
            $state,
            'deflect-missiles'
        )['follow_up'];

        self::assertSame(1, $followUp['cost']);
        self::assertSame(
            'return-deflected-missile',
            $followUp['key']
        );
        self::assertStringContainsString(
            'table confirms',
            $followUp['detail']
        );
    }

    public function testStunningStrikeUnlocksAtFiveAndShowsRealSaveDc(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(5));

        $stunning = $this->technique(
            $state,
            'stunning-strike'
        );

        self::assertTrue($stunning['unlocked']);
        self::assertSame(1, $stunning['cost']);
        self::assertStringContainsString(
            'DC ' . $state['save_dc'],
            $stunning['badge']
        );
        self::assertStringContainsString(
            'does not roll for the target',
            $stunning['detail']
        );
    }

    public function testSlowFallUsesCertifiedLevelScalingWithoutDiscipline(): void
    {
        $state = (new MonkMartialTechniquePresenter())
            ->present($this->monk(4));

        $slowFall = $this->technique(
            $state,
            'slow-fall'
        );

        self::assertTrue($slowFall['unlocked']);
        self::assertSame(0, $slowFall['cost']);
        self::assertSame('Reduce 20', $slowFall['badge']);
        self::assertNull($slowFall['roll']);
    }

    public function testLedgerUsesRealTechniqueSpendsAndDiceworks(): void
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
            'data-discipline-spend',
            $view
        );
        self::assertStringContainsString(
            'Roll Reduction',
            $view
        );
        self::assertStringContainsString(
            'gmrc-guild-roll-trigger',
            $view
        );
    }

    public function testControllerDelegatesNamedTechniqueToReserveService(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertStringContainsString(
            "\$_POST['technique']",
            $controller
        );
        self::assertStringContainsString(
            'spendTechnique(',
            $controller
        );
    }

    public function testTechniquePresentationRemainsResponsiveAndAccessible(): void
    {
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

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-monk-techniques-title"',
            $view
        );
    }

    private function technique(
        array $state,
        string $key
    ): array {
        foreach ($state['techniques'] as $technique) {
            if (($technique['key'] ?? '') === $key) {
                return $technique;
            }
        }

        self::fail('Expected Monk technique was not present.');
    }
}

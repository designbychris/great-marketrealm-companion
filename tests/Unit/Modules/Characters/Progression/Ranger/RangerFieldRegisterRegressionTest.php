<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RangerFieldRegisterRegressionTest extends TestCase
{
    public function testNonRangerIsUnsupported(): void
    {
        self::assertFalse(
            (new RangerFieldRegisterPresenter())
                ->present(
                    $this->character('fighter', 5)
                )['supported']
        );
    }

    public function testFavouredMarkStageImprovesAtSixAndFourteen(): void
    {
        $policy = new RangerFieldPolicy();

        self::assertSame(
            1,
            $policy->favouredMarkStage(
                $this->ranger(1)
            )
        );

        self::assertSame(
            2,
            $policy->favouredMarkStage(
                $this->ranger(6)
            )
        );

        self::assertSame(
            3,
            $policy->favouredMarkStage(
                $this->ranger(14)
            )
        );
    }

    public function testExtraAttackBeginsAtLevelFive(): void
    {
        $policy = new RangerFieldPolicy();

        self::assertFalse(
            $policy->extraAttackUnlocked(
                $this->ranger(4)
            )
        );

        self::assertTrue(
            $policy->extraAttackUnlocked(
                $this->ranger(5)
            )
        );
    }

    public function testSpellSaveDcAndAttackUseWisdom(): void
    {
        $ranger = $this->ranger(5);
        $policy = new RangerFieldPolicy();

        self::assertSame(
            8
            + $ranger
                ->proficiencyBonus()
                ->value()
            + $ranger
                ->abilityScores()
                ->wisdom()
                ->modifier(),
            $policy->spellSaveDc($ranger)
        );

        self::assertSame(
            $ranger
                ->proficiencyBonus()
                ->value()
            + $ranger
                ->abilityScores()
                ->wisdom()
                ->modifier(),
            $policy->spellAttackBonus($ranger)
        );
    }

    public function testLevelOneRegisterKeepsSpellcastingLocked(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(1)
        );

        self::assertFalse(
            $register[
                'spellcasting'
            ]['unlocked']
        );

        self::assertSame(
            [],
            $register[
                'spellcasting'
            ]['slots']
        );

        self::assertSame(
            2,
            $register[
                'next_milestone'
            ]['level']
        );
    }

    public function testLevelFiveRegisterShowsKnownSpellHalfCasting(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(5)
        );

        self::assertTrue(
            $register[
                'spellcasting'
            ]['unlocked']
        );

        self::assertSame(
            'known-spells',
            $register[
                'spellcasting'
            ]['model']
        );

        self::assertSame(
            4,
            $register[
                'spellcasting'
            ]['spells_known']
        );

        self::assertSame(
            2,
            $register[
                'spellcasting'
            ]['maximum_spell_level']
        );

        self::assertSame(
            2,
            $register[
                'extra_attack'
            ]['attacks']
        );
    }

    public function testRegisterCarriesPersistentSharedSpellSlotState(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(5),
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        );

        $levelOne = array_values(
            array_filter(
                $register[
                    'spellcasting'
                ]['slots'],
                static fn (
                    array $slot
                ): bool =>
                    (int) (
                        $slot['level']
                        ?? 0
                    ) === 1
            )
        )[0];

        self::assertSame(
            3,
            $levelOne['remaining']
        );

        self::assertSame(
            1,
            $levelOne['expended']
        );
    }

    public function testRegisterExposesEightCertifiedRangerPathCandidates(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(5)
        );

        self::assertTrue(
            $register['path']['registered']
        );

        self::assertSame(
            8,
            $register['path']['candidate_count']
        );

        self::assertCount(
            8,
            $register['path']['candidates']
        );

        self::assertSame(
            'Eight Ranger Paths available',
            $register['path']['status']
        );
    }

    public function testRegisterShowsNextFieldMilestone(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(5)
        );

        self::assertSame(
            6,
            $register[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            'Favoured Mark Improvement',
            $register[
                'next_milestone'
            ]['label']
        );
    }

    public function testLevelTwentyHasNoNextMilestone(): void
    {
        self::assertNull(
            (
                new RangerFieldRegisterPresenter()
            )->present(
                $this->ranger(20)
            )['next_milestone']
        );
    }

    public function testControllerSuppliesFieldRegisterToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'RangerFieldRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'fieldRegister' => \$fieldRegister",
            $controller
        );
    }

    public function testLedgerRendersRangerFieldRegister(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Ranger’s Field Register',
            $view
        );

        self::assertStringContainsString(
            'data-field-register',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-field-register-title"',
            $view
        );

        self::assertStringContainsString(
            'Ranger path status',
            $view
        );

        self::assertStringContainsString(
            'Eight certified Ranger Paths are now available',
            $view
        );
    }

    public function testFieldRegisterRemainsReadOnlyInThisSlice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-favoured-mark-spend',
            $view
        );

        self::assertStringNotContainsString(
            'data-ranger-path-choice',
            $view
        );
    }

    public function testFieldRegisterPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-field-register',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 620px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testPolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerFieldPolicy()
        )->favouredMarkStage(
            $this->character(
                'wizard',
                5
            )
        );
    }

    private function ranger(
        int $level
    ): Character {
        return $this->character(
            'ranger',
            $level
        );
    }

    private function character(
        string $class,
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Field Register Tester'
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

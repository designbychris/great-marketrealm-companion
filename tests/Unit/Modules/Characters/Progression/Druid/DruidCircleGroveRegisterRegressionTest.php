<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleGroveRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidGrovePolicy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DruidCircleGroveRegisterRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new DruidCircleGroveRegisterPresenter()
            )->present(
                $this->character(
                    'wizard',
                    1,
                    ''
                )
            )['supported']
        );
    }

    public function testLevelOneDruidAlreadyShowsPreparedSpellcasting(): void
    {
        $register = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertTrue(
            $register[
                'spellcasting'
            ]['unlocked']
        );

        self::assertSame(
            'prepared-spells',
            $register[
                'spellcasting'
            ]['model']
        );

        self::assertSame(
            1,
            $register[
                'spellcasting'
            ]['maximum_spell_level']
        );

        self::assertSame(
            2,
            $register[
                'spellcasting'
            ]['cantrips_known']
        );
    }

    public function testLevelOneDruidHasFirstCircleSpellSlots(): void
    {
        $slots = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        )['spellcasting']['slots'];

        self::assertNotSame(
            [],
            $slots
        );

        self::assertSame(
            1,
            $slots[0]['level']
        );

        self::assertSame(
            2,
            $slots[0]['total']
        );

        self::assertSame(
            2,
            $slots[0]['remaining']
        );
    }

    public function testLevelOneShowsWildShapeAndCircleAsUpcoming(): void
    {
        $register = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        );

        self::assertFalse(
            $register[
                'wild_shape'
            ]['unlocked']
        );

        self::assertSame(
            0,
            $register[
                'wild_shape'
            ]['stage']
        );

        self::assertSame(
            2,
            $register[
                'wild_shape'
            ]['next_improvement_level']
        );

        self::assertFalse(
            $register[
                'circle'
            ]['chosen']
        );

        self::assertSame(
            2,
            $register[
                'circle'
            ]['selection_level']
        );
    }

    public function testLevelOneNextMilestoneIsWildShapeAndCircle(): void
    {
        $milestone = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        )['next_milestone'];

        self::assertSame(
            2,
            $milestone['level']
        );

        self::assertSame(
            'Wild Shape & Druid Circle',
            $milestone['label']
        );
    }

    public function testSixExistingCirclesAreVisibleInRegister(): void
    {
        $circle = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(1)
        )['circle'];

        self::assertSame(
            6,
            $circle['candidate_count']
        );

        self::assertSame(
            [
                'circle-of-eating-fresh',
                'circle-of-the-groveflame',
                'circle-of-the-deep-soil',
                'circle-of-the-compost',
                'circle-of-curdle',
                'circle-of-the-churn',
            ],
            array_column(
                $circle['candidates'],
                'key'
            )
        );
    }

    public function testChosenCircleUsesCatalogueLabel(): void
    {
        $circle = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-compost'
            )
        )['circle'];

        self::assertTrue(
            $circle['chosen']
        );

        self::assertSame(
            'Circle of the Compost',
            $circle['label']
        );
    }

    public function testCircleGroveRegisterReportsCertifiedCircleGifts(): void
    {
        $circle = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(
                2,
                'circle-of-the-compost'
            )
        )['circle'];

        self::assertSame(
            4,
            $circle['gift_count']
        );

        self::assertSame(
            'Circle Gifts certified',
            $circle['gift_status']
        );
    }

    public function testPreparedSpellMaximumUsesLevelPlusWisdomModifier(): void
    {
        $policy =
            new DruidGrovePolicy();

        self::assertSame(
            6,
            $policy->preparedSpellMaximum(
                $this->druid(
                    3,
                    '',
                    16
                )
            )
        );
    }

    public function testPreparedSpellMaximumNeverFallsBelowOne(): void
    {
        self::assertSame(
            1,
            (
                new DruidGrovePolicy()
            )->preparedSpellMaximum(
                $this->druid(
                    1,
                    '',
                    6
                )
            )
        );
    }

    public function testSpellSaveAndAttackUseWisdom(): void
    {
        $druid = $this->druid(
            5,
            '',
            16
        );

        $policy =
            new DruidGrovePolicy();

        $wisdom = $druid
            ->abilityScores()
            ->wisdom()
            ->modifier();

        $proficiency = $druid
            ->proficiencyBonus()
            ->value();

        self::assertSame(
            8 + $proficiency + $wisdom,
            $policy->spellSaveDc($druid)
        );

        self::assertSame(
            $proficiency + $wisdom,
            $policy->spellAttackBonus($druid)
        );
    }

    public function testWildShapeStagesAdvanceAtTwoFourEight(): void
    {
        $policy =
            new DruidGrovePolicy();

        self::assertSame(
            1,
            $policy->wildShapeStage(
                $this->druid(2)
            )
        );

        self::assertSame(
            2,
            $policy->wildShapeStage(
                $this->druid(4)
            )
        );

        self::assertSame(
            3,
            $policy->wildShapeStage(
                $this->druid(8)
            )
        );
    }

    public function testRegisterCarriesSharedSpellSlotExpenditure(): void
    {
        $slots = (
            new DruidCircleGroveRegisterPresenter()
        )->present(
            $this->druid(3),
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        )['spellcasting']['slots'];

        $levelOne = array_values(
            array_filter(
                $slots,
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
            1,
            $levelOne['expended']
        );

        self::assertSame(
            $levelOne['total'] - 1,
            $levelOne['remaining']
        );
    }

    public function testControllerSuppliesGroveRegisterToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'DruidCircleGroveRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'groveRegister' => \$groveRegister",
            $controller
        );
    }

    public function testLedgerRendersCircleGroveRegisterAndLevelOneGuidance(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Druid’s Circle Grove Register',
            $view
        );

        self::assertStringContainsString(
            'data-grove-register',
            $view
        );

        self::assertStringContainsString(
            'At Level 1, spellcasting is already alive in the',
            $view
        );

        self::assertStringContainsString(
            'Wild Shape and Circle certification awaken',
            $view
        );
    }

    public function testGroveRegisterRemainsReadOnlyInThisSlice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-wild-shape-spend',
            $view
        );

        self::assertStringNotContainsString(
            'data-druid-circle-art',
            $view
        );
    }

    public function testGroveRegisterPresentationIsResponsiveAndAccessible(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-grove-register',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 460px)',
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

    public function testGrovePolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new DruidGrovePolicy()
        )->wildShapeStage(
            $this->character(
                'wizard',
                5,
                ''
            )
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
                'Circle Grove Tester'
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

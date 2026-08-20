<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Bard;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardPerformancePolicy;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BardCollegeRegisterRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new BardCollegeRegisterPresenter()
            )->present(
                $this->character('wizard', 1, '')
            )['supported']
        );
    }

    public function testLevelOneBardAlreadyHasSpellcastingAndInspiration(): void
    {
        $register = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(1)
        );

        self::assertTrue($register['supported']);
        self::assertSame('d6', $register['inspiration']['die']);
        self::assertSame(4, $register['spellcasting']['spells_known']);
        self::assertSame(2, $register['spellcasting']['cantrips_known']);
    }

    public function testLevelOneCollegeIsCorrectlyUpcoming(): void
    {
        $college = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(1)
        )['college'];

        self::assertSame(3, $college['selection_level']);
        self::assertFalse($college['available']);
        self::assertFalse($college['chosen']);
        self::assertSame('College not yet chosen', $college['label']);
    }

    public function testSevenExistingCollegesRemainVisible(): void
    {
        $college = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(1)
        )['college'];

        self::assertSame(7, $college['candidate_count']);
        self::assertCount(7, $college['candidates']);
    }

    public function testChosenCollegeUsesCatalogueLabel(): void
    {
        $college = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(
                3,
                'college-of-confection'
            )
        )['college'];

        self::assertTrue($college['available']);
        self::assertTrue($college['chosen']);
        self::assertSame('College of Confection', $college['label']);
    }

    public function testCollegeGiftBoundaryRemainsVisibleUntilDedicatedPhase(): void
    {
        $college = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(
                3,
                'college-of-confection'
            )
        )['college'];

        self::assertSame(0, $college['gift_count']);
        self::assertSame(
            'College Gifts await their dedicated phase',
            $college['gift_status']
        );
    }

    public function testInspirationDieScalesAtFiveTenFifteen(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertSame('d6', $policy->inspirationDie($this->bard(1)));
        self::assertSame('d8', $policy->inspirationDie($this->bard(5)));
        self::assertSame('d10', $policy->inspirationDie($this->bard(10)));
        self::assertSame('d12', $policy->inspirationDie($this->bard(15)));
    }

    public function testInspirationUsesCurrentCharismaModifierWithMinimumOne(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertSame(
            3,
            $policy->inspirationMaximum(
                $this->bard(1, '', 16)
            )
        );

        self::assertSame(
            1,
            $policy->inspirationMaximum(
                $this->bard(1, '', 8)
            )
        );
    }

    public function testFontOfInspirationChangesRefreshAtFive(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertSame(
            'long-rest',
            $policy->inspirationRefresh(
                $this->bard(4)
            )
        );

        self::assertSame(
            'short-or-long-rest',
            $policy->inspirationRefresh(
                $this->bard(5)
            )
        );
    }

    public function testSongOfRestProgressionIsD6D8D10D12(): void
    {
        $policy = new BardPerformancePolicy();

        self::assertNull(
            $policy->songOfRestDie(
                $this->bard(1)
            )
        );

        self::assertSame('d6', $policy->songOfRestDie($this->bard(2)));
        self::assertSame('d8', $policy->songOfRestDie($this->bard(9)));
        self::assertSame('d10', $policy->songOfRestDie($this->bard(13)));
        self::assertSame('d12', $policy->songOfRestDie($this->bard(17)));
    }

    public function testSpellSaveAndAttackUseCharisma(): void
    {
        $bard = $this->bard(5, '', 16);
        $policy = new BardPerformancePolicy();

        $charisma = $bard
            ->abilityScores()
            ->charisma()
            ->modifier();

        $proficiency = $bard
            ->proficiencyBonus()
            ->value();

        self::assertSame(
            8 + $proficiency + $charisma,
            $policy->spellSaveDc($bard)
        );

        self::assertSame(
            $proficiency + $charisma,
            $policy->spellAttackBonus($bard)
        );
    }

    public function testLevelOneBardHasFirstCircleSharedSpellSlots(): void
    {
        $slots = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(1)
        )['spellcasting']['slots'];

        self::assertNotSame([], $slots);
        self::assertSame(1, $slots[0]['level']);
        self::assertSame(2, $slots[0]['total']);
        self::assertSame(2, $slots[0]['remaining']);
    }

    public function testRegisterCarriesSharedSpellSlotExpenditure(): void
    {
        $slots = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(3),
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        )['spellcasting']['slots'];

        $first = array_values(
            array_filter(
                $slots,
                static fn (array $slot): bool =>
                    (int) ($slot['level'] ?? 0) === 1
            )
        )[0];

        self::assertSame(1, $first['expended']);
        self::assertSame(
            $first['total'] - 1,
            $first['remaining']
        );
    }

    public function testMagicalSecretsPairCountScalesAtTenFourteenEighteen(): void
    {
        $presenter = new BardCollegeRegisterPresenter();

        self::assertSame(
            0,
            $presenter->present(
                $this->bard(9)
            )['magical_secrets']['pairs']
        );

        self::assertSame(
            1,
            $presenter->present(
                $this->bard(10)
            )['magical_secrets']['pairs']
        );

        self::assertSame(
            2,
            $presenter->present(
                $this->bard(14)
            )['magical_secrets']['pairs']
        );

        self::assertSame(
            3,
            $presenter->present(
                $this->bard(18)
            )['magical_secrets']['pairs']
        );
    }

    public function testLevelOneNextMilestoneIsLevelTwoPerformance(): void
    {
        $milestone = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(1)
        )['next_milestone'];

        self::assertSame(2, $milestone['level']);
        self::assertSame(
            'Jack of All Trades & Song of Rest',
            $milestone['label']
        );
    }

    public function testLevelTwoNextMilestoneIsCollegeSelection(): void
    {
        $milestone = (
            new BardCollegeRegisterPresenter()
        )->present(
            $this->bard(2)
        )['next_milestone'];

        self::assertSame(3, $milestone['level']);
        self::assertSame(
            'Bard College & Expertise',
            $milestone['label']
        );
    }

    public function testControllerSuppliesCollegeRegisterToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'BardCollegeRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'collegeRegister' => \$collegeRegister",
            $controller
        );
    }

    public function testLedgerRendersCollegeRegisterAndEarlyLevelGuidance(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Bard’s College Register',
            $view
        );

        self::assertStringContainsString(
            'data-college-register',
            $view
        );

        self::assertStringContainsString(
            'College selection opens at Level 3',
            $view
        );
    }

    public function testCollegeRegisterIsReadOnlyAndResponsive(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-bard-inspiration-spend',
            $view
        );

        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-college-register',
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

    public function testPerformancePolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new BardPerformancePolicy()
        )->inspirationDie(
            $this->character('wizard', 5, '')
        );
    }

    private function bard(
        int $level,
        string $college = '',
        int $charisma = 10
    ): Character {
        return $this->character(
            'bard',
            $level,
            $college,
            $charisma
        );
    }

    private function character(
        string $class,
        int $level,
        string $college,
        int $charisma = 10
    ): Character {
        $scores = AbilityScores::average()
            ->withCharisma(
                AbilityScore::fromInt(
                    $charisma
                )
            );

        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'College Register Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            $scores,
            callingPath:
                CallingPath::fromString(
                    $college
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

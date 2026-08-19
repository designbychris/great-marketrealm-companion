<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Sorcerer;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SorcererOriginRegisterRegressionTest extends TestCase
{
    public function testNonSorcererIsUnsupported(): void
    {
        self::assertFalse(
            (new SorcererOriginRegisterPresenter())
                ->present(
                    $this->character('fighter', 3)
                )['supported']
        );
    }

    public function testSorceryPointMaximumTracksSorcererLevel(): void
    {
        $policy = new SorcererOriginPolicy();

        self::assertSame(
            0,
            $policy->sorceryPointMaximum(
                $this->sorcerer(1)
            )
        );

        self::assertSame(
            2,
            $policy->sorceryPointMaximum(
                $this->sorcerer(2)
            )
        );

        self::assertSame(
            10,
            $policy->sorceryPointMaximum(
                $this->sorcerer(10)
            )
        );

        self::assertSame(
            20,
            $policy->sorceryPointMaximum(
                $this->sorcerer(20)
            )
        );
    }

    public function testMetamagicKnownCadenceIsLevelAware(): void
    {
        $policy = new SorcererOriginPolicy();

        self::assertSame(
            0,
            $policy->metamagicKnown(
                $this->sorcerer(2)
            )
        );

        self::assertSame(
            2,
            $policy->metamagicKnown(
                $this->sorcerer(3)
            )
        );

        self::assertSame(
            3,
            $policy->metamagicKnown(
                $this->sorcerer(10)
            )
        );

        self::assertSame(
            4,
            $policy->metamagicKnown(
                $this->sorcerer(17)
            )
        );
    }

    public function testSpellSaveDcAndAttackUseCharisma(): void
    {
        $sorcerer = $this->sorcerer(5);
        $policy = new SorcererOriginPolicy();

        self::assertSame(
            8
            + $sorcerer
                ->proficiencyBonus()
                ->value()
            + $sorcerer
                ->abilityScores()
                ->charisma()
                ->modifier(),
            $policy->spellSaveDc(
                $sorcerer
            )
        );

        self::assertSame(
            $sorcerer
                ->proficiencyBonus()
                ->value()
            + $sorcerer
                ->abilityScores()
                ->charisma()
                ->modifier(),
            $policy->spellAttackBonus(
                $sorcerer
            )
        );
    }

    public function testRegisterShowsAwaitingOriginWhenUnchosen(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(1)
        );

        self::assertSame(
            'Awaiting Origin Spark',
            $register['origin']['label']
        );

        self::assertFalse(
            $register['origin']['chosen']
        );
    }

    public function testRegisterShowsKnownSpellModelAndCounts(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(10)
        );

        self::assertSame(
            'known-spells',
            $register[
                'spellcasting'
            ]['model']
        );

        self::assertSame(
            11,
            $register[
                'spellcasting'
            ]['spells_known']
        );

        self::assertSame(
            6,
            $register[
                'spellcasting'
            ]['cantrips_known']
        );

        self::assertSame(
            5,
            $register[
                'spellcasting'
            ]['maximum_spell_level']
        );
    }

    public function testRegisterShowsSharedSpellSlotState(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(5),
            ActiveClassResourceState::fromArray([
                'spell-slot-1' => 1,
            ])
        );

        self::assertNotSame(
            [],
            $register[
                'spellcasting'
            ]['slots']
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
    }

    public function testRegisterShowsNextOriginMilestone(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(1)
        );

        self::assertSame(
            2,
            $register[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            'Font of Magic',
            $register[
                'next_milestone'
            ]['label']
        );
    }

    public function testRepoOriginCandidatesRemainFiveAndSourceDescriptionsRemainUntouched(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'sorcerer'
            )
        );

        self::assertCount(
            5,
            $candidates
        );

        self::assertSame(
            [
                'juiced-blooded',
                'sugarspark-soul',
                'carbonation-soul',
                'soda-born',
                'dairyblooded-soul',
            ],
            array_column(
                $candidates,
                'key'
            )
        );

        foreach ($candidates as $candidate) {
            self::assertStringContainsString(
                'path for the Sorcerer',
                (string) (
                    $candidate['detail']
                    ?? ''
                )
            );
        }
    }

    public function testOriginRegisterDoesNotInventMissingPlaystyleGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'sorcerer'
            )
        );

        foreach ($candidates as $candidate) {
            self::assertSame(
                '',
                (string) (
                    $candidate['identity']
                    ?? ''
                )
            );

            self::assertSame(
                '',
                (string) (
                    $candidate['playstyle']
                    ?? ''
                )
            );

            self::assertSame(
                '',
                (string) (
                    $candidate['best_for']
                    ?? ''
                )
            );
        }
    }

    public function testControllerAndLedgerReceiveOriginRegister(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'SorcererOriginRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'originRegister' => \$originRegister",
            $controller
        );

        self::assertStringContainsString(
            'The Origin Spark Register',
            $view
        );

        self::assertStringContainsString(
            'data-origin-register',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-origin-register-title"',
            $view
        );
    }

    public function testOriginRegisterRemainsReadOnlyInThisSlice(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringNotContainsString(
            'data-sorcery-point-spend',
            $view
        );

        self::assertStringNotContainsString(
            'data-metamagic-use',
            $view
        );
    }

    public function testRegisterPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-origin-register',
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

    public function testPolicyRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new SorcererOriginPolicy())
            ->sorceryPointMaximum(
                $this->character(
                    'fighter',
                    5
                )
            );
    }

    private function sorcerer(
        int $level
    ): Character {
        return $this->character(
            'sorcerer',
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
                'Origin Register Tester'
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

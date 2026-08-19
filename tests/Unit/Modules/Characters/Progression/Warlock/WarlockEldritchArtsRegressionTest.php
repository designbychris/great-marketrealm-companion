<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockEldritchArtsPresenter;
use PHPUnit\Framework\TestCase;

final class WarlockEldritchArtsRegressionTest extends TestCase
{
    public function testNonWarlockDoesNotReceiveEldritchArts(): void
    {
        $state = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $this->character(
                'fighter',
                5
            )
        );

        self::assertFalse(
            $state['supported']
        );

        self::assertSame(
            [],
            $state['beams']
        );
    }

    public function testBureaucraticHexBeamCountScalesByCharacterLevel(): void
    {
        $presenter =
            new WarlockEldritchArtsPresenter();

        foreach ([
            1 => 1,
            4 => 1,
            5 => 2,
            10 => 2,
            11 => 3,
            16 => 3,
            17 => 4,
            20 => 4,
        ] as $level => $expected) {
            self::assertSame(
                $expected,
                $presenter
                    ->present(
                        $this->warlock($level)
                    )['beam_count']
            );
        }
    }

    public function testEveryBeamIsAnIndependentOneD10ForceAttack(): void
    {
        $state = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $this->warlock(17)
        );

        self::assertCount(
            4,
            $state['beams']
        );

        foreach ($state['beams'] as $beam) {
            self::assertSame(
                '1d10',
                $beam['damage_formula']
            );

            self::assertSame(
                'force',
                $beam['damage_type']
            );

            self::assertSame(
                'creature',
                $beam['target_mode']
            );
        }
    }

    public function testBeamLabelsRemainIndividuallyNumbered(): void
    {
        $state = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $this->warlock(11)
        );

        self::assertSame(
            [
                'Bureaucratic Hex · Beam 1',
                'Bureaucratic Hex · Beam 2',
                'Bureaucratic Hex · Beam 3',
            ],
            array_column(
                $state['beams'],
                'label'
            )
        );
    }

    public function testSignatureCantripDoesNotSpendPactSlot(): void
    {
        $state = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $this->warlock(5)
        );

        self::assertTrue(
            $state['at_will']
        );

        self::assertFalse(
            $state['pact_slot_required']
        );
    }

    public function testEldritchAttackBonusUsesCharismaAndProficiency(): void
    {
        $warlock = $this->warlock(5);

        $state = (
            new WarlockEldritchArtsPresenter()
        )->present($warlock);

        self::assertSame(
            $warlock
                ->proficiencyBonus()
                ->value()
            + $warlock
                ->abilityScores()
                ->charisma()
                ->modifier(),
            $state['attack_bonus']
        );
    }

    public function testControllerSuppliesEldritchArtsToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'WarlockEldritchArtsPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'eldritchArts' => \$eldritchArts",
            $controller
        );
    }

    public function testLedgerRendersOneIndependentControlRowPerBeam(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-eldritch-arts',
            $view
        );

        self::assertStringContainsString(
            'data-eldritch-beam=',
            $view
        );

        self::assertStringContainsString(
            'Roll Beam Attack',
            $view
        );

        self::assertStringContainsString(
            'Roll Beam Damage',
            $view
        );

        self::assertStringContainsString(
            'data-roll-formula="1d10"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-damage-type="force"',
            $view
        );
    }

    public function testEachBeamUsesGuildDiceworksSpellAttackContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-guild-roll="d20"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-kind="spell-attack"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-target-mode="creature"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-result-suffix="to hit"',
            $view
        );
    }

    public function testGenericArcaneCardDefersSignatureCantripToEldritchArts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            "\$isWarlockSignature",
            $view
        );

        self::assertStringContainsString(
            "=== 'bureaucratic-hex'",
            $view
        );

        self::assertStringContainsString(
            'Use Eldritch Arts above',
            $view
        );

        self::assertStringContainsString(
            'Independent beam attacks are resolved separately.',
            $view
        );
    }

    public function testLedgerExplainsBeamsMayChooseDifferentTargets(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Each beam resolves separately',
            $view
        );

        self::assertStringContainsString(
            'may target a',
            $view
        );
    }

    public function testEldritchArtsDoesNotInventInvocationEnhancements(): void
    {
        $presenter = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Patron/Services/'
            . 'WarlockEldritchArtsPresenter.php'
        );

        self::assertStringNotContainsString(
            'agonizing',
            strtolower($presenter)
        );

        self::assertStringNotContainsString(
            'repelling',
            strtolower($presenter)
        );
    }

    public function testEldritchArtsPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-eldritch-arts',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 840px)',
            $css
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

    private function warlock(
        int $level
    ): Character {
        return $this->character(
            'warlock',
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
                'Eldritch Arts Tester'
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

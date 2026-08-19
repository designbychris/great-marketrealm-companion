<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\PaladinSacredReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\PaladinProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredActionPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinFinalSealRegressionTest extends TestCase
{
    public function testPaladinRemainsSpecialistCalling(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('paladin')
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
    }

    public function testCorePaladinMilestonesRemainStable(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        $expected = [
            2 => [
                'fighting-style',
                'spellcasting',
                'divine-smite',
            ],
            3 => ['divine-health'],
            5 => ['extra-attack'],
            6 => ['aura-of-protection'],
            10 => ['aura-of-courage'],
            11 => ['improved-divine-smite'],
            14 => ['cleansing-touch'],
            18 => ['aura-improvement'],
        ];

        foreach ($expected as $level => $keys) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $paladin,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthAndOathDelegationsRemainSeparated(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $paladin,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }

        foreach ([3, 7, 15, 20] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $paladin,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testAllEightSacredOathsRemainRegistered(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('paladin')
        );

        self::assertCount(8, $candidates);

        self::assertSame(
            [
                'oath-of-inventory',
                'oath-of-the-colonel',
                'oath-of-the-creamfather',
                'oath-of-aroma',
                'oath-of-clearance',
                'oath-of-seasoning',
                'oath-of-carbonation',
                'oath-of-the-cleaver-saint',
            ],
            array_column($candidates, 'key')
        );
    }

    public function testEverySacredOathKeepsFourGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->oaths() as $oath) {
            self::assertTrue(
                $catalogue->supports($oath)
            );

            self::assertSame(
                [3, 7, 15, 20],
                array_column(
                    $catalogue->all($oath),
                    'level'
                )
            );
        }
    }

    public function testEverySacredOathKeepsDecisionGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('paladin')
        );

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

    public function testSacredPolicyRemainsSingleAuthorityForCoreMaximums(): void
    {
        $paladin = $this->paladin(6);
        $policy = new PaladinSacredPolicy();

        self::assertSame(
            30,
            $policy->layOnHandsMaximum($paladin)
        );

        self::assertSame(
            10,
            $policy->auraRangeFeet($paladin)
        );

        self::assertSame(
            8
            + $paladin->proficiencyBonus()->value()
            + $paladin->abilityScores()->charisma()->modifier(),
            $policy->sacredSaveDc($paladin)
        );
    }

    public function testSacredReservePersistsExpenditureNotMaximum(): void
    {
        $service = new PaladinSacredReserveService();
        $state = $service->spend(
            $this->paladin(4),
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::LAY_ON_HANDS,
            5
        );

        self::assertSame(
            5,
            $state->expended(
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            15,
            $service->remaining(
                $this->paladin(4),
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );

        self::assertSame(
            20,
            $service->remaining(
                $this->paladin(5),
                $state,
                PaladinSacredReserveService::LAY_ON_HANDS
            )
        );
    }

    public function testLongRestRestoresSacredReservesAndSharedSpellSlots(): void
    {
        $paladin = $this->paladin(5);
        $sacred = new PaladinSacredReserveService();
        $slots = new SharedSpellSlotReserveService();

        $state = $sacred->spend(
            $paladin,
            ActiveClassResourceState::fresh(),
            PaladinSacredReserveService::DIVINE_SENSE
        );

        $state = $slots->spend(
            $paladin,
            $state,
            1
        );

        $state = $sacred->longRest(
            $paladin,
            $state
        );

        $state = $slots->longRest(
            $paladin,
            $state
        );

        self::assertSame(
            0,
            $state->expended(
                PaladinSacredReserveService::DIVINE_SENSE
            )
        );

        self::assertSame(
            0,
            $state->expended('spell-slot-1')
        );
    }

    public function testHalfCasterProgressionReachesLevelFiveSlotsAtSeventeen(): void
    {
        $slots = (
            new SharedSpellSlotReserveService()
        )->present(
            $this->paladin(17),
            ActiveClassResourceState::fresh()
        );

        self::assertContains(
            5,
            array_column(
                $slots,
                'level'
            )
        );
    }

    public function testDivineSmiteUsesSharedSlotsAndCertifiedDamageScaling(): void
    {
        $actions = (
            new PaladinSacredActionPresenter()
        )->present(
            $this->paladin(17),
            ActiveClassResourceState::fresh()
        );

        $formulae = [];

        foreach (
            $actions['divine_smite']['smite_options']
            as $option
        ) {
            $formulae[
                $option['slot_level']
            ] = $option['formula'];
        }

        self::assertSame('2d8', $formulae[1]);
        self::assertSame('3d8', $formulae[2]);
        self::assertSame('4d8', $formulae[3]);
        self::assertSame('5d8', $formulae[4]);
        self::assertSame('5d8', $formulae[5]);
    }

    public function testDivineSmiteDoesNotInventQualification(): void
    {
        $actions = (
            new PaladinSacredActionPresenter()
        )->present(
            $this->paladin(5),
            ActiveClassResourceState::fresh()
        );

        self::assertStringContainsString(
            'after the table confirms',
            $actions[
                'divine_smite'
            ]['qualification']
        );
    }

    public function testCleansingTouchRemainsLockedBeforeFourteen(): void
    {
        $actions = (
            new PaladinSacredActionPresenter()
        )->present(
            $this->paladin(13),
            ActiveClassResourceState::fresh()
        );

        self::assertFalse(
            $actions[
                'cleansing_touch'
            ]['unlocked']
        );

        self::assertFalse(
            $actions[
                'cleansing_touch'
            ]['available']
        );
    }

    public function testSacredRegisterCarriesReserveAndActionStateTogether(): void
    {
        $register = (
            new PaladinSacredRegisterPresenter()
        )->present(
            $this->paladin(5),
            ActiveClassResourceState::fromArray([
                PaladinSacredReserveService::LAY_ON_HANDS => 3,
                PaladinSacredReserveService::DIVINE_SENSE => 1,
            ])
        );

        self::assertSame(
            25,
            $register[
                'lay_on_hands'
            ]['maximum']
        );

        self::assertSame(
            22,
            $register[
                'lay_on_hands'
            ]['remaining']
        );

        self::assertTrue(
            $register[
                'actions'
            ]['divine_smite']['unlocked']
        );
    }

    public function testSacredActionFormsRemainOnApplicationPostBridge(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $view
        );

        self::assertStringContainsString(
            '/sacred/action',
            $view
        );

        self::assertStringContainsString(
            '/sacred/rest',
            $view
        );

        self::assertStringContainsString(
            "'gmrc_character_sacred_'",
            $view
        );
    }

    public function testSmiteKeepsGuildDiceworksContract(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-guild-roll="damage"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-source="Divine Smite"',
            $view
        );

        self::assertStringContainsString(
            'data-roll-damage-type="radiant"',
            $view
        );

        $script = $this->source(
            'assets/js/modules/characters/guild-dice.js'
        );

        self::assertStringContainsString(
            "ledger.addEventListener('click'",
            $script
        );

        self::assertStringContainsString(
            "'[data-guild-roll]'",
            $script
        );
    }

    public function testLayOnHandsKeepsRecipientBoundaryExplicit(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Heal this Paladin',
            $view
        );

        self::assertStringContainsString(
            'Record spend for another creature',
            $view
        );
    }

    public function testFinalSealSmiteLayoutCannotUseThreeColumnOverflowRule(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.6E — The Paladin's Final Seal",
            $css
        );

        self::assertStringContainsString(
            'container-type: inline-size',
            $css
        );

        self::assertStringContainsString(
            'grid-column: 1 / -1',
            $css
        );

        self::assertStringContainsString(
            'repeat(2, minmax(0, 1fr))',
            $css
        );

        self::assertStringContainsString(
            '@container (max-width: 17rem)',
            $css
        );
    }

    public function testFinalSealKeepsResponsiveAndForcedColourSupport(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );

        self::assertStringContainsString(
            'max-width: 100%',
            $css
        );

        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );
    }

    public function testSacredReserveRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredReserveService())
            ->longRest(
                $this->character(
                    'fighter',
                    5
                ),
                ActiveClassResourceState::fresh()
            );
    }

    /** @return array<int,string> */
    private function oaths(): array
    {
        return [
            'oath-of-inventory',
            'oath-of-the-colonel',
            'oath-of-the-creamfather',
            'oath-of-aroma',
            'oath-of-clearance',
            'oath-of-seasoning',
            'oath-of-carbonation',
            'oath-of-the-cleaver-saint',
        ];
    }

    private function paladin(
        int $level
    ): Character {
        return $this->character(
            'paladin',
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
                'Final Seal Tester'
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

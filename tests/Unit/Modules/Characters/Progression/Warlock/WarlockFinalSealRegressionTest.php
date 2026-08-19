<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\WarlockPactReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WarlockProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockEldritchArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WarlockFinalSealRegressionTest extends TestCase
{
    public function testWarlockRemainsSpecialistPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
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

    public function testPatronContractRemainsLevelOnePathChoice(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
        );

        self::assertIsArray($definition);
        self::assertSame(
            'Otherworldly Patron',
            $definition['label']
        );
        self::assertSame(
            'Patron Contract Folio',
            $definition['folio_label']
        );
        self::assertSame(
            'otherworldly-patron',
            $definition['choice_key']
        );
        self::assertSame(
            1,
            $definition['selection_level']
        );
    }

    public function testAllFourPatronsRemainRegisteredWithGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('warlock')
        );

        self::assertCount(4, $candidates);

        self::assertSame(
            [
                'pact-of-the-mascot',
                'the-forgotten-freezer',
                'the-spoilfather',
                'the-sugar-fiend',
            ],
            array_column($candidates, 'key')
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
        }
    }

    public function testEveryPatronKeepsFourGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->patrons() as $patron) {
            self::assertTrue(
                $catalogue->supports($patron)
            );

            self::assertSame(
                [1, 6, 10, 14],
                array_column(
                    $catalogue->all($patron),
                    'level'
                )
            );
        }
    }

    public function testWarlockCallingMilestonesRemainStable(): void
    {
        $progression = new WarlockProgression();
        $warlock =
            CharacterClass::fromString('warlock');

        self::assertSame(
            ['eldritch-invocations'],
            array_column(
                $progression
                    ->forLevel(
                        $warlock,
                        2
                    )['automatic'],
                'key'
            )
        );

        self::assertContains(
            'pact-boon',
            array_column(
                $progression
                    ->forLevel(
                        $warlock,
                        3
                    )['automatic'],
                'key'
            )
        );

        self::assertContains(
            'mystic-arcanum-6',
            array_column(
                $progression
                    ->forLevel(
                        $warlock,
                        11
                    )['automatic'],
                'key'
            )
        );

        self::assertSame(
            ['eldritch-master'],
            array_column(
                $progression
                    ->forLevel(
                        $warlock,
                        20
                    )['automatic'],
                'key'
            )
        );
    }

    public function testInvocationKnownCadenceRemainsCertified(): void
    {
        $policy = new WarlockPatronPolicy();

        $expected = [
            1 => 0,
            2 => 2,
            5 => 3,
            7 => 4,
            9 => 5,
            12 => 6,
            15 => 7,
            18 => 8,
        ];

        foreach ($expected as $level => $known) {
            self::assertSame(
                $known,
                $policy->invocationsKnown(
                    $this->warlock($level)
                )
            );
        }
    }

    public function testPactMagicSlotProgressionRemainsCertified(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(
            1,
            $policy->pactSlotLevel(
                $this->warlock(1)
            )
        );

        self::assertSame(
            5,
            $policy->pactSlotLevel(
                $this->warlock(9)
            )
        );

        self::assertSame(
            2,
            $policy->pactSlots(
                $this->warlock(10)
            )
        );

        self::assertSame(
            3,
            $policy->pactSlots(
                $this->warlock(11)
            )
        );

        self::assertSame(
            4,
            $policy->pactSlots(
                $this->warlock(17)
            )
        );
    }

    public function testPactReserveRestoresOnShortAndLongRest(): void
    {
        $service = new WarlockPactReserveService();
        $warlock = $this->warlock(5);

        $spent = $service->spend(
            $warlock,
            ActiveClassResourceState::fresh()
        );

        self::assertSame(
            1,
            $service->expended($spent)
        );

        self::assertSame(
            0,
            $service->expended(
                $service->shortRest(
                    $warlock,
                    $spent
                )
            )
        );

        self::assertSame(
            0,
            $service->expended(
                $service->longRest(
                    $warlock,
                    $spent
                )
            )
        );
    }

    public function testPactReserveUsesDedicatedResourceIdentity(): void
    {
        self::assertSame(
            'pact-magic-slot',
            WarlockPactReserveService::RESOURCE
        );

        self::assertStringNotContainsString(
            'spell-slot-',
            WarlockPactReserveService::RESOURCE
        );
    }

    public function testMysticArcanumCadenceRemainsSixThroughNine(): void
    {
        $policy = new WarlockPatronPolicy();

        self::assertSame(
            [],
            $policy->mysticArcanumLevels(
                $this->warlock(10)
            )
        );

        self::assertSame(
            [6],
            $policy->mysticArcanumLevels(
                $this->warlock(11)
            )
        );

        self::assertSame(
            [6, 7],
            $policy->mysticArcanumLevels(
                $this->warlock(13)
            )
        );

        self::assertSame(
            [6, 7, 8],
            $policy->mysticArcanumLevels(
                $this->warlock(15)
            )
        );

        self::assertSame(
            [6, 7, 8, 9],
            $policy->mysticArcanumLevels(
                $this->warlock(17)
            )
        );
    }

    public function testBureaucraticHexBeamCadenceRemainsIndependent(): void
    {
        $presenter =
            new WarlockEldritchArtsPresenter();

        foreach ([
            1 => 1,
            5 => 2,
            11 => 3,
            17 => 4,
        ] as $level => $beams) {
            $state = $presenter->present(
                $this->warlock($level)
            );

            self::assertSame(
                $beams,
                $state['beam_count']
            );

            self::assertCount(
                $beams,
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
            }
        }
    }

    public function testBureaucraticHexNeverSpendsPactMagic(): void
    {
        $state = (
            new WarlockEldritchArtsPresenter()
        )->present(
            $this->warlock(17)
        );

        self::assertTrue(
            $state['at_will']
        );

        self::assertFalse(
            $state['pact_slot_required']
        );
    }

    public function testBeamButtonsKeepGuildDiceworksContracts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-eldritch-beam=',
            $view
        );

        self::assertStringContainsString(
            'data-roll-kind="spell-attack"',
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

    public function testBeamAttackButtonNoLongerDisplaysLiteralTwentyGlyph(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            '<span aria-hidden="true">✥</span>',
            $view
        );

        self::assertStringNotContainsString(
            '<span aria-hidden="true">20</span>'
            . "\n"
            . '                                        Roll Beam Attack',
            $view
        );
    }

    public function testFinalSealStylesBeamGlyphAsDecoration(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.7E — The Warlock's Final Seal",
            $css
        );

        self::assertStringContainsString(
            '.gmrc-eldritch-beam__actions'
            . ' .gmrc-guild-roll-trigger'
            . ' > span[aria-hidden="true"]',
            $css
        );

        self::assertStringContainsString(
            'place-items: center',
            $css
        );
    }

    public function testPatronRegisterCarriesCertifiedGiftAndPactStateTogether(): void
    {
        $register = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $this->warlock(
                10,
                'pact-of-the-mascot',
                [
                    'smiling-sponsorship',
                    'brand-ambassador',
                ]
            ),
            ActiveClassResourceState::fromArray([
                WarlockPactReserveService::RESOURCE => 1,
            ])
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertSame(
            1,
            $register[
                'pact_magic'
            ]['remaining']
        );

        self::assertSame(
            [
                'smiling-sponsorship',
                'brand-ambassador',
            ],
            array_column(
                $register['patron_gifts'],
                'key'
            )
        );
    }

    public function testWarlockRoutesRemainDedicatedToPactReserve(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/characters/{id}/pact/spend',
            $routes
        );

        self::assertStringContainsString(
            '/characters/{id}/pact/rest',
            $routes
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'gmrc_character_pact_',
            $provider
        );
    }

    public function testFinalSealKeepsResponsiveAndForcedColourSupport(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
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

    public function testPactReserveRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new WarlockPactReserveService())
            ->spend(
                $this->character(
                    'fighter',
                    5
                ),
                ActiveClassResourceState::fresh()
            );
    }

    /** @return array<int,string> */
    private function patrons(): array
    {
        return [
            'pact-of-the-mascot',
            'the-forgotten-freezer',
            'the-spoilfather',
            'the-sugar-fiend',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function warlock(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Final Seal Warlock'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'warlock'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(24),
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
                'Final Seal Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(24),
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

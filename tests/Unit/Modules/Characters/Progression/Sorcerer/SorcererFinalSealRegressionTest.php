<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Sorcerer;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SorcererSorceryReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\SorcererProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererMetamagicCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererMetamagicService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\SorcererSpellcastingProgression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SorcererFinalSealRegressionTest extends TestCase
{
    public function testSorcererRemainsSpecialistSpellcastingPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertSame(
            ClassCapabilityProfile::SPECIALIST,
            $profile->implementationState()
        );

        self::assertTrue(
            $profile->hasSpecialistAdvancement()
        );

        self::assertTrue(
            $profile->hasSpellcastingProgression()
        );

        self::assertTrue(
            $profile->hasCallingPathProgression()
        );
    }

    public function testSorcerousOriginRemainsLevelOnePathChoice(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertIsArray($definition);
        self::assertSame(
            'Sorcerous Origin',
            $definition['label']
        );
        self::assertSame(
            'Origin Spark Folio',
            $definition['folio_label']
        );
        self::assertSame(
            'sorcerous-origin',
            $definition['choice_key']
        );
        self::assertSame(
            1,
            $definition['selection_level']
        );
    }

    public function testFiveMarketrealmOriginsRemainRegistered(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('sorcerer')
        );

        self::assertCount(5, $candidates);

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
    }

    public function testOriginGiftBoundaryRemainsExplicitlyUnimplemented(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ([
            'juiced-blooded',
            'sugarspark-soul',
            'carbonation-soul',
            'soda-born',
            'dairyblooded-soul',
        ] as $origin) {
            self::assertFalse(
                $catalogue->supports($origin)
            );

            self::assertSame(
                [],
                $catalogue->all($origin)
            );
        }
    }

    public function testCallingKeepsFontMetamagicAndRestorationMilestones(): void
    {
        $progression =
            new SorcererProgression();
        $sorcerer =
            CharacterClass::fromString(
                'sorcerer'
            );

        self::assertSame(
            'font-of-magic',
            $progression
                ->forLevel(
                    $sorcerer,
                    2
                )['automatic'][0]['key']
        );

        self::assertSame(
            2,
            $progression
                ->forLevel(
                    $sorcerer,
                    3
                )['automatic'][0][
                    'options_known'
                ]
        );

        self::assertSame(
            3,
            $progression
                ->forLevel(
                    $sorcerer,
                    10
                )['automatic'][0][
                    'options_known'
                ]
        );

        self::assertSame(
            4,
            $progression
                ->forLevel(
                    $sorcerer,
                    17
                )['automatic'][0][
                    'options_known'
                ]
        );

        self::assertSame(
            'sorcerous-restoration',
            $progression
                ->forLevel(
                    $sorcerer,
                    20
                )['automatic'][0]['key']
        );
    }

    public function testSorcererRemainsKnownSpellFullCaster(): void
    {
        $progression =
            new SorcererSpellcastingProgression();

        $levelTen =
            $progression->forLevel(
                CharacterClass::fromString(
                    'sorcerer'
                ),
                10
            );

        self::assertSame(
            'known-spells',
            $levelTen['model']
        );

        self::assertSame(
            11,
            $levelTen['spells_known']
        );

        self::assertSame(
            6,
            $levelTen['cantrips_known']
        );

        self::assertSame(
            5,
            $levelTen['maximum_spell_level']
        );
    }

    public function testKnownSpellProgressionReachesNinthCircleAtTwenty(): void
    {
        $levelTwenty = (
            new SorcererSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString(
                'sorcerer'
            ),
            20
        );

        self::assertSame(
            15,
            $levelTwenty['spells_known']
        );

        self::assertSame(
            6,
            $levelTwenty['cantrips_known']
        );

        self::assertSame(
            9,
            $levelTwenty[
                'maximum_spell_level'
            ]
        );
    }

    public function testSorceryPointMaximumRemainsSorcererLevel(): void
    {
        $policy =
            new SorcererOriginPolicy();

        foreach (
            [2, 5, 10, 17, 20]
            as $level
        ) {
            self::assertSame(
                $level,
                $policy
                    ->sorceryPointMaximum(
                        $this->sorcerer(
                            $level
                        )
                    )
            );
        }
    }

    public function testSorceryReservePersistsExpenditureNotMaximum(): void
    {
        $service =
            new SorcererSorceryReserveService();

        $state = $service->spend(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            3
        );

        self::assertSame(
            3,
            $state->expended(
                SorcererSorceryReserveService::RESOURCE
            )
        );

        self::assertSame(
            2,
            $service->remaining(
                $this->sorcerer(5),
                $state
            )
        );

        self::assertSame(
            7,
            $service->remaining(
                $this->sorcerer(10),
                $state
            )
        );
    }

    public function testFlexibleCastingCreationCostsRemainCertified(): void
    {
        self::assertSame(
            [2, 3, 5, 6, 7],
            array_column(
                (
                    new SorcererSorceryReserveService()
                )->slotCreationCosts(),
                'cost'
            )
        );
    }

    public function testFlexibleCastingNeverCreatesAboveFifthCircle(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->slotCreationCost(6);
    }

    public function testLongRestRestoresSorceryPointsAndSharedSlots(): void
    {
        $sorcerer =
            $this->sorcerer(5);
        $sorcery =
            new SorcererSorceryReserveService();
        $slots =
            new SharedSpellSlotReserveService();

        $state = $sorcery->spend(
            $sorcerer,
            ActiveClassResourceState::fresh(),
            2
        );

        $state = $slots->spend(
            $sorcerer,
            $state,
            1
        );

        $state = $sorcery->longRest(
            $sorcerer,
            $state
        );

        $state = $slots->longRest(
            $sorcerer,
            $state
        );

        self::assertSame(
            0,
            $state->expended(
                SorcererSorceryReserveService::RESOURCE
            )
        );

        self::assertSame(
            0,
            $state->expended(
                'spell-slot-1'
            )
        );
    }

    public function testMetamagicAllowanceRemainsTwoThreeFour(): void
    {
        $service =
            new SorcererMetamagicService();

        self::assertSame(
            2,
            $service->allowance(
                $this->sorcerer(3)
            )
        );

        self::assertSame(
            3,
            $service->allowance(
                $this->sorcerer(10)
            )
        );

        self::assertSame(
            4,
            $service->allowance(
                $this->sorcerer(17)
            )
        );
    }

    public function testEightMetamagicArtsRemainCertified(): void
    {
        $options = (
            new SorcererMetamagicCatalogue()
        )->all();

        self::assertCount(
            8,
            $options
        );

        self::assertContains(
            'quickened-spell',
            array_column(
                $options,
                'key'
            )
        );

        self::assertContains(
            'twinned-spell',
            array_column(
                $options,
                'key'
            )
        );
    }

    public function testMetamagicUsesSameSorceryPointReserve(): void
    {
        $state = (
            new SorcererMetamagicService()
        )->use(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            [
                'quickened-spell',
                'subtle-spell',
            ],
            'quickened-spell'
        );

        self::assertSame(
            2,
            $state->expended(
                SorcererSorceryReserveService::RESOURCE
            )
        );
    }

    public function testOriginRegisterCarriesSorceryAndMetamagicTogether(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(10),
            ActiveClassResourceState::fromArray([
                SorcererSorceryReserveService::RESOURCE => 3,
            ]),
            [
                'careful-spell',
                'quickened-spell',
                'subtle-spell',
            ]
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertSame(
            7,
            $register[
                'sorcery_points'
            ]['remaining']
        );

        self::assertSame(
            3,
            $register[
                'metamagic'
            ]['known']
        );

        self::assertCount(
            3,
            $register[
                'metamagic'
            ]['selected']
        );
    }

    public function testSorcererRoutesRemainSeparatedByResponsibility(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        foreach ([
            '/sorcery/spend',
            '/sorcery/convert',
            '/sorcery/rest',
            '/metamagic/choices',
            '/metamagic/use',
        ] as $route) {
            self::assertStringContainsString(
                $route,
                $routes
            );
        }
    }

    public function testSorcererFormsRemainOnApplicationPostBridge(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'value="gmrc_app_request"',
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_sorcery_',
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_metamagic_',
            $view
        );
    }

    public function testFinalSealHardensLongMetamagicLabelsAndButtons(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.8E — The Sorcerer's Final Seal",
            $css
        );

        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );

        self::assertStringContainsString(
            'white-space: normal',
            $css
        );

        self::assertStringContainsString(
            'max-width: 100%',
            $css
        );
    }

    public function testFinalSealKeepsNarrowScreenAndForcedColourSupport(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '@media (max-width: 420px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );

        self::assertStringContainsString(
            'outline-color: Highlight',
            $css
        );
    }

    public function testFinalSealKeepsNativeAccessibleSelectionControls(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'type="checkbox"',
            $view
        );

        self::assertStringContainsString(
            'name="metamagic[]"',
            $view
        );

        self::assertStringContainsString(
            'name="spell_level"',
            $view
        );

        self::assertStringContainsString(
            'data-metamagic-use=',
            $view
        );
    }

    public function testSorceryReserveStillRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererSorceryReserveService()
        )->maximum(
            $this->character(
                'wizard',
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
                'Sorcerer Final Seal'
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

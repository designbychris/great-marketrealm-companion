<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\RangerFieldReserveService;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RangerProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldArtsPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\RangerSpellcastingProgression;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RangerFinalSealRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function pathKeys(): array
    {
        return [
            'aislewarden-conclave',
            'deep-root-warden',
            'cold-vault-stalker',
            'conclave-of-the-forager',
            'spice-trail-hunter',
            'rindrunner',
            'seedshot-conclave',
            'expiry-hunter',
        ];
    }

    public function testRangerRemainsSpecialistSpellcastingPathCalling(): void
    {
        $profile = (
            new ClassCapabilityCatalogue()
        )->forClass(
            CharacterClass::fromString('ranger')
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

    public function testRangerFoundationsRemainFavouredMarkAndNaturalExplorer(): void
    {
        $foundations = (
            new RangerProgression()
        )->foundations(
            CharacterClass::fromString('ranger')
        );

        self::assertSame(
            [
                'favoured-mark',
                'natural-explorer',
            ],
            array_column(
                $foundations,
                'key'
            )
        );
    }

    public function testRangerPathSelectionRemainsLevelThree(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('ranger')
        );

        self::assertIsArray($definition);
        self::assertSame(
            'Ranger Path',
            $definition['label']
        );
        self::assertSame(
            'Field Path Folio',
            $definition['folio_label']
        );
        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testExactlyEightCanonRangerPathsRemainAvailable(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('ranger')
        );

        self::assertCount(
            8,
            $candidates
        );

        self::assertSame(
            $this->pathKeys(),
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testEveryRangerPathRetainsFourFeatureMilestones(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach (
            $this->pathKeys()
            as $path
        ) {
            $gifts = $catalogue->all(
                $path
            );

            self::assertCount(
                4,
                $gifts
            );

            self::assertSame(
                [3, 7, 11, 15],
                array_column(
                    $gifts,
                    'level'
                )
            );
        }
    }

    public function testRangerRemainsKnownSpellHalfCaster(): void
    {
        $entry = (
            new RangerSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            10
        );

        self::assertSame(
            'known-spells',
            $entry['model']
        );

        self::assertSame(
            6,
            $entry['spells_known']
        );

        self::assertSame(
            0,
            $entry['cantrips_known']
        );

        self::assertSame(
            3,
            $entry['maximum_spell_level']
        );
    }

    public function testRangerSpellcastingReachesFifthCircleAtSeventeen(): void
    {
        $entry = (
            new RangerSpellcastingProgression()
        )->forLevel(
            CharacterClass::fromString('ranger'),
            17
        );

        self::assertSame(
            5,
            $entry['maximum_spell_level']
        );

        self::assertSame(
            10,
            $entry['spells_known']
        );
    }

    public function testFieldRegisterCarriesPathSpellcastingAndMilestonesTogether(): void
    {
        $register = (
            new RangerFieldRegisterPresenter()
        )->present(
            $this->ranger(
                11,
                'expiry-hunter'
            )
        );

        self::assertTrue(
            $register['supported']
        );

        self::assertTrue(
            $register['path']['registered']
        );

        self::assertSame(
            8,
            $register['path']['candidate_count']
        );

        self::assertTrue(
            $register[
                'spellcasting'
            ]['unlocked']
        );

        self::assertSame(
            2,
            $register[
                'extra_attack'
            ]['attacks']
        );
    }

    public function testFiniteFieldReservesRemainSourceDefinedOnly(): void
    {
        $service =
            new RangerFieldReserveService();

        self::assertSame(
            [],
            $service->reserves(
                $this->ranger(
                    15,
                    'aislewarden-conclave'
                ),
                ActiveClassResourceState::fresh()
            )
        );

        self::assertSame(
            [],
            $service->reserves(
                $this->ranger(
                    15,
                    'cold-vault-stalker'
                ),
                ActiveClassResourceState::fresh()
            )
        );
    }

    public function testDeepRootReservesRemainPbPlusOncePerLongRest(): void
    {
        $ranger = $this->ranger(
            15,
            'deep-root-warden'
        );

        $reserves = (
            new RangerFieldReserveService()
        )->reserves(
            $ranger,
            ActiveClassResourceState::fresh()
        );

        self::assertCount(
            2,
            $reserves
        );

        self::assertSame(
            $ranger
                ->proficiencyBonus()
                ->value(),
            $reserves[0]['maximum']
        );

        self::assertSame(
            1,
            $reserves[1]['maximum']
        );
    }

    public function testExpiryHunterPutItBackRemainsPbUses(): void
    {
        $ranger = $this->ranger(
            11,
            'expiry-hunter'
        );

        $reserve = (
            new RangerFieldReserveService()
        )->reserves(
            $ranger,
            ActiveClassResourceState::fresh()
        )[0];

        self::assertSame(
            RangerFieldReserveService::PUT_IT_BACK,
            $reserve['resource']
        );

        self::assertSame(
            $ranger
                ->proficiencyBonus()
                ->value(),
            $reserve['maximum']
        );
    }

    public function testFieldReserveLongRestDoesNotSilentlyRestoreSpellSlots(): void
    {
        $service =
            new RangerFieldReserveService();

        $ranger = $this->ranger(
            15,
            'seedshot-conclave'
        );

        $state = ActiveClassResourceState::fromArray([
            RangerFieldReserveService::ANCIENT_SEED => 1,
            'spell-slot-1' => 1,
        ]);

        $rested = $service->longRest(
            $ranger,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                RangerFieldReserveService::ANCIENT_SEED
            )
        );

        self::assertSame(
            1,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testSharedSpellSlotLongRestCompletesRangerRestCycle(): void
    {
        $ranger = $this->ranger(
            5,
            'aislewarden-conclave'
        );

        $state = ActiveClassResourceState::fromArray([
            'spell-slot-1' => 1,
        ]);

        $rested = (
            new SharedSpellSlotReserveService()
        )->longRest(
            $ranger,
            $state
        );

        self::assertSame(
            0,
            $rested->expended(
                'spell-slot-1'
            )
        );
    }

    public function testFieldArtsRemainLevelGated(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        self::assertCount(
            1,
            $presenter->present(
                $this->ranger(
                    3,
                    'seedshot-conclave'
                )
            )['arts']
        );

        self::assertCount(
            2,
            $presenter->present(
                $this->ranger(
                    7,
                    'seedshot-conclave'
                )
            )['arts']
        );

        self::assertCount(
            4,
            $presenter->present(
                $this->ranger(
                    15,
                    'seedshot-conclave'
                )
            )['arts']
        );
    }

    public function testAislewardenQuarryDamageStillScalesFromD6ToD8(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        self::assertSame(
            '1d6',
            $presenter->present(
                $this->ranger(
                    3,
                    'aislewarden-conclave'
                )
            )['arts'][0]['rolls'][0]['formula']
        );

        self::assertSame(
            '1d8',
            $presenter->present(
                $this->ranger(
                    11,
                    'aislewarden-conclave'
                )
            )['arts'][0]['rolls'][0]['formula']
        );
    }

    public function testSpiceTrailInfusionsStillScaleFromD6ToTwoD6(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        self::assertSame(
            ['1d6', '1d6', '1d6', '1d6'],
            array_column(
                $presenter->present(
                    $this->ranger(
                        3,
                        'spice-trail-hunter'
                    )
                )['arts'][0]['choices'],
                'formula'
            )
        );

        self::assertSame(
            ['2d6', '2d6', '2d6', '2d6'],
            array_column(
                $presenter->present(
                    $this->ranger(
                        11,
                        'spice-trail-hunter'
                    )
                )['arts'][0]['choices'],
                'formula'
            )
        );
    }

    public function testFinalSeasoningKeepsFourTypedDamageRolls(): void
    {
        $final = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                15,
                'spice-trail-hunter'
            )
        )['arts'][3];

        self::assertSame(
            ['fire', 'thunder', 'radiant', 'poison'],
            array_column(
                $final['rolls'],
                'damage_type'
            )
        );

        self::assertSame(
            ['2d6', '2d6', '2d6', '2d6'],
            array_column(
                $final['rolls'],
                'formula'
            )
        );
    }

    public function testForagerAndSeedshotStillDoNotInventMissingDice(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        $forager = $presenter->present(
            $this->ranger(
                3,
                'conclave-of-the-forager'
            )
        )['arts'][0];

        self::assertArrayNotHasKey(
            'formula',
            $forager['choices'][3]
        );

        $seedshot = $presenter->present(
            $this->ranger(
                3,
                'seedshot-conclave'
            )
        )['arts'][0];

        foreach (
            $seedshot['choices']
            as $choice
        ) {
            self::assertArrayNotHasKey(
                'formula',
                $choice
            );
        }
    }

    public function testRangerFieldActionsRemainOnSharedGuildDiceworksSurface(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-ranger-field-arts',
            $view
        );

        self::assertStringContainsString(
            'data-guild-roll=',
            $view
        );

        self::assertStringContainsString(
            'data-ranger-field-art-use=',
            $view
        );

        self::assertStringNotContainsString(
            'data-ranger-diceworks',
            $view
        );
    }

    public function testRangerRoutesKeepSpendAndRestSeparated(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/characters/{id}/field/spend',
            $routes
        );

        self::assertStringContainsString(
            '/characters/{id}/field/rest',
            $routes
        );
    }

    public function testFinalSealHardensLongLabelsAndNarrowScreens(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            "Phase III.12.9E — The Ranger's Final Seal",
            $css
        );

        self::assertStringContainsString(
            'overflow-wrap: anywhere',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 380px)',
            $css
        );

        self::assertStringContainsString(
            'min-height: 2.75rem',
            $css
        );
    }

    public function testFinalSealPreservesReducedMotionAndForcedColours(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '@media (prefers-reduced-motion: reduce)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );

        self::assertStringContainsString(
            'ButtonText',
            $css
        );
    }

    public function testRangerReserveStillRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new RangerFieldReserveService()
        )->reserves(
            $this->character(
                'wizard',
                15,
                'deep-root-warden'
            ),
            ActiveClassResourceState::fresh()
        );
    }

    private function ranger(
        int $level,
        string $path
    ): Character {
        return $this->character(
            'ranger',
            $level,
            $path
        );
    }

    private function character(
        string $class,
        int $level,
        string $path
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Ranger Final Seal'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                $class
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $path
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

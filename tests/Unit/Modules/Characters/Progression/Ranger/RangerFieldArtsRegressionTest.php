<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\RangerFieldReserveService;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Ranger\Services\RangerFieldArtsPresenter;
use PHPUnit\Framework\TestCase;

final class RangerFieldArtsRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        self::assertFalse(
            (
                new RangerFieldArtsPresenter()
            )->present(
                $this->character(
                    'wizard',
                    5,
                    ''
                )
            )['supported']
        );
    }

    public function testRangerWithoutPathShowsNoArts(): void
    {
        $presented = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                3,
                ''
            )
        );

        self::assertTrue(
            $presented['supported']
        );

        self::assertSame(
            'Awaiting Ranger Path',
            $presented['path_label']
        );

        self::assertSame(
            [],
            $presented['arts']
        );
    }

    public function testAislewardenQuarryDamageImprovesAtEleven(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        $levelThree = $presenter->present(
            $this->ranger(
                3,
                'aislewarden-conclave'
            )
        );

        $levelEleven = $presenter->present(
            $this->ranger(
                11,
                'aislewarden-conclave'
            )
        );

        self::assertSame(
            '1d6',
            $levelThree[
                'arts'
            ][0]['rolls'][0]['formula']
        );

        self::assertSame(
            '1d8',
            $levelEleven[
                'arts'
            ][0]['rolls'][0]['formula']
        );
    }

    public function testDeepRootArtsExposeReserveBackedAbilities(): void
    {
        $arts = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                15,
                'deep-root-warden'
            )
        )['arts'];

        self::assertSame(
            RangerFieldReserveService::GRASPING_ROOTS,
            $arts[0]['resource']
        );

        self::assertSame(
            RangerFieldReserveService::HEART_OF_THE_ROOTLANDS,
            $arts[3]['resource']
        );

        self::assertSame(
            '2d8',
            $arts[3]['rolls'][0]['formula']
        );

        self::assertSame(
            'healing',
            $arts[3]['rolls'][0]['kind']
        );
    }

    public function testColdVaultStalkerUsesOnlySuppliedDamageFormulae(): void
    {
        $arts = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                15,
                'cold-vault-stalker'
            )
        )['arts'];

        self::assertSame(
            '1d6',
            $arts[0]['rolls'][0]['formula']
        );

        self::assertSame(
            '2d6',
            $arts[2]['rolls'][0]['formula']
        );

        self::assertSame(
            [],
            $arts[3]['rolls']
        );
    }

    public function testForagerShowsFiveSuppliedRemedies(): void
    {
        $art = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                3,
                'conclave-of-the-forager'
            )
        )['arts'][0];

        self::assertSame(
            'foragers-remedies',
            $art['key']
        );

        self::assertCount(
            5,
            $art['choices']
        );

        self::assertSame(
            [
                'mintleaf-draught',
                'basil-balm',
                'rosemary-tonic',
                'nettle-oil',
                'sagebrew',
            ],
            array_column(
                $art['choices'],
                'key'
            )
        );
    }

    public function testForagerDoesNotInventNettleOilDamageDie(): void
    {
        $choices = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                3,
                'conclave-of-the-forager'
            )
        )['arts'][0]['choices'];

        $nettle = $choices[3];

        self::assertStringContainsString(
            'No damage die was supplied',
            $nettle['effect']
        );

        self::assertArrayNotHasKey(
            'formula',
            $nettle
        );

        self::assertSame(
            '1d4',
            $choices[4]['formula']
        );
    }

    public function testSpiceInfusionsUseOneD6ThenTwoD6(): void
    {
        $presenter =
            new RangerFieldArtsPresenter();

        $early = $presenter->present(
            $this->ranger(
                3,
                'spice-trail-hunter'
            )
        )['arts'][0]['choices'];

        $improved = $presenter->present(
            $this->ranger(
                11,
                'spice-trail-hunter'
            )
        )['arts'][0]['choices'];

        self::assertSame(
            ['1d6', '1d6', '1d6', '1d6'],
            array_column(
                $early,
                'formula'
            )
        );

        self::assertSame(
            ['2d6', '2d6', '2d6', '2d6'],
            array_column(
                $improved,
                'formula'
            )
        );
    }

    public function testFinalSeasoningKeepsFourDamageTypesSeparate(): void
    {
        $arts = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                15,
                'spice-trail-hunter'
            )
        )['arts'];

        $final = $arts[3];

        self::assertSame(
            [
                'fire',
                'thunder',
                'radiant',
                'poison',
            ],
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

        self::assertSame(
            RangerFieldReserveService::FINAL_SEASONING,
            $final['resource']
        );
    }

    public function testRindrunnerPiercingWedgeRollsOneD8(): void
    {
        $arts = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                11,
                'rindrunner'
            )
        )['arts'];

        self::assertSame(
            '1d8',
            $arts[2]['rolls'][0]['formula']
        );
    }

    public function testSeedshotShowsFiveCanonSeedChoicesWithoutInventedDice(): void
    {
        $art = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                3,
                'seedshot-conclave'
            )
        )['arts'][0];

        self::assertCount(
            5,
            $art['choices']
        );

        self::assertSame(
            [
                'vine-seed',
                'burstmelon-seed',
                'sunseed',
                'heavyseed',
                'bloom-seed',
            ],
            array_column(
                $art['choices'],
                'key'
            )
        );

        foreach ($art['choices'] as $choice) {
            self::assertArrayNotHasKey(
                'formula',
                $choice
            );
        }
    }

    public function testExpiryHunterOffersRadiantOrNecroticExpiryDamage(): void
    {
        $art = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                3,
                'expiry-hunter'
            )
        )['arts'][0];

        self::assertSame(
            ['radiant', 'necrotic'],
            array_column(
                $art['rolls'],
                'damage_type'
            )
        );

        self::assertSame(
            ['1d8', '1d8'],
            array_column(
                $art['rolls'],
                'formula'
            )
        );
    }

    public function testPresenterOnlyShowsArtsUnlockedByCurrentLevel(): void
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

    public function testPresenterCarriesFieldReserveStateIntoArtsSurface(): void
    {
        $presented = (
            new RangerFieldArtsPresenter()
        )->present(
            $this->ranger(
                11,
                'expiry-hunter'
            ),
            ActiveClassResourceState::fromArray([
                RangerFieldReserveService::PUT_IT_BACK => 1,
            ])
        );

        self::assertCount(
            1,
            $presented['field_reserves']
        );

        self::assertSame(
            1,
            $presented[
                'field_reserves'
            ][0]['expended']
        );
    }

    public function testControllerSuppliesFieldArtsToLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'RangerFieldArtsPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'fieldArts' => \$fieldArts",
            $controller
        );
    }

    public function testLedgerShowsFieldArtsChoicesRollsAndReserveUses(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'The Ranger’s Field Arts',
            $view
        );

        self::assertStringContainsString(
            'data-ranger-field-art=',
            $view
        );

        self::assertStringContainsString(
            'data-ranger-field-choice=',
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
    }

    public function testFieldArtsReuseExistingFieldSpendRoute(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            ". '/field/spend'",
            $view
        );

        self::assertStringContainsString(
            'gmrc_character_field_',
            $view
        );
    }

    public function testFieldArtsExplainSourceFaithfulMissingValues(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Dice buttons appear only where your Ranger source',
            $view
        );

        self::assertStringContainsString(
            'rather than fabricated',
            $view
        );
    }

    public function testFieldArtsPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-ranger-field-arts',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 720px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
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
                'Field Arts Tester'
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

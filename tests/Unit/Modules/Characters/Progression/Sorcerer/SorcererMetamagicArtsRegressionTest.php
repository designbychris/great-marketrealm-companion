<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Sorcerer;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererMetamagicCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererMetamagicService;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services\SorcererOriginRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SorcererMetamagicArtsRegressionTest extends TestCase
{
    public function testCatalogueProvidesEightCertifiedMetamagicOptions(): void
    {
        $options = (
            new SorcererMetamagicCatalogue()
        )->all();

        self::assertCount(
            8,
            $options
        );

        self::assertSame(
            [
                'careful-spell',
                'distant-spell',
                'empowered-spell',
                'extended-spell',
                'heightened-spell',
                'quickened-spell',
                'subtle-spell',
                'twinned-spell',
            ],
            array_column(
                $options,
                'key'
            )
        );
    }

    public function testEveryMetamagicOptionHasPlayerFacingGuidance(): void
    {
        foreach (
            (
                new SorcererMetamagicCatalogue()
            )->all()
            as $option
        ) {
            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $option['label']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $option['summary']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $option['timing']
                        ?? ''
                    )
                )
            );
        }
    }

    public function testFixedMetamagicCostsRemainCertified(): void
    {
        $catalogue =
            new SorcererMetamagicCatalogue();

        self::assertSame(
            1,
            $catalogue->cost(
                'careful-spell'
            )
        );

        self::assertSame(
            3,
            $catalogue->cost(
                'heightened-spell'
            )
        );

        self::assertSame(
            2,
            $catalogue->cost(
                'quickened-spell'
            )
        );

        self::assertSame(
            1,
            $catalogue->cost(
                'subtle-spell'
            )
        );
    }

    public function testTwinnedSpellUsesSpellLevelWithMinimumOnePoint(): void
    {
        $catalogue =
            new SorcererMetamagicCatalogue();

        self::assertSame(
            1,
            $catalogue->cost(
                'twinned-spell',
                0
            )
        );

        self::assertSame(
            1,
            $catalogue->cost(
                'twinned-spell',
                1
            )
        );

        self::assertSame(
            5,
            $catalogue->cost(
                'twinned-spell',
                5
            )
        );
    }

    public function testLevelThreeMustChooseExactlyTwoMetamagicOptions(): void
    {
        $service =
            new SorcererMetamagicService();

        self::assertSame(
            [
                'quickened-spell',
                'subtle-spell',
            ],
            $service->validateChoices(
                $this->sorcerer(3),
                [
                    'subtle-spell',
                    'quickened-spell',
                ]
            )
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $service->validateChoices(
            $this->sorcerer(3),
            ['subtle-spell']
        );
    }

    public function testLevelTenMustChooseExactlyThreeMetamagicOptions(): void
    {
        self::assertCount(
            3,
            (
                new SorcererMetamagicService()
            )->validateChoices(
                $this->sorcerer(10),
                [
                    'careful-spell',
                    'quickened-spell',
                    'subtle-spell',
                ]
            )
        );
    }

    public function testLevelSeventeenMustChooseExactlyFourMetamagicOptions(): void
    {
        self::assertCount(
            4,
            (
                new SorcererMetamagicService()
            )->validateChoices(
                $this->sorcerer(17),
                [
                    'careful-spell',
                    'distant-spell',
                    'quickened-spell',
                    'subtle-spell',
                ]
            )
        );
    }

    public function testMetamagicCannotBeSelectedBeforeLevelThree(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererMetamagicService()
        )->validateChoices(
            $this->sorcerer(2),
            [
                'quickened-spell',
                'subtle-spell',
            ]
        );
    }

    public function testUnknownMetamagicCannotBeCertified(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererMetamagicService()
        )->validateChoices(
            $this->sorcerer(3),
            [
                'quickened-spell',
                'mystery-spell',
            ]
        );
    }

    public function testUsingQuickenedSpellSpendsTwoSorceryPoints(): void
    {
        $sorcerer =
            $this->sorcerer(5);

        $next = (
            new SorcererMetamagicService()
        )->use(
            $sorcerer,
            ActiveClassResourceState::fresh(),
            [
                'quickened-spell',
                'subtle-spell',
            ],
            'quickened-spell'
        );

        self::assertSame(
            2,
            $next->expended(
                SorcererSorceryReserveService::RESOURCE
            )
        );
    }

    public function testUsingTwinnedSpellSpendsSpellLevelSorceryPoints(): void
    {
        $next = (
            new SorcererMetamagicService()
        )->use(
            $this->sorcerer(8),
            ActiveClassResourceState::fresh(),
            [
                'twinned-spell',
                'subtle-spell',
            ],
            'twinned-spell',
            4
        );

        self::assertSame(
            4,
            $next->expended(
                SorcererSorceryReserveService::RESOURCE
            )
        );
    }

    public function testUnselectedMetamagicCannotBeUsed(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererMetamagicService()
        )->use(
            $this->sorcerer(5),
            ActiveClassResourceState::fresh(),
            [
                'quickened-spell',
                'subtle-spell',
            ],
            'heightened-spell'
        );
    }

    public function testMetamagicCannotSpendMoreSorceryPointsThanRemain(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (
            new SorcererMetamagicService()
        )->use(
            $this->sorcerer(3),
            ActiveClassResourceState::fromArray([
                SorcererSorceryReserveService::RESOURCE => 2,
            ]),
            [
                'heightened-spell',
                'subtle-spell',
            ],
            'heightened-spell'
        );
    }

    public function testOriginRegisterReceivesSelectedMetamagicArts(): void
    {
        $register = (
            new SorcererOriginRegisterPresenter()
        )->present(
            $this->sorcerer(10),
            ActiveClassResourceState::fresh(),
            [
                'careful-spell',
                'quickened-spell',
                'subtle-spell',
            ]
        );

        self::assertSame(
            3,
            $register[
                'metamagic'
            ]['known']
        );

        self::assertSame(
            [
                'careful-spell',
                'quickened-spell',
                'subtle-spell',
            ],
            $register[
                'metamagic'
            ]['selected_keys']
        );

        self::assertCount(
            3,
            $register[
                'metamagic'
            ]['selected']
        );
    }

    public function testControllerLoadsPersistsAndUsesMetamagicChoices(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'SorcererMetamagicRepository',
            $controller
        );

        self::assertStringContainsString(
            'saveMetamagicChoices',
            $controller
        );

        self::assertStringContainsString(
            'useMetamagic',
            $controller
        );

        self::assertStringContainsString(
            'SorcererMetamagicService',
            $controller
        );
    }

    public function testMetamagicRoutesUseDedicatedNonceBridge(): void
    {
        $routes = $this->source(
            'app/Modules/Characters/Routes.php'
        );

        self::assertStringContainsString(
            '/metamagic/choices',
            $routes
        );

        self::assertStringContainsString(
            '/metamagic/use',
            $routes
        );

        $provider = $this->source(
            'app/Providers/FrontendServiceProvider.php'
        );

        self::assertStringContainsString(
            'metamagic/(?:choices|use)',
            $provider
        );

        self::assertStringContainsString(
            'gmrc_character_metamagic_',
            $provider
        );
    }

    public function testMetamagicRepositoryUsesOwnerScopedCharacterMeta(): void
    {
        $repository = $this->source(
            'app/Modules/Characters/ActivePlay/'
            . 'Repositories/'
            . 'SorcererMetamagicRepository.php'
        );

        self::assertStringContainsString(
            '_gmrc_sorcerer_metamagic',
            $repository
        );

        self::assertStringContainsString(
            "'author' =>",
            $repository
        );

        self::assertStringContainsString(
            'get_current_user_id()',
            $repository
        );
    }

    public function testLedgerShowsSelectionAndActiveUseSurfaces(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-metamagic-arts',
            $view
        );

        self::assertStringContainsString(
            'data-metamagic-choices',
            $view
        );

        self::assertStringContainsString(
            'Save Metamagic Arts',
            $view
        );

        self::assertStringContainsString(
            'data-metamagic-active',
            $view
        );

        self::assertStringContainsString(
            'data-metamagic-use=',
            $view
        );
    }

    public function testTwinnedSpellUiCollectsSpellLevelForVariableCost(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Spell level',
            $view
        );

        self::assertStringContainsString(
            'name="spell_level"',
            $view
        );

        self::assertStringContainsString(
            'Cantrip — 1 point',
            $view
        );
    }

    public function testMetamagicPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-metamagic-arts',
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

    private function sorcerer(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Metamagic Arts Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'sorcerer'
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

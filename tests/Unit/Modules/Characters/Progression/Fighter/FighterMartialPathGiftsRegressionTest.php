<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathGiftFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Martial\Services\FighterMartialRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\FighterMartialPathGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use PHPUnit\Framework\TestCase;

final class FighterMartialPathGiftsRegressionTest extends TestCase
{
    public function testAllSixRegisteredFighterPathsHaveGiftProgressions(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertTrue(
                $catalogue->supports($path)
            );

            self::assertCount(
                5,
                $catalogue->all($path)
            );
        }
    }

    public function testEachFighterPathUsesTheStandardMartialGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertSame(
                [3, 7, 10, 15, 18],
                array_column(
                    $catalogue->all($path),
                    'level'
                )
            );
        }
    }

    public function testSelectingCarverAtLevelThreeRevealsItsFirstGift(): void
    {
        $folio = (
            new PathGiftFolio()
        )->build(
            $this->fighter(
                level: 2
            ),
            3,
            [
                'fighter-martial-path' => [
                    'the-carver',
                ],
            ]
        );

        self::assertNotNull($folio);

        $state = $folio->toArray();

        self::assertSame(
            'path-gifts',
            $state['key']
        );

        self::assertTrue(
            $state['ready']
        );

        self::assertSame(
            ['carvers-flourish'],
            $state['facts']['gift_keys']
        );
    }

    public function testCertifiedLevelThreeGiftIsNotOfferedTwice(): void
    {
        self::assertNull(
            (
                new PathGiftFolio()
            )->build(
                $this->fighter(
                    level: 3,
                    path: 'the-carver',
                    gifts: [
                        'carvers-flourish',
                    ]
                ),
                4
            )
        );
    }

    public function testLevelSevenUnlocksOnlyNextCarverGift(): void
    {
        $state = (
            new PathGiftFolio()
        )->build(
            $this->fighter(
                level: 6,
                path: 'the-carver',
                gifts: [
                    'carvers-flourish',
                ]
            ),
            7
        )->toArray();

        self::assertSame(
            ['engraved-guard'],
            $state['facts']['gift_keys']
        );

        self::assertFalse(
            $state['facts']['catch_up']
        );
    }

    public function testLaterFighterPathGiftMilestonesUnlockOneAtATime(): void
    {
        $catalogue = new PathGiftCatalogue();
        $known = PathGifts::none();

        $expected = [
            3 => 'aisle-watch',
            7 => 'stockroom-intercept',
            10 => 'hold-the-aisle',
            15 => 'sentinels-warning',
            18 => 'unbroken-shelf',
        ];

        foreach ($expected as $level => $giftKey) {
            $unlocked = $catalogue->unlocked(
                'shelf-sentinel',
                $level,
                $known
            );

            $latest = $unlocked[
                count($unlocked) - 1
            ];

            self::assertSame(
                $giftKey,
                $latest['key']
            );

            $known = $known->grant(
                array_column(
                    $unlocked,
                    'key'
                )
            );
        }
    }

    public function testMartialRegisterShowsOnlyCertifiedPathGifts(): void
    {
        $fighter = $this->fighter(
            level: 10,
            path: 'the-carver',
            gifts: [
                'carvers-flourish',
                'engraved-guard',
            ]
        );

        $state = (
            new FighterMartialRegisterPresenter()
        )->present($fighter);

        self::assertSame(
            [
                'carvers-flourish',
                'engraved-guard',
            ],
            array_column(
                $state['path']['gifts'],
                'key'
            )
        );

        self::assertNotContains(
            'signature-cut',
            array_column(
                $state['path']['gifts'],
                'key'
            )
        );
    }

    public function testAllFighterPathGiftsAreAutomatic(): void
    {
        foreach (
            FighterMartialPathGiftProgression::allDefinitions()
            as $definition
        ) {
            self::assertSame(
                ['automatic'],
                array_values(
                    array_unique(
                        array_column(
                            $definition->gifts(),
                            'mode'
                        )
                    )
                )
            );
        }
    }

    public function testFighterLevelThreeCallingDelegatesPathAndPathGifts(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Progression/'
            . 'Definitions/Classes/'
            . 'FighterProgression.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            "'folio' => 'path'",
            $source
        );

        self::assertStringContainsString(
            "'folio' => 'path-gifts'",
            $source
        );

        self::assertStringContainsString(
            "'phase' => 'III.12.2B'",
            $source
        );
    }

    public function testMartialRegisterPresentsCertifiedPathGiftCards(): void
    {
        $view = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            'Certified Martial Path Gifts',
            $view
        );

        self::assertStringContainsString(
            'gmrc-martial-register__path-gifts',
            $view
        );

        self::assertStringContainsString(
            "['path']['gifts']",
            $view
        );
    }

    public function testPathGiftPresentationRemainsResponsive(): void
    {
        $css = file_get_contents(
            $this->root()
            . '/assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertIsString($css);

        self::assertStringContainsString(
            '.gmrc-martial-register__path-gifts',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 700px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    public function testPathGiftCatalogueKeepsExistingShelfmancyDefinition(): void
    {
        $catalogue = new PathGiftCatalogue();

        self::assertTrue(
            $catalogue->supports(
                'school-of-shelfmancy'
            )
        );

        self::assertTrue(
            $catalogue->supports(
                'cutlery-knight'
            )
        );
    }

    /**
     * @return array<int,string>
     */
    private function paths(): array
    {
        return [
            'discontinued-lineage',
            'butcher',
            'the-carver',
            'cutlery-knight',
            'the-vineblade',
            'shelf-sentinel',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function fighter(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Martial Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'fighter'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
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

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

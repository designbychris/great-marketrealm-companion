<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Primal\Services\BarbarianRageRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class BarbarianPrimalPathsRegressionTest extends TestCase
{
    public function testAllEightBarbarianPathsHaveFourGiftMilestones(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertTrue(
                $catalogue->supports($path)
            );

            self::assertSame(
                [3, 6, 10, 14],
                array_column(
                    $catalogue->all($path),
                    'level'
                )
            );
        }
    }

    public function testButcheredRageHasExpectedGiftProgression(): void
    {
        $gifts = (new PathGiftCatalogue())
            ->all(
                'path-of-the-butchered-rage'
            );

        self::assertSame(
            [
                'bloodied-cleaver',
                'butchers-instinct',
                'carving-frenzy',
                'slaughterhouse-fury',
            ],
            array_column(
                $gifts,
                'key'
            )
        );
    }

    public function testLevelThreeCanGrantFirstButcheredRageGift(): void
    {
        $folio = (new PathGiftFolio())
            ->build(
                $this->barbarian(2),
                3,
                [
                    'barbarian-primal-path' => [
                        'path-of-the-butchered-rage',
                    ],
                ]
            );

        self::assertNotNull($folio);

        self::assertSame(
            ['bloodied-cleaver'],
            $folio->toArray()[
                'facts'
            ]['gift_keys']
        );
    }

    public function testLaterGiftMilestonesUnlockInOrder(): void
    {
        $catalogue = new PathGiftCatalogue();
        $known = PathGifts::none();

        foreach ([
            3 => 'bloodied-cleaver',
            6 => 'butchers-instinct',
            10 => 'carving-frenzy',
            14 => 'slaughterhouse-fury',
        ] as $level => $expected) {
            $unlocked = $catalogue->unlocked(
                'path-of-the-butchered-rage',
                $level,
                $known
            );

            self::assertSame(
                $expected,
                $unlocked[
                    count($unlocked) - 1
                ]['key']
            );

            $known = $known->grant(
                array_column(
                    $unlocked,
                    'key'
                )
            );
        }
    }

    public function testPathChoicesNowCarryPlayerGuidance(): void
    {
        $choices = (new PathCandidateCatalogue())
            ->forClass(
                CharacterClass::fromString(
                    'barbarian'
                )
            );

        $butchered = array_values(
            array_filter(
                $choices,
                static fn (array $choice): bool =>
                    $choice['key']
                    === 'path-of-the-butchered-rage'
            )
        )[0];

        self::assertStringContainsString(
            'close-quarters',
            $butchered['playstyle']
        );

        self::assertStringContainsString(
            'Butcher Isles',
            $butchered['best_for']
        );

        self::assertStringContainsString(
            'merciless momentum',
            $butchered['identity']
        );

        self::assertCount(
            4,
            $butchered['gift_preview']
        );
    }

    public function testEveryBarbarianPathHasMeaningfulChoiceGuidance(): void
    {
        $choices = (new PathCandidateCatalogue())
            ->forClass(
                CharacterClass::fromString(
                    'barbarian'
                )
            );

        self::assertCount(
            8,
            $choices
        );

        foreach ($choices as $choice) {
            self::assertNotSame(
                '',
                $choice['playstyle']
            );

            self::assertNotSame(
                '',
                $choice['best_for']
            );

            self::assertNotSame(
                '',
                $choice['identity']
            );

            self::assertCount(
                4,
                $choice['gift_preview']
            );
        }
    }

    public function testChoiceViewExplainsPlaystyleBestFitAndGiftPreview(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/'
            . 'advancement.php'
        );

        self::assertStringContainsString(
            'Playstyle:',
            $source
        );

        self::assertStringContainsString(
            'Best for:',
            $source
        );

        self::assertStringContainsString(
            'Preview Path Gifts',
            $source
        );
    }

    public function testCertifiedButcheredRageGiftAppearsInRageRegister(): void
    {
        $fighter = $this->barbarian(
            3,
            'path-of-the-butchered-rage',
            ['bloodied-cleaver']
        );

        $state = (
            new BarbarianRageRegisterPresenter()
        )->present($fighter);

        self::assertSame(
            ['bloodied-cleaver'],
            array_column(
                $state['path_gifts'],
                'key'
            )
        );
    }

    public function testFutureGiftDoesNotAppearBeforeCertification(): void
    {
        $state = (
            new BarbarianRageRegisterPresenter()
        )->present(
            $this->barbarian(
                10,
                'path-of-the-butchered-rage',
                [
                    'bloodied-cleaver',
                    'butchers-instinct',
                ]
            )
        );

        self::assertNotContains(
            'carving-frenzy',
            array_column(
                $state['path_gifts'],
                'key'
            )
        );
    }

    public function testRageRegisterRendersCertifiedPrimalPathGifts(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Certified Primal Path Gifts',
            $source
        );

        self::assertStringContainsString(
            'gmrc-rage-register__path-gifts',
            $source
        );
    }

    public function testChoiceGuidanceIsSharedInfrastructureNotBarbarianViewHack(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Paths/Services/'
            . 'PathCandidateCatalogue.php'
        );

        self::assertStringContainsString(
            'PathChoiceGuideCatalogue',
            $source
        );

        self::assertStringContainsString(
            "'gift_preview'",
            $source
        );
    }

    public function testPrimalPathPresentationRemainsResponsive(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-path-choice-guide__gifts',
            $source
        );

        self::assertStringContainsString(
            '.gmrc-rage-register__path-gifts',
            $source
        );

        self::assertStringContainsString(
            '@media (forced-colors:active)',
            $source
        );
    }

    /**
     * @return array<int,string>
     */
    private function paths(): array
    {
        return [
            'path-of-the-great-tony',
            'path-of-the-expired',
            'path-of-the-marbled-rage',
            'path-of-the-rind',
            'path-of-the-butchered-rage',
            'path-of-the-sugarrush',
            'path-of-the-pickled-rage',
            'path-of-the-butterbound',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function barbarian(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Primal Path Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'barbarian'
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

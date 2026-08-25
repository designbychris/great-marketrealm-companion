<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredPolicy;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Sacred\Services\PaladinSacredRegisterPresenter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinSacredRegisterRegressionTest extends TestCase
{
    public function testNonPaladinIsUnsupported(): void
    {
        self::assertFalse(
            (new PaladinSacredRegisterPresenter())
                ->present(
                    $this->character('fighter', 3)
                )['supported']
        );
    }

    public function testLayOnHandsPoolScalesAtFivePerPaladinLevel(): void
    {
        $policy = new PaladinSacredPolicy();

        self::assertSame(
            5,
            $policy->layOnHandsMaximum(
                $this->paladin(1)
            )
        );

        self::assertSame(
            20,
            $policy->layOnHandsMaximum(
                $this->paladin(4)
            )
        );

        self::assertSame(
            100,
            $policy->layOnHandsMaximum(
                $this->paladin(20)
            )
        );
    }

    public function testSacredSaveDcUsesCharismaAndProficiency(): void
    {
        $paladin = $this->paladin(5);

        self::assertSame(
            8
            + $paladin->proficiencyBonus()->value()
            + $paladin->abilityScores()->charisma()->modifier(),
            (new PaladinSacredPolicy())
                ->sacredSaveDc($paladin)
        );
    }

    public function testDivineSenseHasAtLeastOneUse(): void
    {
        self::assertGreaterThanOrEqual(
            1,
            (new PaladinSacredPolicy())
                ->divineSenseMaximum(
                    $this->paladin(1)
                )
        );
    }

    public function testAuraRangeUnlocksAtSixAndExpandsAtEighteen(): void
    {
        $policy = new PaladinSacredPolicy();

        self::assertSame(
            0,
            $policy->auraRangeFeet(
                $this->paladin(5)
            )
        );

        self::assertSame(
            10,
            $policy->auraRangeFeet(
                $this->paladin(6)
            )
        );

        self::assertSame(
            30,
            $policy->auraRangeFeet(
                $this->paladin(18)
            )
        );
    }

    public function testCleansingTouchUnlocksAtFourteen(): void
    {
        $policy = new PaladinSacredPolicy();

        self::assertSame(
            0,
            $policy->cleansingTouchMaximum(
                $this->paladin(13)
            )
        );

        self::assertGreaterThanOrEqual(
            1,
            $policy->cleansingTouchMaximum(
                $this->paladin(14)
            )
        );
    }

    public function testRegisterTracksCoreUnlocks(): void
    {
        $presenter =
            new PaladinSacredRegisterPresenter();

        $levelOne = $presenter->present(
            $this->paladin(1)
        );

        $levelTwo = $presenter->present(
            $this->paladin(2)
        );

        $levelSix = $presenter->present(
            $this->paladin(6)
        );

        self::assertFalse(
            $this->feature(
                $levelOne,
                'divine-smite'
            )['unlocked']
        );

        self::assertTrue(
            $this->feature(
                $levelTwo,
                'divine-smite'
            )['unlocked']
        );

        self::assertTrue(
            $this->feature(
                $levelSix,
                'aura-of-protection'
            )['unlocked']
        );
    }

    public function testSacredOathOpensAtLevelThree(): void
    {
        $presenter =
            new PaladinSacredRegisterPresenter();

        self::assertSame(
            'Opens at Level 3',
            $presenter
                ->present(
                    $this->paladin(2)
                )['oath']['label']
        );

        self::assertSame(
            'Awaiting Sacred Oath',
            $presenter
                ->present(
                    $this->paladin(3)
                )['oath']['label']
        );
    }

    public function testGrandCatalogueNowContainsAllEightPaladinOaths(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'paladin'
            )
        );

        self::assertCount(
            8,
            $candidates
        );

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
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testAllEightOathsHaveCreationChoiceGuidance(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'paladin'
            )
        );

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['identity']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['playstyle']
                        ?? ''
                    )
                )
            );

            self::assertNotSame(
                '',
                trim(
                    (string) (
                        $candidate['best_for']
                        ?? ''
                    )
                )
            );
        }
    }

    public function testCatalogueVersionInvalidatesThreeOathSnapshot(): void
    {
        $repository = $this->source(
            'app/Modules/Characters/Catalogue/'
            . 'Repositories/'
            . 'CharacterCatalogueRepository.php'
        );

        self::assertStringContainsString(
            "private const VERSION = '3.7.5';",
            $repository
        );

        $catalogue = json_decode(
            $this->source(
                'resources/catalogue/'
                . 'players-handbook.v1.json'
            ),
            true
        );

        self::assertIsArray($catalogue);

        self::assertSame(
            '3.7.5',
            $catalogue['version']
        );
    }

    public function testControllerAndLedgerReceiveSacredRegister(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'PaladinSacredRegisterPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'sacredRegister' => \$sacredRegister",
            $controller
        );

        self::assertStringContainsString(
            'The Sacred Register',
            $view
        );

        self::assertStringContainsString(
            'data-sacred-register',
            $view
        );

        self::assertStringContainsString(
            'aria-labelledby="gmrc-sacred-register-title"',
            $view
        );
    }

    public function testSacredRegisterCanBeExtendedByActivePlaySlices(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-sacred-actions',
            $view
        );

        self::assertStringContainsString(
            'data-sacred-action=',
            $view
        );

        self::assertStringContainsString(
            'data-sacred-rest',
            $view
        );
    }

    public function testPolicyRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinSacredPolicy())
            ->layOnHandsMaximum(
                $this->character(
                    'rogue',
                    4
                )
            );
    }

    private function feature(
        array $state,
        string $key
    ): array {
        foreach (
            $state['features']
            as $feature
        ) {
            if (
                ($feature['key'] ?? '')
                === $key
            ) {
                return $feature;
            }
        }

        self::fail(
            'Expected Paladin feature was not present.'
        );
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
                'Sacred Register Tester'
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

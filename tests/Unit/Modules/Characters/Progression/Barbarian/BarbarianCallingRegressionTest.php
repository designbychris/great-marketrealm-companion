<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Barbarian;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\BarbarianProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BarbarianCallingRegressionTest extends TestCase
{
    public function testBarbarianUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('barbarian'),
                2
            );

        self::assertSame(
            'barbarian',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelTwoRecordsRecklessAttackAndDangerSense(): void
    {
        $entry = (new BarbarianProgression())
            ->forLevel(
                CharacterClass::fromString('barbarian'),
                2
            );

        self::assertSame(
            [
                'reckless-attack',
                'danger-sense',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testLevelThreeDelegatesPrimalPathChoice(): void
    {
        $entry = (new BarbarianProgression())
            ->forLevel(
                CharacterClass::fromString('barbarian'),
                3
            );

        self::assertSame(
            [
                'path',
                'path-gifts',
            ],
            array_column(
                $entry['delegated'],
                'folio'
            )
        );

        self::assertSame(
            'primal-path',
            $entry['delegated'][0]['key']
        );

        self::assertSame(
            'primal-path-gifts',
            $entry['delegated'][1]['key']
        );
    }

    public function testBarbarianPathIsChosenAtLevelThree(): void
    {
        $definition = (new PathProgressionCatalogue())
            ->forClass(
                CharacterClass::fromString('barbarian')
            );

        self::assertIsArray($definition);

        self::assertSame(
            'Primal Path',
            $definition['label']
        );

        self::assertSame(
            'barbarian-primal-path',
            $definition['choice_key']
        );

        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testFreshRepositoryContainsEightBarbarianPaths(): void
    {
        $candidates = (new PathCandidateCatalogue())
            ->forClass(
                CharacterClass::fromString('barbarian')
            );

        self::assertCount(
            8,
            $candidates
        );

        self::assertSame(
            [
                'path-of-the-great-tony',
                'path-of-the-expired',
                'path-of-the-marbled-rage',
                'path-of-the-rind',
                'path-of-the-butchered-rage',
                'path-of-the-sugarrush',
                'path-of-the-pickled-rage',
                'path-of-the-butterbound',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testExtraAttackArrivesAtLevelFive(): void
    {
        $entry = (new BarbarianProgression())
            ->forLevel(
                CharacterClass::fromString('barbarian'),
                5
            );

        self::assertSame(
            [
                'extra-attack',
                'fast-movement',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            2,
            $entry['automatic'][0]['attacks']
        );
    }

    public function testBrutalCriticalReferenceScalesAcrossMilestones(): void
    {
        $progression = new BarbarianProgression();
        $barbarian = CharacterClass::fromString('barbarian');

        self::assertSame(
            1,
            $progression
                ->forLevel(
                    $barbarian,
                    9
                )['automatic'][0]['extra_dice']
        );

        self::assertSame(
            2,
            $progression
                ->forLevel(
                    $barbarian,
                    13
                )['automatic'][0]['extra_dice']
        );

        self::assertSame(
            3,
            $progression
                ->forLevel(
                    $barbarian,
                    17
                )['automatic'][0]['extra_dice']
        );
    }

    public function testGrowthMilestonesDelegateToSharedGrowthFolio(): void
    {
        $progression = new BarbarianProgression();
        $barbarian = CharacterClass::fromString('barbarian');

        foreach (
            [4, 8, 12, 16, 19]
            as $level
        ) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $barbarian,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testFuturePrimalPathGiftMilestonesAreReserved(): void
    {
        $progression = new BarbarianProgression();
        $barbarian = CharacterClass::fromString('barbarian');

        foreach (
            [6, 10, 14]
            as $level
        ) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $barbarian,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testBarbarianBecomesSpecialistWithoutSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('barbarian')
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

    public function testBarbarianProgressionRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new BarbarianProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testBarbarianProgressionRejectsInvalidLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new BarbarianProgression())
            ->forLevel(
                CharacterClass::fromString('barbarian'),
                21
            );
    }

    public function testExistingRageFeatureRemainsInSharedAbilityCatalogue(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Arcana/Models/'
            . 'ArcaneAbilityCatalogue.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            "'rage'",
            $source
        );

        self::assertStringContainsString(
            "['barbarian']",
            $source
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

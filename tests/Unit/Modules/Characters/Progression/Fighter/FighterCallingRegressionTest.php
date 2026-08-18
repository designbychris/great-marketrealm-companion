<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Fighter;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\FighterProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FighterCallingRegressionTest extends TestCase
{
    public function testFighterUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );

        self::assertSame(
            'fighter',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelTwoRecordsActionSurgeAsAutomaticCallingGain(): void
    {
        $entry = (new FighterProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );

        self::assertSame(
            ['action-surge'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            'Action Surge',
            $entry['automatic'][0]['label']
        );
    }

    public function testLevelThreeDelegatesMartialPathChoice(): void
    {
        $entry = (new FighterProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
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
            'martial-path',
            $entry['delegated'][0]['key']
        );

        self::assertSame(
            'martial-path-gifts',
            $entry['delegated'][1]['key']
        );
    }

    public function testFighterPathIsChosenAtLevelThree(): void
    {
        $definition = (new PathProgressionCatalogue())
            ->forClass(
                CharacterClass::fromString('fighter')
            );

        self::assertIsArray($definition);

        self::assertSame(
            'Martial Path',
            $definition['label']
        );

        self::assertSame(
            'fighter-martial-path',
            $definition['choice_key']
        );

        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testFighterCatalogueAlreadyContainsMartialPathCandidates(): void
    {
        $candidates = (new PathCandidateCatalogue())
            ->forClass(
                CharacterClass::fromString('fighter')
            );

        self::assertCount(
            6,
            $candidates
        );

        self::assertSame(
            [
                'discontinued-lineage',
                'butcher',
                'the-carver',
                'cutlery-knight',
                'the-vineblade',
                'shelf-sentinel',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testExtraAttackMilestonesRemainCallingReferenceMetadata(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString('fighter');

        $levelFive = $progression->forLevel(
            $fighter,
            5
        );

        $levelEleven = $progression->forLevel(
            $fighter,
            11
        );

        $levelTwenty = $progression->forLevel(
            $fighter,
            20
        );

        self::assertSame(
            2,
            $levelFive['automatic'][0]['attacks']
        );

        self::assertSame(
            3,
            $levelEleven['automatic'][0]['attacks']
        );

        self::assertSame(
            4,
            $levelTwenty['automatic'][0]['attacks']
        );
    }

    public function testIndomitableAndActionSurgeScalingAreRecorded(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString('fighter');

        $levelThirteen = $progression->forLevel(
            $fighter,
            13
        );

        $levelSeventeen = $progression->forLevel(
            $fighter,
            17
        );

        self::assertSame(
            2,
            $levelThirteen['automatic'][0]['uses']
        );

        self::assertSame(
            [
                'action-surge',
                'indomitable',
            ],
            array_column(
                $levelSeventeen['automatic'],
                'key'
            )
        );

        self::assertSame(
            [2, 3],
            array_column(
                $levelSeventeen['automatic'],
                'uses'
            )
        );
    }

    public function testFighterGrowthMilestonesDelegateToSharedGrowthFolio(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString('fighter');

        foreach ([
            4,
            6,
            8,
            12,
            14,
            16,
            19,
        ] as $level) {
            $entry = $progression->forLevel(
                $fighter,
                $level
            );

            self::assertContains(
                'growth',
                array_column(
                    $entry['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testFighterPathFeatureMilestonesAreReservedForPathGiftPass(): void
    {
        $progression = new FighterProgression();
        $fighter = CharacterClass::fromString('fighter');

        foreach ([7, 10, 15, 18] as $level) {
            $entry = $progression->forLevel(
                $fighter,
                $level
            );

            self::assertSame(
                'path-gifts',
                $entry['delegated'][0]['folio']
            );

            self::assertSame(
                'III.12.2B',
                $entry['delegated'][0]['phase']
            );
        }
    }

    public function testFighterProgressionRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new FighterProgression())
            ->forLevel(
                CharacterClass::fromString('wizard'),
                2
            );
    }

    public function testFighterProgressionRejectsOutOfRangeLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new FighterProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                21
            );
    }

    public function testFighterDoesNotBecomeSpellcasterBySpecialisation(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Progression/'
            . 'Spellcasting/Models/'
            . 'SpellcastingProgressionCatalogue.php'
        );

        self::assertIsString($source);

        self::assertStringNotContainsString(
            'FighterSpellcastingProgression',
            $source
        );
    }

    public function testCallingFolioCanExplainAutomaticCallingGains(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Modules/Characters/Progression/'
            . 'Folios/CallingFolio.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'automatic Calling',
            $source
        );

        self::assertStringContainsString(
            "'automatic_gains' =>",
            $source
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

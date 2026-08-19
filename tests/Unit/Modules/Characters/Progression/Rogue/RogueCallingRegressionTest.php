<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\RogueProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RogueCallingRegressionTest extends TestCase
{
    public function testRogueUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('rogue'),
                2
            );

        self::assertSame(
            'rogue',
            $entry['class']
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelTwoRecordsCunningAction(): void
    {
        $entry = (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('rogue'),
                2
            );

        self::assertSame(
            ['cunning-action'],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testLevelThreeDelegatesRogueArchetypeChoice(): void
    {
        $entry = (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('rogue'),
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
            'rogue-archetype',
            $entry['delegated'][0]['key']
        );

        self::assertSame(
            'rogue-archetype-feature',
            $entry['delegated'][1]['key']
        );
    }

    public function testRogueArchetypeIsChosenAtLevelThree(): void
    {
        $definition = (new PathProgressionCatalogue())
            ->forClass(
                CharacterClass::fromString('rogue')
            );

        self::assertIsArray($definition);

        self::assertSame(
            'Rogue Archetype',
            $definition['label']
        );

        self::assertSame(
            'rogue-archetype',
            $definition['choice_key']
        );

        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testFreshRepositoryContainsSixRogueArchetypes(): void
    {
        $candidates = (new PathCandidateCatalogue())
            ->forClass(
                CharacterClass::fromString('rogue')
            );

        self::assertCount(
            6,
            $candidates
        );

        self::assertSame(
            [
                'the-cheetoblade',
                'spiceblade',
                'the-breadknife',
                'mastermind-of-the-aisles',
                'aisle-stalker',
                'taffy-trickster',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testSneakAttackReferenceScalesAcrossOddLevels(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString(
            'rogue'
        );

        self::assertSame(
            '2d6',
            $progression
                ->forLevel(
                    $rogue,
                    3
                )['automatic'][0]['dice']
        );

        self::assertSame(
            '6d6',
            $progression
                ->forLevel(
                    $rogue,
                    11
                )['automatic'][0]['dice']
        );

        self::assertSame(
            '10d6',
            $progression
                ->forLevel(
                    $rogue,
                    19
                )['automatic'][0]['dice']
        );
    }

    public function testLevelFiveRecordsSneakAttackAndUncannyDodge(): void
    {
        $entry = (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('rogue'),
                5
            );

        self::assertSame(
            [
                'sneak-attack',
                'uncanny-dodge',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testLaterRogueMilestonesRemainRepresented(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString(
            'rogue'
        );

        $expected = [
            6 => ['expertise'],
            7 => [
                'sneak-attack',
                'evasion',
            ],
            11 => [
                'sneak-attack',
                'reliable-talent',
            ],
            14 => ['blindsense'],
            15 => [
                'sneak-attack',
                'slippery-mind',
            ],
            18 => ['elusive'],
            20 => ['stroke-of-luck'],
        ];

        foreach (
            $expected
            as $level => $keys
        ) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthMilestonesDelegateToSharedGrowthFolio(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString(
            'rogue'
        );

        foreach (
            [4, 8, 10, 12, 16, 19]
            as $level
        ) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testFutureArchetypeGiftMilestonesAreReserved(): void
    {
        $progression = new RogueProgression();
        $rogue = CharacterClass::fromString(
            'rogue'
        );

        foreach (
            [9, 13, 17]
            as $level
        ) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $rogue,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testRogueBecomesSpecialistWithoutBaselineSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('rogue')
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

    public function testRogueProgressionRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testRogueProgressionRejectsInvalidLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new RogueProgression())
            ->forLevel(
                CharacterClass::fromString('rogue'),
                21
            );
    }
}

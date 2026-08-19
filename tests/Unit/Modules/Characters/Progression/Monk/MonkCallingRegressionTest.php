<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Audit\ClassCapabilityProfile;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\MonkProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MonkCallingRegressionTest extends TestCase
{
    public function testMonkUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('monk'),
                2
            );

        self::assertSame('monk', $entry['class']);
        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testLevelTwoRecordsDisciplineAndMovement(): void
    {
        $entry = (new MonkProgression())
            ->forLevel(
                CharacterClass::fromString('monk'),
                2
            );

        self::assertSame(
            [
                'discipline',
                'unarmoured-movement',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testLevelThreeDelegatesMonasticWayChoice(): void
    {
        $entry = (new MonkProgression())
            ->forLevel(
                CharacterClass::fromString('monk'),
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
            'monastic-way',
            $entry['delegated'][0]['key']
        );

        self::assertSame(
            'monastic-way-gift',
            $entry['delegated'][1]['key']
        );
    }

    public function testMonasticWayIsChosenAtLevelThree(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('monk')
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Monastic Way',
            $definition['label']
        );

        self::assertSame(
            'monastic-way',
            $definition['choice_key']
        );

        self::assertSame(
            3,
            $definition['selection_level']
        );
    }

    public function testFreshRepositoryContainsSixMonasticWays(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('monk')
        );

        self::assertCount(6, $candidates);

        self::assertSame(
            [
                'way-of-the-spun-cloud',
                'way-of-the-neon-crunch',
                'way-of-the-vacuum-seal',
                'way-of-the-simmering-soul',
                'way-of-the-whirling-utensil',
                'way-of-the-spongecake-soul',
            ],
            array_column(
                $candidates,
                'key'
            )
        );
    }

    public function testLevelFiveRecordsExtraAttackAndStunningStrike(): void
    {
        $entry = (new MonkProgression())
            ->forLevel(
                CharacterClass::fromString('monk'),
                5
            );

        self::assertSame(
            [
                'extra-attack',
                'stunning-strike',
            ],
            array_column(
                $entry['automatic'],
                'key'
            )
        );
    }

    public function testLaterMonkMilestonesRemainRepresented(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        $expected = [
            6 => ['disciplined-strikes'],
            7 => [
                'evasion',
                'stillness-of-mind',
            ],
            10 => ['purity-of-body'],
            13 => ['tongue-of-sun-and-moon'],
            14 => ['diamond-soul'],
            15 => ['timeless-body'],
            18 => ['empty-body'],
            20 => ['perfect-self'],
        ];

        foreach ($expected as $level => $keys) {
            self::assertSame(
                $keys,
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['automatic'],
                    'key'
                )
            );
        }
    }

    public function testGrowthMilestonesDelegateToSharedGrowthFolio(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testFutureWayGiftMilestonesAreReserved(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        foreach ([6, 11, 17] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $monk,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testMonkBecomesSpecialistWithoutBaselineSpellcasting(): void
    {
        $profile = (new ClassCapabilityCatalogue())
            ->forClass(
                CharacterClass::fromString('monk')
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

    public function testMonkProgressionRejectsAnotherCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new MonkProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testMonkProgressionRejectsInvalidLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new MonkProgression())
            ->forLevel(
                CharacterClass::fromString('monk'),
                21
            );
    }
}

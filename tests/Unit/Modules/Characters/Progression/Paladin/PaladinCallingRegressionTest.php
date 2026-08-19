<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\PaladinProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Models\ClassProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinCallingRegressionTest extends TestCase
{
    public function testPaladinUsesSpecialistProgressionDefinition(): void
    {
        $entry = (new ClassProgressionCatalogue())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                2
            );

        self::assertSame('paladin', $entry['class']);
        self::assertSame('reference', $entry['catalogue_status']);
    }

    public function testLevelOneFoundationsRemainDivineSenseAndLayOnHands(): void
    {
        $foundations = (new PaladinProgression())
            ->foundations(
                CharacterClass::fromString('paladin')
            );

        self::assertSame(
            [
                'divine-sense',
                'lay-on-hands',
            ],
            array_column($foundations, 'key')
        );
    }

    public function testLevelTwoEstablishesMartialSacredHybrid(): void
    {
        $entry = (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                2
            );

        self::assertSame(
            [
                'fighting-style',
                'spellcasting',
                'divine-smite',
            ],
            array_column($entry['automatic'], 'key')
        );
    }

    public function testLevelThreeDelegatesSacredOathAndFirstGift(): void
    {
        $entry = (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                3
            );

        self::assertSame(
            [
                'path',
                'path-gifts',
            ],
            array_column($entry['delegated'], 'folio')
        );

        self::assertSame(
            [
                'sacred-oath',
                'sacred-oath-gift',
            ],
            array_column($entry['delegated'], 'key')
        );
    }

    public function testExtraAttackRemainsLevelFiveMilestone(): void
    {
        $entry = (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                5
            );

        self::assertSame(
            'extra-attack',
            $entry['automatic'][0]['key']
        );

        self::assertSame(
            2,
            $entry['automatic'][0]['attacks']
        );
    }

    public function testAuraMilestonesRecordTheirCertifiedRanges(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        $six = $progression->forLevel($paladin, 6);
        $ten = $progression->forLevel($paladin, 10);
        $eighteen = $progression->forLevel($paladin, 18);

        self::assertSame(
            'aura-of-protection',
            $six['automatic'][0]['key']
        );
        self::assertSame(
            10,
            $six['automatic'][0]['range_feet']
        );

        self::assertSame(
            'aura-of-courage',
            $ten['automatic'][0]['key']
        );
        self::assertSame(
            10,
            $ten['automatic'][0]['range_feet']
        );

        self::assertSame(
            'aura-improvement',
            $eighteen['automatic'][0]['key']
        );
        self::assertSame(
            30,
            $eighteen['automatic'][0]['range_feet']
        );
    }

    public function testLaterCoreMilestonesRemainCallingOwned(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        self::assertSame(
            ['improved-divine-smite'],
            array_column(
                $progression
                    ->forLevel($paladin, 11)['automatic'],
                'key'
            )
        );

        self::assertSame(
            ['cleansing-touch'],
            array_column(
                $progression
                    ->forLevel($paladin, 14)['automatic'],
                'key'
            )
        );
    }

    public function testGrowthMilestonesDelegateToSharedGrowthFolio(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        foreach ([4, 8, 12, 16, 19] as $level) {
            self::assertContains(
                'growth',
                array_column(
                    $progression
                        ->forLevel(
                            $paladin,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testSacredOathGiftCadenceIsReservedForOathPhase(): void
    {
        $progression = new PaladinProgression();
        $paladin = CharacterClass::fromString('paladin');

        foreach ([7, 15, 20] as $level) {
            self::assertSame(
                'path-gifts',
                $progression
                    ->forLevel(
                        $paladin,
                        $level
                    )['delegated'][0]['folio']
            );
        }
    }

    public function testCallingDoesNotPrematurelyClaimPaladinSpellcastingCatalogue(): void
    {
        $entry = (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                2
            );

        self::assertContains(
            'spellcasting',
            array_column(
                $entry['automatic'],
                'key'
            )
        );

        self::assertSame(
            'reference',
            $entry['catalogue_status']
        );
    }

    public function testPaladinProgressionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('fighter'),
                2
            );
    }

    public function testPaladinProgressionRejectsInvalidAdvancementLevel(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                1
            );
    }

    public function testRegisteredFallbackNoLongerOwnsPaladin(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 6)
            . '/app/Modules/Characters/Progression/'
            . 'Models/ClassProgressionCatalogue.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'new PaladinProgression()',
            $source
        );
        self::assertStringContainsString(
            'new RegisteredCallingProgression()',
            $source
        );
        self::assertLessThan(
            strpos(
                $source,
                'new RegisteredCallingProgression()'
            ),
            strpos(
                $source,
                'new PaladinProgression()'
            )
        );
    }
}

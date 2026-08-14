<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WizardProgression;
use PHPUnit\Framework\TestCase;

final class WizardProgressionTest extends TestCase
{
    public function testLevelTwoDelegatesSpellbookAndPath(): void
    {
        $entry = (
            new WizardProgression()
        )->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            2
        );

        self::assertSame(
            [
                'spellbook',
                'path',
                'path-gifts',
            ],
            array_column(
                $entry['delegated'],
                'folio'
            )
        );
    }

    public function testLevelThreeDelegatesArcaneStudies(): void
    {
        $entry = (
            new WizardProgression()
        )->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            3
        );

        self::assertCount(
            1,
            $entry['delegated']
        );

        self::assertSame(
            'Arcane Studies',
            $entry['delegated'][0]['label']
        );
    }

    public function testLevelFourIdentifiesGrowthWithoutOwningTheChoice(): void
    {
        $entry = (
            new WizardProgression()
        )->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            4
        );

        self::assertContains(
            'growth',
            array_column(
                $entry['delegated'],
                'folio'
            )
        );

        self::assertSame(
            [],
            $entry['automatic']
        );
    }

    public function testPathFeatureMilestonesBelongToGiftsOfThePath(): void
    {
        $definition =
            new WizardProgression();

        foreach ([6, 10, 14] as $level) {
            $entry = $definition->forLevel(
                CharacterClass::fromString(
                    'wizard'
                ),
                $level
            );

            self::assertContains(
                'path-gifts',
                array_column(
                    $entry['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testHighLevelWizardMilestonesRemainDelegatedToSpecialists(): void
    {
        $definition =
            new WizardProgression();

        $eighteen = $definition->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            18
        );

        $twenty = $definition->forLevel(
            CharacterClass::fromString(
                'wizard'
            ),
            20
        );

        self::assertSame(
            'Spell Mastery',
            $eighteen['delegated'][0]['label']
        );

        self::assertSame(
            'Signature Spells',
            $twenty['delegated'][0]['label']
        );
    }
}

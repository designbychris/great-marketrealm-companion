<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class WizardPathProgressionTest extends TestCase
{
    public function testWizardPathIsArcaneTraditionAtLevelTwo(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'wizard'
            )
        );

        self::assertIsArray($definition);

        self::assertSame(
            'Arcane Tradition',
            $definition['label']
        );

        self::assertSame(
            2,
            $definition['selection_level']
        );

        self::assertSame(
            'wizard-arcane-tradition',
            $definition['choice_key']
        );
    }

    public function testWizardUsesMarketrealmPathsFromGrandCatalogue(): void
    {
        $options = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'wizard'
            )
        );

        self::assertCount(8, $options);

        self::assertContains(
            'school-of-aromancy',
            array_column(
                $options,
                'key'
            )
        );

        self::assertContains(
            'school-of-preservation',
            array_column(
                $options,
                'key'
            )
        );

        self::assertContains(
            'boneweaver',
            array_column(
                $options,
                'key'
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions\PaladinOathProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaladinPathProgressionRegressionTest extends TestCase
{
    public function testPathCatalogueRecognisesPaladinSacredOathProgression(): void
    {
        $definition = (
            new PathProgressionCatalogue()
        )->forClass(
            CharacterClass::fromString('paladin')
        );

        self::assertIsArray($definition);
        self::assertSame('paladin', $definition['class']);
        self::assertSame('Sacred Oath', $definition['label']);
        self::assertSame('Sacred Oath Folio', $definition['folio_label']);
        self::assertSame('sacred-oath', $definition['choice_key']);
        self::assertSame(3, $definition['selection_level']);
    }

    public function testPaladinOathDefinitionRejectsForeignCalling(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        (new PaladinOathProgression())
            ->definition(
                CharacterClass::fromString('fighter')
            );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Definitions\WizardSpellcastingProgression;
use PHPUnit\Framework\TestCase;

final class WizardSpellcastingProgressionTest extends TestCase
{
    public function testWizardLearnsTwoSpellsAtEachAdvancementLevel(): void
    {
        $definition = new WizardSpellcastingProgression();
        $wizard = CharacterClass::fromString('wizard');

        self::assertSame(2, $definition->forLevel($wizard, 2)['spells_learned']);
        self::assertSame(2, $definition->forLevel($wizard, 4)['maximum_spell_level']);
        self::assertSame(1, $definition->forLevel($wizard, 4)['cantrips_learned']);
        self::assertSame(1, $definition->forLevel($wizard, 10)['cantrips_learned']);
    }
}

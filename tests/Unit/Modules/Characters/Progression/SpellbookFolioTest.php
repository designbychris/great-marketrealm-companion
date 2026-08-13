<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\SpellbookFolio;
use PHPUnit\Framework\TestCase;

final class SpellbookFolioTest extends TestCase
{
    public function testWizardLevelTwoRequiresTwoExistingMarketrealmSpells(): void
    {
        $folio = (new SpellbookFolio())->build($this->character(), 2);
        self::assertNotNull($folio);
        $state = $folio->toArray();
        self::assertFalse($state['ready']);
        self::assertSame(2, $state['facts']['choice_minimum']);
        self::assertSame(
            ['pantry-ward', 'market-missile'],
            array_column(
                $state['choices'],
                'key'
            )
        );

        self::assertSame(
            [1, 1],
            array_column(
                $state['choices'],
                'spell_level'
            )
        );
    }

    public function testTwoRecordedSpellChoicesMakeFolioReady(): void
    {
        $folio = (new SpellbookFolio())->build(
            $this->character(),
            2,
            ['pantry-ward', 'market-missile']
        );
        self::assertNotNull($folio);
        self::assertTrue($folio->toArray()['ready']);
    }

    private function character(): Character
    {
        return Character::create(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            HitPoints::full(8),
            AbilityScores::fromScores(
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(14),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            )
        );
    }
}

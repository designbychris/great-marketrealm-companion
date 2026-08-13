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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\CallingFolio;
use PHPUnit\Framework\TestCase;

final class CallingFolioTest extends TestCase
{
    public function testWizardCallingFolioIsReadyAndDelegatesSpecialistRules(): void
    {
        $character = $this->character();

        $folio = (
            new CallingFolio()
        )->build(
            $character,
            4
        )->toArray();

        self::assertTrue($folio['ready']);
        self::assertFalse(
            $folio['requires_choice']
        );

        self::assertSame(
            'calling',
            $folio['key']
        );

        self::assertSame(
            2,
            $folio['facts']['delegated_folios']
        );

        self::assertSame(
            [
                'spellbook',
                'growth',
            ],
            array_column(
                $folio['delegated'],
                'folio'
            )
        );

        /*
         * Merely resolving Calling rules must never advance the Character.
         */
        self::assertSame(
            1,
            $character->level()->value()
        );

        self::assertSame(
            8,
            $character->hitPoints()->maximum()
        );
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

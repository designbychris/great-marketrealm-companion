<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class CharacterCertificationTest extends TestCase
{
    public function testGuildCertificationAdvancesExactlyOneLevelAndHp(): void
    {
        $character = $this->character();
        $character->gainExperience(
            Experience::fromInt(300)
        );

        $character->certifyAdvancement(6);

        self::assertSame(
            2,
            $character->level()->value()
        );

        self::assertSame(
            15,
            $character->hitPoints()->maximum()
        );

        self::assertSame(
            15,
            $character->hitPoints()->current()
        );

        self::assertSame(
            300,
            $character->experience()->value()
        );
    }

    public function testCharacterCannotBeCertifiedWithoutEligibility(): void
    {
        $this->expectException(
            \LogicException::class
        );

        $this->character()
            ->certifyAdvancement(6);
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
            HitPoints::full(9),
            AbilityScores::fromScores(
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            )
        );
    }
}

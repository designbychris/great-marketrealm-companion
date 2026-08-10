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
use PHPUnit\Framework\TestCase;
final class RisingRegisterDomainTest extends TestCase
{
    public function testExperienceCanUnlockAndCertifyLevelTwo(): void
    {
        $character = $this->character();
        $character->gainExperience(
            \GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience::fromInt(300)
        );
        self::assertSame(2, $character->level()->value());
        self::assertSame(300, $character->experience()->value());
        self::assertSame(14, $character->hitPoints()->maximum());
    }
    private function character(): Character
    {
        return Character::create(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),CharacterName::fromString('Wiz'),Race::fromString('fructan'),CharacterClass::fromString('wizard'),HitPoints::full(8),
            AbilityScores::fromScores(AbilityScore::fromInt(10),AbilityScore::fromInt(10),AbilityScore::fromInt(14),AbilityScore::fromInt(16),AbilityScore::fromInt(10),AbilityScore::fromInt(10))
        );
    }
}

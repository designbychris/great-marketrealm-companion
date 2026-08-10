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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\RisingRegisterPresenter;
use PHPUnit\Framework\TestCase;
final class RisingRegisterPresenterTest extends TestCase
{
    public function testLevelOneShowsThreeHundredXpThreshold(): void
    {
        $character=Character::create(CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),CharacterName::fromString('Wiz'),Race::fromString('fructan'),CharacterClass::fromString('wizard'),HitPoints::full(8),AbilityScores::fromScores(AbilityScore::fromInt(10),AbilityScore::fromInt(10),AbilityScore::fromInt(14),AbilityScore::fromInt(16),AbilityScore::fromInt(10),AbilityScore::fromInt(10)));
        $state=(new RisingRegisterPresenter())->present($character);
        self::assertSame(300,$state['next_level_xp']);self::assertSame(300,$state['xp_to_next']);self::assertSame('+2',$state['current_proficiency']);self::assertSame(6,$state['next_hit_point_gain']);
    }
}

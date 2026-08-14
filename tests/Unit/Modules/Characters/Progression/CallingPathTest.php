<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class CallingPathTest extends TestCase
{
    public function testNewCharacterHasNoCallingPath(): void
    {
        self::assertFalse(
            $this->character()
                ->callingPath()
                ->isChosen()
        );
    }

    public function testCallingPathCanBeCertifiedOnce(): void
    {
        $character = $this->character();

        $character->chooseCallingPath(
            CallingPath::fromString(
                'school-of-aromancy'
            )
        );

        self::assertSame(
            'school-of-aromancy',
            $character
                ->callingPath()
                ->value()
        );
    }

    public function testCertifiedCallingPathCannotBeReplacedSilently(): void
    {
        $character = $this->character();

        $character->chooseCallingPath(
            CallingPath::fromString(
                'school-of-aromancy'
            )
        );

        $this->expectException(
            \LogicException::class
        );

        $character->chooseCallingPath(
            CallingPath::fromString(
                'school-of-preservation'
            )
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

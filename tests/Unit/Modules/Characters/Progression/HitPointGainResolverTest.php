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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\HitPointGainResolver;
use PHPUnit\Framework\TestCase;

final class HitPointGainResolverTest extends TestCase
{
    public function testAverageUsesClassAverageAndConstitution(): void
    {
        $gain = (
            new HitPointGainResolver()
        )->resolve(
            $this->character(),
            [
                'vitality-hit-points' => [
                    'average',
                ],
            ]
        );

        self::assertSame(6, $gain);
    }

    public function testMissingVitalityChoiceCannotBeCertified(): void
    {
        $this->expectException(
            \RuntimeException::class
        );

        (
            new HitPointGainResolver()
        )->resolve(
            $this->character(),
            []
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

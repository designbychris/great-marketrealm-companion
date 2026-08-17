<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Combat\Targets;

use GreatMarketrealmCompanion\Modules\Characters\Combat\Targets\Services\RollTargetCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class RollTargetCatalogueTest extends TestCase
{
    public function testOnlySelfIsResolvedUntilTargetRegistryExists(): void
    {
        $score = AbilityScore::fromInt(10);
        $character = Character::create(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            HitPoints::full(8),
            AbilityScores::fromScores(
                $score,
                $score,
                $score,
                $score,
                $score,
                $score
            )
        );

        $targets = (
            new RollTargetCatalogue()
        )->forCharacter($character);

        self::assertSame(
            [
                'self',
                'ally',
                'player-character',
                'npc',
                'hostile-creature',
            ],
            array_column($targets, 'kind')
        );

        self::assertTrue($targets[0]['resolved']);
        self::assertSame(
            '01KZM4W72K1G12FY75R0BTQREW',
            $targets[0]['id']
        );
        self::assertSame(
            'Magic',
            $targets[0]['target_label']
        );

        self::assertFalse($targets[1]['resolved']);
        self::assertNull($targets[1]['id']);
        self::assertFalse($targets[4]['resolved']);
    }
}

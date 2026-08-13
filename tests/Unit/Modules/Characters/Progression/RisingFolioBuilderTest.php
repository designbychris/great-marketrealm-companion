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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\RisingFolioBuilder;
use PHPUnit\Framework\TestCase;

final class RisingFolioBuilderTest extends TestCase
{
    public function testBuilderCreatesVitalityAndProficiencyFolios(): void
    {
        $folios = (
            new RisingFolioBuilder()
        )->forAdvancement(
            $this->character(),
            2
        );

        $states = $folios->toArray();

        self::assertSame(
            ['vitality', 'proficiency'],
            array_column(
                $states,
                'key'
            )
        );

        self::assertSame(2, $folios->total());
        self::assertSame(3, $folios->readyCount());
        self::assertSame(1, $folios->attentionCount());
    }

    public function testVitalityFolioCarriesHpChoiceFacts(): void
    {
        $states = (
            new RisingFolioBuilder()
        )->forAdvancement(
            $this->character(),
            2
        )->toArray();

        $vitality = $states[0];

        self::assertSame('d6', $vitality['facts']['hit_die']);
        self::assertSame(6, $vitality['facts']['average_gain']);
        self::assertTrue($vitality['requires_choice']);
        self::assertCount(2, $vitality['choices']);
    }

    public function testRecordedVitalityChoiceMakesFolioReady(): void
    {
        $folios = (
            new RisingFolioBuilder()
        )->forAdvancement(
            $this->character(),
            2,
            [
                'vitality-hit-points' => [
                    'average',
                ],
            ]
        );

        self::assertSame(2, $folios->readyCount());
        self::assertSame(0, $folios->attentionCount());
        self::assertTrue($folios->allReady());

        $vitality = $folios->toArray()[0];

        self::assertSame(
            'average',
            $vitality['facts']['selected']
        );

        self::assertTrue(
            $vitality['ready']
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

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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathGiftFolio;
use PHPUnit\Framework\TestCase;

final class PathGiftFolioTest extends TestCase
{
    public function testGiftFolioWaitsUntilAPathIsSelected(): void
    {
        self::assertNull(
            (
                new PathGiftFolio()
            )->build(
                $this->wizard(
                    level: 1
                ),
                2
            )
        );
    }

    public function testSelectingShelfmancyRevealsTwoAutomaticLevelTwoGifts(): void
    {
        $folio = (
            new PathGiftFolio()
        )->build(
            $this->wizard(
                level: 1
            ),
            2,
            [
                'wizard-arcane-tradition' => [
                    'school-of-shelfmancy',
                ],
            ]
        );

        self::assertNotNull($folio);

        $state = $folio->toArray();

        self::assertSame(
            'path-gifts',
            $state['key']
        );

        self::assertTrue(
            $state['ready']
        );

        self::assertSame(
            2,
            $state['facts']['gift_count']
        );

        self::assertSame(
            [
                'spell-stored-container',
                'packaging-proficiency',
            ],
            $state['facts']['gift_keys']
        );
    }

    public function testExistingLevelThreeShelfmancerReceivesCatchUpGifts(): void
    {
        $state = (
            new PathGiftFolio()
        )->build(
            $this->wizard(
                level: 3,
                path: 'school-of-shelfmancy'
            ),
            4
        )->toArray();

        self::assertTrue(
            $state['facts']['catch_up']
        );

        self::assertSame(
            2,
            $state['facts']['gift_count']
        );
    }

    public function testCertifiedLevelTwoGiftsAreNotOfferedAgain(): void
    {
        self::assertNull(
            (
                new PathGiftFolio()
            )->build(
                $this->wizard(
                    level: 3,
                    path: 'school-of-shelfmancy',
                    gifts: [
                        'spell-stored-container',
                        'packaging-proficiency',
                    ]
                ),
                4
            )
        );
    }

    public function testLevelSixUnlocksVacuumLockOnly(): void
    {
        $state = (
            new PathGiftFolio()
        )->build(
            $this->wizard(
                level: 5,
                path: 'school-of-shelfmancy',
                gifts: [
                    'spell-stored-container',
                    'packaging-proficiency',
                ]
            ),
            6
        )->toArray();

        self::assertSame(
            ['vacuum-lock'],
            $state['facts']['gift_keys']
        );

        self::assertFalse(
            $state['facts']['catch_up']
        );
    }

    /**
     * @param array<int,string> $gifts
     */
    private function wizard(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            Level::fromInt($level),
            Experience::fromInt(6500),
            HitPoints::full(18),
            AbilityScores::fromScores(
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            ),
            callingPath: CallingPath::fromString(
                $path
            ),
            pathGifts: PathGifts::fromArray(
                $gifts
            )
        );
    }
}

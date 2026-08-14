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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathFolio;
use PHPUnit\Framework\TestCase;

final class PathFolioTest extends TestCase
{
    public function testWizardLevelTwoRequiresArcaneTradition(): void
    {
        $folio = (
            new PathFolio()
        )->build(
            $this->wizardAtLevel(1),
            2
        );

        self::assertNotNull($folio);

        $state = $folio->toArray();

        self::assertSame('path', $state['key']);
        self::assertFalse($state['ready']);
        self::assertTrue(
            $state['requires_choice']
        );

        self::assertSame(
            'Arcane Tradition',
            $state['facts']['path_label']
        );

        self::assertCount(
            8,
            $state['choices']
        );
    }

    public function testRecordedArcaneTraditionMakesPathReady(): void
    {
        $state = (
            new PathFolio()
        )->build(
            $this->wizardAtLevel(1),
            2,
            [
                'wizard-arcane-tradition' => [
                    'school-of-aromancy',
                ],
            ]
        )->toArray();

        self::assertTrue($state['ready']);

        self::assertSame(
            ['school-of-aromancy'],
            $state['facts']['selected_values']
        );
    }

    public function testOlderWizardWithoutPathGetsCatchUpFolio(): void
    {
        $state = (
            new PathFolio()
        )->build(
            $this->wizardAtLevel(3),
            4
        )->toArray();

        self::assertTrue(
            $state['facts']['catch_up']
        );

        self::assertFalse($state['ready']);
    }

    public function testCertifiedWizardPathDoesNotAskAgain(): void
    {
        $character =
            $this->wizardAtLevel(3);

        $character->chooseCallingPath(
            CallingPath::fromString(
                'school-of-aromancy'
            )
        );

        self::assertNull(
            (
                new PathFolio()
            )->build(
                $character,
                4
            )
        );
    }

    private function wizardAtLevel(
        int $level
    ): Character {
        return Character::reconstitute(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt($level),
            Experience::fromInt(2700),
            HitPoints::full(18),
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

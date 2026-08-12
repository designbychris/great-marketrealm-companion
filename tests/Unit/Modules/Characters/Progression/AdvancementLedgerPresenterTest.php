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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementLedgerPresenter;
use PHPUnit\Framework\TestCase;

final class AdvancementLedgerPresenterTest extends TestCase
{
    public function testEligibleCharacterTargetsOneLevelAtATime(): void
    {
        $character = $this->character();
        $character->gainExperience(
            Experience::fromInt(6500)
        );

        $state = (
            new AdvancementLedgerPresenter()
        )->present($character);

        self::assertTrue($state['eligible']);
        self::assertSame(1, $state['current_level']);
        self::assertSame(2, $state['target_level']);
        self::assertSame(5, $state['highest_eligible_level']);
        self::assertSame(4, $state['levels_waiting']);
        self::assertFalse($state['commit_available']);
    }

    public function testPreviewProvidesHpAndProficiencyFacts(): void
    {
        $character = $this->character();
        $character->gainExperience(
            Experience::fromInt(300)
        );

        $state = (
            new AdvancementLedgerPresenter()
        )->present($character);

        self::assertSame('d6', $state['hit_die']);
        self::assertSame(6, $state['suggested_hp_gain']);
        self::assertSame('+2', $state['current_proficiency']);
        self::assertSame('+2', $state['target_proficiency']);
        self::assertSame(
            'foundation',
            $state['class_progression']['catalogue_status']
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

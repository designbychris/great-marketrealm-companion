<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\CompleteAdventurer;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Modules\Characters\Services\CompleteAdventurerPresenter;
use PHPUnit\Framework\TestCase;

final class CompleteAdventurerPresenterTest extends TestCase
{
    public function testCompleteRecordConnectsSevenGuildFolios(): void
    {
        $state = (new CompleteAdventurerPresenter())->present(
            $this->character(),
            $this->portrait(),
            ['rows' => [], 'equipped_count' => 0],
            [],
            ['entries' => [], 'has_spells' => false],
            [
                'progress_percent' => 0,
                'xp_to_next' => 300,
                'next_level' => 2,
                'is_maximum' => false,
            ]
        );

        self::assertTrue($state['complete']);
        self::assertSame(7, $state['ready_count']);
        self::assertSame(7, $state['total']);
        self::assertSame(
            ['identity','abilities','portrait','equipment','combat','arcana','progression'],
            array_column($state['sections'], 'key')
        );
    }

    public function testEmptyPackAndNoSpellcastingRemainValidStates(): void
    {
        $state = (new CompleteAdventurerPresenter())->present(
            $this->character(),
            $this->portrait(),
            ['rows' => [], 'equipped_count' => 0],
            [],
            ['entries' => [], 'has_spells' => false],
            ['progress_percent' => 0, 'xp_to_next' => 300, 'next_level' => 2]
        );

        $sections = [];
        foreach ($state['sections'] as $section) {
            $sections[$section['key']] = $section;
        }

        self::assertTrue($sections['equipment']['ready']);
        self::assertTrue($sections['combat']['ready']);
        self::assertTrue($sections['arcana']['ready']);
        self::assertStringContainsString(
            'no spellcasting required',
            $sections['arcana']['detail']
        );
    }

    private function character(): Character
    {
        $score = AbilityScore::fromInt(10);

        return Character::create(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            HitPoints::full(8),
            AbilityScores::fromScores($score,$score,$score,$score,$score,$score)
        );
    }

    private function portrait(): PortraitViewModel
    {
        return new PortraitViewModel(
            'generated',
            'Wiz',
            'fructan',
            'Fructan',
            'wizard',
            'Wizard',
            '<svg></svg>',
            ['body' => 'fructan-body-01'],
            'complete-adventurer-seed'
        );
    }
}

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementSealPresenter;
use PHPUnit\Framework\TestCase;

final class AdvancementSealPresenterTest extends TestCase
{
    public function testSealBecomesReadyWhenAllFoliosAreReady(): void
    {
        $state = (
            new AdvancementSealPresenter()
        )->present(
            $this->character(),
            [
                'eligible' => true,
                'folios_complete' => true,
                'folio_ready_count' => 2,
                'folio_total' => 2,
                'current_level' => 1,
                'target_level' => 2,
                'suggested_hp_gain' => 6,
                'hit_die' => 'd8',
                'constitution_modifier' => 1,
                'current_proficiency' => '+2',
                'target_proficiency' => '+2',
                'recorded_choices' => [
                    'vitality-hit-points' => [
                        'average',
                    ],
                ],
            ]
        );

        self::assertTrue($state['ready']);
        self::assertSame(
            'The Advancement Seal',
            $state['title']
        );

        self::assertSame(
            'READY FOR GUILD CERTIFICATION',
            $state['status']
        );

        self::assertStringContainsString(
            '+6 maximum HP',
            $state['review'][1]['detail']
        );

        self::assertFalse(
            $state['commit_available']
        );
    }

    public function testSealWaitsForIncompleteFolios(): void
    {
        $state = (
            new AdvancementSealPresenter()
        )->present(
            $this->character(),
            [
                'eligible' => true,
                'folios_complete' => false,
                'folio_ready_count' => 1,
                'folio_total' => 2,
                'current_level' => 1,
                'target_level' => 2,
                'suggested_hp_gain' => 6,
                'hit_die' => 'd8',
                'constitution_modifier' => 1,
                'current_proficiency' => '+2',
                'target_proficiency' => '+2',
                'recorded_choices' => [],
            ]
        );

        self::assertFalse($state['ready']);

        self::assertSame(
            'Registrar’s Review Pending',
            $state['title']
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

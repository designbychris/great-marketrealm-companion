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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Spellbook;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\LivingRegisterPresenter;
use PHPUnit\Framework\TestCase;

final class LivingRegisterPresenterTest extends TestCase
{
    public function testItPresentsOnlyCertifiedCharacterState(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(2700),
            HitPoints::fromValues(21, 24),
            $this->abilities(),
            spellbook: Spellbook::fromArray([
                'spells' => ['market-missile', 'pantry-ward'],
                'cantrips' => ['produce-spark'],
            ]),
            callingPath: CallingPath::fromString('school-of-shelfmancy'),
            pathGifts: PathGifts::fromArray([
                'spell-stored-container',
                'packaging-proficiency',
            ])
        );

        $state = (new LivingRegisterPresenter())->present(
            $character,
            [
                ['target_level' => 2, 'hit_point_gain' => 6],
                ['target_level' => 3, 'hit_point_gain' => 5],
                ['target_level' => 4, 'hit_point_gain' => 5],
            ]
        );

        self::assertSame(4, $state['level']);
        self::assertSame('Wizard', $state['calling']);
        self::assertSame('School of Shelfmancy', $state['path_label']);
        self::assertSame('+2', $state['proficiency']);
        self::assertSame(21, $state['current_hp']);
        self::assertSame(24, $state['maximum_hp']);
        self::assertSame(3, $state['arcana_known']);
        self::assertSame(2, $state['path_gift_count']);
        self::assertSame(3, $state['certification_count']);
        self::assertSame(4, $state['latest_certification']['target_level']);
        self::assertTrue($state['is_living_record']);
    }

    public function testLatestCertificationBecomesFreshInk(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(2700),
            HitPoints::fromValues(24, 24),
            $this->abilities(),
            spellbook: Spellbook::fromArray([]),
            callingPath: CallingPath::fromString('school-of-shelfmancy')
        );

        $state = (new LivingRegisterPresenter())->present($character, [[
            'from_level' => 3,
            'target_level' => 4,
            'hit_point_gain' => 6,
            'old_maximum_hp' => 18,
            'new_maximum_hp' => 24,
            'proficiency' => '+2',
            'choices' => [
                'wizard-spells' => ['market-missile', 'pantry-ward'],
                'wizard-cantrips' => ['produce-spark'],
            ],
            'path_gifts_granted' => [
                ['key' => 'packaging-proficiency', 'label' => 'Packaging Proficiency'],
            ],
            'certified_at' => '2026-08-15T07:30:00+00:00',
        ]]);

        self::assertTrue($state['has_fresh_ink']);
        self::assertSame(3, $state['fresh_ink']['from_level']);
        self::assertSame(4, $state['fresh_ink']['target_level']);
        self::assertSame(6, $state['fresh_ink']['hit_point_gain']);
        self::assertSame(18, $state['fresh_ink']['old_maximum_hp']);
        self::assertSame(24, $state['fresh_ink']['new_maximum_hp']);
        self::assertSame(['market-missile', 'pantry-ward'], $state['fresh_ink']['spells_learned']);
        self::assertSame(['produce-spark'], $state['fresh_ink']['cantrips_learned']);
        self::assertSame(['Packaging Proficiency'], $state['fresh_ink']['path_gifts_granted']);
    }

    public function testCertificationHistoryBecomesNewestFirstSealedChronicle(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(2700),
            HitPoints::fromValues(24, 24),
            $this->abilities()
        );

        $state = (new LivingRegisterPresenter())->present($character, [
            [
                'certification_key' => 'wiz:1:2',
                'from_level' => 1,
                'target_level' => 2,
                'hit_point_gain' => 6,
                'certified_at' => '2026-08-10T09:00:00+00:00',
            ],
            [
                'certification_key' => 'wiz:2:3',
                'from_level' => 2,
                'target_level' => 3,
                'hit_point_gain' => 5,
                'choices' => ['wizard-spells' => ['pantry-ward']],
                'certified_at' => '2026-08-12T09:00:00+00:00',
            ],
            [
                'certification_key' => 'wiz:3:4',
                'from_level' => 3,
                'target_level' => 4,
                'hit_point_gain' => 5,
                'path_gifts_granted' => [[
                    'key' => 'spell-stored-container',
                    'label' => 'Spell-Stored Container',
                ]],
                'certified_at' => '2026-08-15T07:30:00+00:00',
            ],
        ]);

        self::assertTrue($state['has_chronicle']);
        self::assertCount(3, $state['chronicle']);
        self::assertSame(3, $state['chronicle'][0]['sequence']);
        self::assertTrue($state['chronicle'][0]['is_latest']);
        self::assertSame(4, $state['chronicle'][0]['target_level']);
        self::assertSame(['Spell-Stored Container'], $state['chronicle'][0]['path_gifts_granted']);
        self::assertSame(2, $state['chronicle'][1]['sequence']);
        self::assertSame(['pantry-ward'], $state['chronicle'][1]['spells_learned']);
        self::assertSame(1, $state['chronicle'][2]['sequence']);
        self::assertFalse($state['chronicle'][2]['is_latest']);
    }

    public function testChronicleMarksMeaningfulGuildMilestones(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(5),
            Experience::fromInt(6500),
            HitPoints::fromValues(30, 30),
            $this->abilities(),
            callingPath: CallingPath::fromString('school-of-shelfmancy')
        );

        $state = (new LivingRegisterPresenter())->present($character, [
            [
                'from_level' => 1,
                'target_level' => 2,
                'calling_path' => '',
            ],
            [
                'from_level' => 2,
                'target_level' => 3,
                'calling_path' => 'school-of-shelfmancy',
            ],
            [
                'from_level' => 3,
                'target_level' => 4,
                'calling_path' => 'school-of-shelfmancy',
                'path_gifts_granted' => [[
                    'key' => 'spell-stored-container',
                    'label' => 'Spell-Stored Container',
                ]],
            ],
            [
                'from_level' => 4,
                'target_level' => 5,
                'calling_path' => 'school-of-shelfmancy',
            ],
        ]);

        self::assertTrue($state['has_milestones']);
        self::assertSame(4, $state['milestone_count']);
        self::assertSame('level-5', $state['chronicle'][0]['milestones'][0]['key']);
        self::assertSame('path-gift', $state['chronicle'][1]['milestones'][0]['key']);
        self::assertSame('calling-path', $state['chronicle'][2]['milestones'][0]['key']);
        self::assertSame('first-seal', $state['chronicle'][3]['milestones'][0]['key']);
    }

    public function testChronicleProducesAReadOnlyMeasureOfTheJourney(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(4),
            Experience::fromInt(2700),
            HitPoints::fromValues(24, 24),
            $this->abilities()
        );

        $state = (new LivingRegisterPresenter())->present($character, [
            [
                'from_level' => 1,
                'target_level' => 2,
                'hit_point_gain' => 6,
                'choices' => ['wizard-spells' => ['market-missile']],
                'certified_at' => '2026-08-10T09:00:00+00:00',
            ],
            [
                'from_level' => 2,
                'target_level' => 3,
                'hit_point_gain' => 5,
                'calling_path' => 'school-of-shelfmancy',
                'choices' => [
                    'wizard-spells' => ['pantry-ward'],
                    'wizard-cantrips' => ['produce-spark'],
                ],
                'certified_at' => '2026-08-12T09:00:00+00:00',
            ],
            [
                'from_level' => 3,
                'target_level' => 4,
                'hit_point_gain' => 5,
                'calling_path' => 'school-of-shelfmancy',
                'path_gifts_granted' => [[
                    'key' => 'spell-stored-container',
                    'label' => 'Spell-Stored Container',
                ]],
                'certified_at' => '2026-08-15T07:30:00+00:00',
            ],
        ]);

        self::assertTrue($state['has_journey_measure']);
        self::assertSame(3, $state['journey_measure']['certifications']);
        self::assertSame(16, $state['journey_measure']['maximum_hp_gained']);
        self::assertSame(2, $state['journey_measure']['spells_learned']);
        self::assertSame(1, $state['journey_measure']['cantrips_learned']);
        self::assertSame(1, $state['journey_measure']['path_gifts_granted']);
        self::assertSame(3, $state['journey_measure']['milestones']);
        self::assertSame('2026-08-10T09:00:00+00:00', $state['journey_measure']['first_certified_at']);
        self::assertSame('2026-08-15T07:30:00+00:00', $state['journey_measure']['latest_certified_at']);
    }

    public function testChronicleProducesALivingRecordOfChange(): void
    {
        $character = Character::reconstitute(
            CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
            CharacterName::fromString('Wiz'),
            Race::fromString('fructan'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(5),
            Experience::fromInt(6500),
            HitPoints::fromValues(30, 30),
            $this->abilities(),
            callingPath: CallingPath::fromString('school-of-shelfmancy')
        );

        $state = (new LivingRegisterPresenter())->present($character, [
            [
                'from_level' => 1,
                'target_level' => 2,
                'old_maximum_hp' => 8,
                'new_maximum_hp' => 14,
                'hit_point_gain' => 6,
            ],
            [
                'from_level' => 2,
                'target_level' => 3,
                'calling_path' => 'school-of-shelfmancy',
                'choices' => ['wizard-spells' => ['pantry-ward']],
                'hit_point_gain' => 5,
            ],
            [
                'from_level' => 3,
                'target_level' => 4,
                'calling_path' => 'school-of-shelfmancy',
                'path_gifts_granted' => [[
                    'key' => 'spell-stored-container',
                    'label' => 'Spell-Stored Container',
                ]],
                'hit_point_gain' => 5,
            ],
            [
                'from_level' => 4,
                'target_level' => 5,
                'calling_path' => 'school-of-shelfmancy',
                'hit_point_gain' => 6,
            ],
        ]);

        self::assertTrue($state['has_change_record']);
        self::assertSame(1, $state['change_record']['starting_level']);
        self::assertSame(5, $state['change_record']['current_level']);
        self::assertSame(4, $state['change_record']['levels_gained']);
        self::assertSame(8, $state['change_record']['starting_maximum_hp']);
        self::assertSame(30, $state['change_record']['current_maximum_hp']);
        self::assertSame(22, $state['change_record']['maximum_hp_change']);
        self::assertSame(3, $state['change_record']['first_path']['level']);
        self::assertSame(2, $state['change_record']['first_path']['sequence']);
        self::assertSame(4, $state['change_record']['first_path_gift']['level']);
        self::assertSame(3, $state['change_record']['first_path_gift']['sequence']);
        self::assertSame(3, $state['change_record']['first_arcana']['level']);
    }

    public function testFreshInkIsAbsentWithoutCertificationHistory(): void
    {
        $state = (new LivingRegisterPresenter())->present(
            Character::create(
                CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
                CharacterName::fromString('Wiz'),
                Race::fromString('fructan'),
                CharacterClass::fromString('wizard'),
                HitPoints::full(8),
                $this->abilities()
            )
        );

        self::assertFalse($state['has_fresh_ink']);
        self::assertNull($state['fresh_ink']);
    }

    public function testUnchosenPathProducesAnEmptyPathRecord(): void
    {
        $state = (new LivingRegisterPresenter())->present(
            Character::create(
                CharacterId::fromString('01KZM4W72K1G12FY75R0BTQREW'),
                CharacterName::fromString('Wiz'),
                Race::fromString('fructan'),
                CharacterClass::fromString('wizard'),
                HitPoints::full(8),
                $this->abilities()
            )
        );

        self::assertFalse($state['has_path']);
        self::assertSame('', $state['path_label']);
        self::assertSame(0, $state['path_gift_count']);
        self::assertSame(0, $state['certification_count']);
    }

    private function abilities(): AbilityScores
    {
        return AbilityScores::fromScores(
            AbilityScore::fromInt(10),
            AbilityScore::fromInt(10),
            AbilityScore::fromInt(14),
            AbilityScore::fromInt(16),
            AbilityScore::fromInt(10),
            AbilityScore::fromInt(10)
        );
    }
}

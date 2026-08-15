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

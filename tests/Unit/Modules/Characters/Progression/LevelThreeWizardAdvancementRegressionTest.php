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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Spellbook;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Services\AdvancementLedgerPresenter;
use PHPUnit\Framework\TestCase;

final class LevelThreeWizardAdvancementRegressionTest extends TestCase
{
    public function testCertifiedPathIsNotRequestedAgainAtLevelThree(): void
    {
        $character = $this->levelTwoWizard();

        $state = (
            new AdvancementLedgerPresenter()
        )->present($character);

        self::assertTrue($state['eligible']);
        self::assertSame(3, $state['target_level']);

        self::assertNotContains(
            'path',
            array_column(
                $state['folios'],
                'key'
            )
        );

        self::assertSame(
            'school-of-shelfmancy',
            $character
                ->callingPath()
                ->value()
        );
    }

    public function testLevelThreeOffersFourNewLevelTwoWizardSpells(): void
    {
        $state = (
            new AdvancementLedgerPresenter()
        )->present(
            $this->levelTwoWizard()
        );

        $spellbook = $this->folio(
            $state['folios'],
            'spellbook'
        );

        self::assertNotNull($spellbook);

        self::assertSame(
            2,
            $spellbook['facts']['choice_minimum']
        );

        self::assertSame(
            4,
            $spellbook['facts']['available_choices']
        );

        self::assertSame(
            [
                'aisle-step',
                'stockroom-veil',
                'price-freeze',
                'crate-levitation',
            ],
            array_column(
                $spellbook['choices'],
                'key'
            )
        );

        self::assertSame(
            [2, 2, 2, 2],
            array_column(
                $spellbook['choices'],
                'spell_level'
            )
        );
    }

    public function testHpAndTwoSpellsCompleteLevelThreeFolios(): void
    {
        $state = (
            new AdvancementLedgerPresenter()
        )->present(
            $this->levelTwoWizard(),
            [
                'vitality-hit-points' => [
                    'average',
                ],
                'wizard-spells' => [
                    'aisle-step',
                    'stockroom-veil',
                ],
            ]
        );

        self::assertSame(5, $state['folio_total']);
        self::assertSame(5, $state['folio_ready_count']);
        self::assertSame(0, $state['folio_attention_count']);
        self::assertTrue($state['folios_complete']);
    }

    /**
     * @param array<int,array<string,mixed>> $folios
     * @return array<string,mixed>|null
     */
    private function folio(
        array $folios,
        string $key
    ): ?array {
        foreach ($folios as $folio) {
            if (
                is_array($folio)
                && (string) (
                    $folio['key'] ?? ''
                ) === $key
            ) {
                return $folio;
            }
        }

        return null;
    }

    private function levelTwoWizard(): Character
    {
        return Character::reconstitute(
            CharacterId::fromString(
                '01KZM4W72K1G12FY75R0BTQREW'
            ),
            CharacterName::fromString('Magic'),
            Race::fromString('frostreem'),
            CharacterClass::fromString('wizard'),
            Level::fromInt(2),
            Experience::fromInt(900),
            HitPoints::full(14),
            AbilityScores::fromScores(
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(12),
                AbilityScore::fromInt(16),
                AbilityScore::fromInt(10),
                AbilityScore::fromInt(10)
            ),
            spellbook: Spellbook::fromArray(
                [
                    'spells' => [
                        'pantry-ward',
                        'market-missile',
                    ],
                    'cantrips' => [],
                ]
            ),
            callingPath: CallingPath::fromString(
                'school-of-shelfmancy'
            )
        );
    }
}

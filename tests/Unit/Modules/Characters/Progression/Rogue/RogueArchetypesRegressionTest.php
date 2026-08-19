<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Rogue;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Cunning\Services\RogueCunningRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathGiftFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions\RogueArchetypeGiftProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class RogueArchetypesRegressionTest extends TestCase
{
    public function testAllSixRogueArchetypesHaveGiftProgressions(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertTrue(
                $catalogue->supports($path)
            );

            self::assertCount(
                4,
                $catalogue->all($path)
            );
        }
    }

    public function testEachRogueArchetypeUsesCertifiedGiftCadence(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->paths() as $path) {
            self::assertSame(
                [3, 9, 13, 17],
                array_column(
                    $catalogue->all($path),
                    'level'
                )
            );
        }
    }

    public function testAllRogueArchetypeGiftsAreAutomatic(): void
    {
        foreach (
            RogueArchetypeGiftProgression::allDefinitions()
            as $definition
        ) {
            self::assertSame(
                ['automatic'],
                array_values(
                    array_unique(
                        array_column(
                            $definition->gifts(),
                            'mode'
                        )
                    )
                )
            );
        }
    }

    public function testLevelThreeSelectionRevealsFirstCheetobladeGift(): void
    {
        $folio = (
            new PathGiftFolio()
        )->build(
            $this->rogue(level: 2),
            3,
            [
                'rogue-archetype' => [
                    'the-cheetoblade',
                ],
            ]
        );

        self::assertNotNull($folio);

        $state = $folio->toArray();

        self::assertTrue($state['ready']);

        self::assertSame(
            ['cheetle-dust-feint'],
            $state['facts']['gift_keys']
        );
    }

    public function testLevelNineRevealsOnlyNextMastermindGift(): void
    {
        $state = (
            new PathGiftFolio()
        )->build(
            $this->rogue(
                level: 8,
                path: 'mastermind-of-the-aisles',
                gifts: [
                    'aisle-scheme',
                ]
            ),
            9
        )->toArray();

        self::assertSame(
            ['planned-distraction'],
            $state['facts']['gift_keys']
        );

        self::assertFalse(
            $state['facts']['catch_up']
        );
    }

    public function testLaterAisleStalkerGiftsUnlockInOrder(): void
    {
        $catalogue = new PathGiftCatalogue();
        $known = PathGifts::none();

        $expected = [
            3 => 'endcap-ambush',
            9 => 'silent-trolley',
            13 => 'closing-time-hunter',
            17 => 'nowhere-left-to-hide',
        ];

        foreach ($expected as $level => $key) {
            $unlocked = $catalogue->unlocked(
                'aisle-stalker',
                $level,
                $known
            );

            $latest = $unlocked[
                count($unlocked) - 1
            ];

            self::assertSame(
                $key,
                $latest['key']
            );

            $known = $known->grant(
                array_column(
                    $unlocked,
                    'key'
                )
            );
        }
    }

    public function testRogueChoiceCardsNowHaveUsefulGuidance(): void
    {
        $options = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('rogue')
        );

        self::assertCount(6, $options);

        foreach ($options as $option) {
            self::assertNotSame(
                '',
                $option['identity']
            );

            self::assertNotSame(
                '',
                $option['playstyle']
            );

            self::assertNotSame(
                '',
                $option['best_for']
            );

            self::assertCount(
                4,
                $option['gift_preview']
            );
        }
    }

    public function testMastermindChoicePreviewExplainsItsIdentity(): void
    {
        $options = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('rogue')
        );

        $mastermind = array_values(
            array_filter(
                $options,
                static fn (array $option): bool =>
                    $option['key']
                    === 'mastermind-of-the-aisles'
            )
        )[0];

        self::assertStringContainsString(
            'schemer',
            strtolower(
                $mastermind['identity']
            )
        );

        self::assertSame(
            [
                3,
                9,
                13,
                17,
            ],
            array_column(
                $mastermind['gift_preview'],
                'level'
            )
        );
    }

    public function testRogueLevelThreeDelegatesPathAndFirstGift(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Progression/'
            . 'Definitions/Classes/'
            . 'RogueProgression.php'
        );

        self::assertStringContainsString(
            "'folio' => 'path'",
            $source
        );

        self::assertStringContainsString(
            "'folio' => 'path-gifts'",
            $source
        );

        self::assertStringContainsString(
            "'phase' => 'III.12.4B'",
            $source
        );
    }

    public function testCunningRegisterShowsOnlyCertifiedArchetypeGifts(): void
    {
        $rogue = $this->rogue(
            level: 13,
            path: 'taffy-trickster',
            gifts: [
                'sticky-fingers',
                'pulled-sugar-escape',
            ]
        );

        $state = (
            new RogueCunningRegisterPresenter()
        )->present($rogue);

        self::assertSame(
            [
                'sticky-fingers',
                'pulled-sugar-escape',
            ],
            array_column(
                $state['archetype']['gifts'],
                'key'
            )
        );

        self::assertNotContains(
            'sweet-deception',
            array_column(
                $state['archetype']['gifts'],
                'key'
            )
        );
    }

    public function testLedgerPresentsCertifiedRogueArchetypeGiftCards(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Certified Rogue Archetype Gifts',
            $view
        );

        self::assertStringContainsString(
            'gmrc-cunning-register__path-gifts',
            $view
        );

        self::assertStringContainsString(
            "['gifts']",
            $view
        );
    }

    public function testRogueArchetypeGiftPresentationIsResponsive(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-cunning-register__gift-grid',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 700px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    /** @return array<int,string> */
    private function paths(): array
    {
        return [
            'the-cheetoblade',
            'spiceblade',
            'the-breadknife',
            'mastermind-of-the-aisles',
            'aisle-stalker',
            'taffy-trickster',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function rogue(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Archetype Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString('rogue'),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $path
                ),
            pathGifts:
                PathGifts::fromArray(
                    $gifts
                )
        );
    }

    private function source(
        string $relative
    ): string {
        $source = file_get_contents(
            $this->root()
            . '/'
            . $relative
        );

        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

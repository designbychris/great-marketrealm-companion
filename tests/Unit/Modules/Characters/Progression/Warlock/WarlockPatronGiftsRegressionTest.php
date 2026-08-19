<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Warlock;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\WarlockProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Folios\PathGiftFolio;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Patron\Services\WarlockPatronRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class WarlockPatronGiftsRegressionTest extends TestCase
{
    public function testAllFourPatronsHaveFourAutomaticGifts(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->patrons() as $patron) {
            $gifts = $catalogue->all($patron);

            self::assertCount(4, $gifts);

            self::assertSame(
                [1, 6, 10, 14],
                array_column(
                    $gifts,
                    'level'
                )
            );

            self::assertSame(
                [
                    'automatic',
                    'automatic',
                    'automatic',
                    'automatic',
                ],
                array_column(
                    $gifts,
                    'mode'
                )
            );
        }
    }

    public function testEveryPatronGiftHasPlayerFacingExplanation(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->patrons() as $patron) {
            foreach ($catalogue->all($patron) as $gift) {
                self::assertNotSame(
                    '',
                    trim(
                        (string) (
                            $gift['label']
                            ?? ''
                        )
                    )
                );

                self::assertNotSame(
                    '',
                    trim(
                        (string) (
                            $gift['summary']
                            ?? ''
                        )
                    )
                );

                self::assertNotSame(
                    '',
                    trim(
                        (string) (
                            $gift['detail']
                            ?? ''
                        )
                    )
                );
            }
        }
    }

    public function testChoicePreviewShowsCompletePatronGiftCadence(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'warlock'
            )
        );

        self::assertCount(4, $candidates);

        foreach ($candidates as $candidate) {
            self::assertSame(
                [1, 6, 10, 14],
                array_column(
                    $candidate[
                        'gift_preview'
                    ],
                    'level'
                )
            );

            self::assertCount(
                4,
                $candidate[
                    'gift_preview'
                ]
            );
        }
    }

    public function testFirstLevelGiftCanCatchUpOnFirstAdvancement(): void
    {
        $folio = (
            new PathGiftFolio()
        )->build(
            $this->warlock(
                1,
                'pact-of-the-mascot'
            ),
            2
        );

        self::assertNotNull($folio);

        self::assertSame(
            ['smiling-sponsorship'],
            $folio
                ->toArray()[
                    'facts'
                ]['gift_keys']
        );

        self::assertTrue(
            $folio
                ->toArray()[
                    'facts'
                ]['catch_up']
        );
    }

    public function testLaterPatronGiftMilestonesUnlockInOrder(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $known =
            PathGifts::fromArray([
                'smiling-sponsorship',
            ]);

        foreach ([
            6 => 'brand-ambassador',
            10 => 'impossible-endorsement',
            14 => 'mascot-unmasked',
        ] as $level => $expected) {
            $unlocked =
                $catalogue->unlocked(
                    'pact-of-the-mascot',
                    $level,
                    $known
                );

            self::assertSame(
                $expected,
                $unlocked[
                    count($unlocked) - 1
                ]['key']
            );

            $known = $known->grant(
                array_column(
                    $unlocked,
                    'key'
                )
            );
        }
    }

    public function testWarlockProgressionReservesLaterPatronGiftLevels(): void
    {
        $progression =
            new WarlockProgression();

        $warlock =
            CharacterClass::fromString(
                'warlock'
            );

        foreach ([6, 10, 14] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $warlock,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testAllFourPatronsAreRegisteredBySharedGiftCatalogue(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ($this->patrons() as $patron) {
            self::assertTrue(
                $catalogue->supports(
                    $patron
                )
            );

            self::assertNotSame(
                '',
                $catalogue->pathLabel(
                    $patron
                )
            );
        }
    }

    public function testPatronGiftKeysAreUniqueAcrossWarlockCatalogue(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $keys = [];

        foreach ($this->patrons() as $patron) {
            foreach ($catalogue->all($patron) as $gift) {
                $keys[] = (string) (
                    $gift['key']
                    ?? ''
                );
            }
        }

        self::assertCount(
            16,
            $keys
        );

        self::assertCount(
            16,
            array_unique($keys)
        );
    }

    public function testMascotPreviewBeginsWithSmilingSponsorship(): void
    {
        $candidate = array_values(
            array_filter(
                (
                    new PathCandidateCatalogue()
                )->forClass(
                    CharacterClass::fromString(
                        'warlock'
                    )
                ),
                static fn (
                    array $candidate
                ): bool =>
                    ($candidate['key'] ?? '')
                    === 'pact-of-the-mascot'
            )
        )[0];

        self::assertSame(
            'Smiling Sponsorship',
            $candidate[
                'gift_preview'
            ][0]['label']
        );

        self::assertSame(
            1,
            $candidate[
                'gift_preview'
            ][0]['level']
        );
    }

    public function testRegisterShowsOnlyCertifiedPatronGifts(): void
    {
        $register = (
            new WarlockPatronRegisterPresenter()
        )->present(
            $this->warlock(
                10,
                'pact-of-the-mascot',
                [
                    'smiling-sponsorship',
                    'brand-ambassador',
                ]
            )
        );

        self::assertSame(
            [
                'smiling-sponsorship',
                'brand-ambassador',
            ],
            array_column(
                $register[
                    'patron_gifts'
                ],
                'key'
            )
        );

        self::assertNotContains(
            'impossible-endorsement',
            array_column(
                $register[
                    'patron_gifts'
                ],
                'key'
            )
        );
    }

    public function testPatronRegisterRendersCertifiedGiftSection(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'Certified Patron Gifts',
            $view
        );

        self::assertStringContainsString(
            'Contract clauses currently in force',
            $view
        );

        self::assertStringContainsString(
            'gmrc-patron-register__gift-grid',
            $view
        );
    }

    public function testPatronGiftPresentationIsResponsiveAndForcedColourSafe(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-patron-register__gift-grid',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 840px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    /** @return array<int,string> */
    private function patrons(): array
    {
        return [
            'pact-of-the-mascot',
            'the-forgotten-freezer',
            'the-spoilfather',
            'the-sugar-fiend',
        ];
    }

    /**
     * @param array<int,string> $gifts
     */
    private function warlock(
        int $level,
        string $path = '',
        array $gifts = []
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Patron Gift Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'warlock'
            ),
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

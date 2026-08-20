<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Artificer;

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
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class ArtificerSpecialisationGiftsRegressionTest extends TestCase
{
    public function testAllFourSpecialisationsAreCertifiedBySharedGiftCatalogue(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->specialisations() as $specialisation) {
            self::assertTrue(
                $catalogue->supports(
                    $specialisation
                )
            );

            self::assertNotSame(
                '',
                $catalogue->pathLabel(
                    $specialisation
                )
            );

            self::assertNotSame(
                [],
                $catalogue->all(
                    $specialisation
                )
            );
        }
    }

    public function testCanonicalSpecialisationGiftCadencesPreserveHandbookShape(): void
    {
        $catalogue = new PathGiftCatalogue();

        $expected = [
            'the-spice-engineer' =>
                [3, 3, 5, 9, 15],
            'the-cheesemonger' =>
                [3, 3, 5, 9, 15],
            'the-sous-sorcerer' =>
                [3, 3],
            'the-culinary-engineer' =>
                [3, 3, 5, 9, 15],
        ];

        foreach (
            $expected
            as $specialisation => $levels
        ) {
            self::assertSame(
                $levels,
                array_column(
                    $catalogue->all(
                        $specialisation
                    ),
                    'level'
                )
            );
        }
    }

    public function testCanonicalHandbookGiftNamesRemainCertified(): void
    {
        $catalogue = new PathGiftCatalogue();

        self::assertSame(
            [
                'Spicecrafting',
                'Infused Condiments',
                'Flavour Cascade',
                'Gourmet Arsenal',
                'The Grand Seasoning',
            ],
            array_column(
                $catalogue->all(
                    'the-spice-engineer'
                ),
                'label'
            )
        );

        self::assertSame(
            [
                'Cheesy Constructs',
                'Cheese-Forged Infusions',
                'Dairy Density',
                'Cheese Overload',
                'Grand Gruyère',
            ],
            array_column(
                $catalogue->all(
                    'the-cheesemonger'
                ),
                'label'
            )
        );

        self::assertSame(
            [
                'Sous-Sorcerer Core Features',
                'Flavour Surge',
            ],
            array_column(
                $catalogue->all(
                    'the-sous-sorcerer'
                ),
                'label'
            )
        );

        self::assertSame(
            [
                'Tools of the Trade',
                'Culinary Infusions',
                'Battle Feast',
                'Animated Utensils',
                'Master of Magical Cuisine',
            ],
            array_column(
                $catalogue->all(
                    'the-culinary-engineer'
                ),
                'label'
            )
        );
    }

    public function testEverySpecialisationGiftHasPlayerFacingExplanation(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach (
            $this->specialisations()
            as $specialisation
        ) {
            foreach (
                $catalogue->all(
                    $specialisation
                )
                as $gift
            ) {
                self::assertNotSame(
                    '',
                    trim(
                        (string) (
                            $gift['key']
                            ?? ''
                        )
                    )
                );

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

                self::assertSame(
                    'automatic',
                    $gift['mode']
                    ?? null
                );
            }
        }
    }

    public function testCandidateCatalogueNowShowsSpecialisationGiftPreviews(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString(
                'artificer'
            )
        );

        self::assertCount(
            4,
            $candidates
        );

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                [],
                $candidate['gift_preview']
            );

            self::assertLessThanOrEqual(
                4,
                count(
                    $candidate[
                        'gift_preview'
                    ]
                )
            );
        }
    }

    public function testLevelThreeUnlocksBothOpeningSpiceEngineerGifts(): void
    {
        $unlocked = (
            new PathGiftCatalogue()
        )->unlocked(
            'the-spice-engineer',
            3,
            PathGifts::none()
        );

        self::assertSame(
            [
                'spicecrafting',
                'infused-condiments',
            ],
            array_column(
                $unlocked,
                'key'
            )
        );
    }

    public function testLaterSpiceEngineerGiftsDoNotUnlockEarly(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $known =
            PathGifts::fromArray([
                'spicecrafting',
                'infused-condiments',
            ]);

        self::assertSame(
            [],
            $catalogue->unlocked(
                'the-spice-engineer',
                4,
                $known
            )
        );

        self::assertSame(
            ['flavour-cascade'],
            array_column(
                $catalogue->unlocked(
                    'the-spice-engineer',
                    5,
                    $known
                ),
                'key'
            )
        );
    }

    public function testLevelFifteenUnlocksCulinaryEngineerCapstoneAfterKnownGifts(): void
    {
        $unlocked = (
            new PathGiftCatalogue()
        )->unlocked(
            'the-culinary-engineer',
            15,
            PathGifts::fromArray([
                'tools-of-the-trade',
                'culinary-infusions',
                'battle-feast',
                'animated-utensils',
            ])
        );

        self::assertSame(
            ['master-of-magical-cuisine'],
            array_column(
                $unlocked,
                'key'
            )
        );
    }

    public function testSousSorcererDoesNotInventUnsupportedLaterFeatures(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        self::assertSame(
            [
                'sous-sorcerer-core-features',
                'flavour-surge',
            ],
            array_column(
                $catalogue->all(
                    'the-sous-sorcerer'
                ),
                'key'
            )
        );

        self::assertSame(
            [],
            $catalogue->unlocked(
                'the-sous-sorcerer',
                15,
                PathGifts::fromArray([
                    'sous-sorcerer-core-features',
                    'flavour-surge',
                ])
            )
        );
    }

    public function testSpecialisationGiftKeysAreUniqueAcrossArtificerCatalogue(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $keys = [];

        foreach (
            $this->specialisations()
            as $specialisation
        ) {
            foreach (
                $catalogue->all(
                    $specialisation
                )
                as $gift
            ) {
                $keys[] =
                    (string) (
                        $gift['key']
                        ?? ''
                    );
            }
        }

        self::assertCount(
            17,
            $keys
        );

        self::assertCount(
            17,
            array_unique(
                $keys
            )
        );
    }

    public function testRegisterCertifiesGiftsAndSkipsUnsupportedSousSorcererMilestones(): void
    {
        $presenter =
            new ArtificerSpecialisationRegisterPresenter();

        $spice = $presenter->present(
            $this->artificer(
                3,
                'the-spice-engineer'
            )
        );

        self::assertSame(
            5,
            $spice[
                'specialisation'
            ]['gift_count']
        );

        self::assertSame(
            'Specialisation Gifts certified',
            $spice[
                'specialisation'
            ]['gift_status']
        );

        self::assertSame(
            5,
            $spice[
                'next_milestone'
            ]['level']
        );

        $sous = $presenter->present(
            $this->artificer(
                3,
                'the-sous-sorcerer'
            )
        );

        self::assertSame(
            2,
            $sous[
                'specialisation'
            ]['gift_count']
        );

        self::assertSame(
            6,
            $sous[
                'next_milestone'
            ]['level']
        );

        self::assertSame(
            'Tool Expertise',
            $sous[
                'next_milestone'
            ]['label']
        );
    }

    /**
     * @return array<int,string>
     */
    private function specialisations(): array
    {
        return [
            'the-spice-engineer',
            'the-cheesemonger',
            'the-sous-sorcerer',
            'the-culinary-engineer',
        ];
    }

    private function artificer(
        int $level,
        string $specialisation
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'Specialisation Gift Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString(
                'artificer'
            ),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $specialisation
                )
        );
    }
}

<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Bard;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class BardCollegeGiftsRegressionTest extends TestCase
{
    public function testAllSevenBardCollegesAreCertifiedBySharedGiftCatalogue(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->colleges() as $college) {
            self::assertTrue($catalogue->supports($college));
            self::assertNotSame('', $catalogue->pathLabel($college));
            self::assertNotSame([], $catalogue->all($college));
        }
    }

    public function testCanonicalCollegeGiftCadencesArePreserved(): void
    {
        $catalogue = new PathGiftCatalogue();

        $expected = [
            'college-of-the-seasoned-song' => [3, 3, 6, 14],
            'college-of-nostalgia' => [3, 3, 6],
            'college-of-preservation' => [3, 3, 6, 14],
            'charcutaire' => [3, 3, 6, 14],
            'college-of-culinary-crescendo' => [3, 3, 6, 14],
            'college-of-confection' => [3, 6, 14],
            'college-of-churned-verse' => [3, 3, 6, 14],
        ];

        foreach ($expected as $college => $levels) {
            self::assertSame(
                $levels,
                array_column($catalogue->all($college), 'level')
            );
        }
    }

    public function testHandbookCollegeGiftNamesRemainCanonical(): void
    {
        $catalogue = new PathGiftCatalogue();

        self::assertSame(
            [
                'Spice Notes',
                'Herbal Harmonization',
                'Choral Infusion',
                'Symphony of the Senses',
            ],
            array_column(
                $catalogue->all('college-of-the-seasoned-song'),
                'label'
            )
        );

        self::assertSame(
            [
                'Sizzling Solo',
                'Cook’s Toolkit',
                'Boiling Over',
                'Kitchen Orchestra',
            ],
            array_column(
                $catalogue->all('college-of-culinary-crescendo'),
                'label'
            )
        );

        self::assertSame(
            [
                'Creamtone Cantrips',
                'Harmonic Churn',
                'Chill Out',
                'Flavourful Refrain',
            ],
            array_column(
                $catalogue->all('college-of-churned-verse'),
                'label'
            )
        );
    }

    public function testEveryCollegeGiftHasPlayerFacingExplanation(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->colleges() as $college) {
            foreach ($catalogue->all($college) as $gift) {
                self::assertNotSame('', trim((string) ($gift['key'] ?? '')));
                self::assertNotSame('', trim((string) ($gift['label'] ?? '')));
                self::assertNotSame('', trim((string) ($gift['summary'] ?? '')));
                self::assertNotSame('', trim((string) ($gift['detail'] ?? '')));
                self::assertSame('automatic', $gift['mode'] ?? null);
            }
        }
    }

    public function testCandidateCatalogueNowShowsCollegeGiftPreviews(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('bard')
        );

        self::assertCount(7, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame([], $candidate['gift_preview']);
            self::assertLessThanOrEqual(4, count($candidate['gift_preview']));
        }
    }

    public function testLevelThreeUnlocksBothOpeningSeasonedSongGifts(): void
    {
        $unlocked = (
            new PathGiftCatalogue()
        )->unlocked(
            'college-of-the-seasoned-song',
            3,
            PathGifts::none()
        );

        self::assertSame(
            ['spice-notes', 'herbal-harmonization'],
            array_column($unlocked, 'key')
        );
    }

    public function testLaterSeasonedSongGiftsDoNotUnlockEarly(): void
    {
        $catalogue = new PathGiftCatalogue();
        $known = PathGifts::fromArray([
            'spice-notes',
            'herbal-harmonization',
        ]);

        self::assertSame(
            [],
            $catalogue->unlocked(
                'college-of-the-seasoned-song',
                5,
                $known
            )
        );

        self::assertSame(
            ['choral-infusion'],
            array_column(
                $catalogue->unlocked(
                    'college-of-the-seasoned-song',
                    6,
                    $known
                ),
                'key'
            )
        );
    }

    public function testLevelFourteenUnlocksSeasonedSongCapstoneAfterKnownGifts(): void
    {
        $unlocked = (
            new PathGiftCatalogue()
        )->unlocked(
            'college-of-the-seasoned-song',
            14,
            PathGifts::fromArray([
                'spice-notes',
                'herbal-harmonization',
                'choral-infusion',
            ])
        );

        self::assertSame(
            ['symphony-of-the-senses'],
            array_column($unlocked, 'key')
        );
    }

    public function testNostalgiaDoesNotInventAnUnsupportedLevelFourteenGift(): void
    {
        $catalogue = new PathGiftCatalogue();

        self::assertSame(
            [
                'jingle-strike',
                'viral-catchphrase',
                'forgotten-favorite',
            ],
            array_column(
                $catalogue->all('college-of-nostalgia'),
                'key'
            )
        );

        self::assertSame(
            [],
            $catalogue->unlocked(
                'college-of-nostalgia',
                14,
                PathGifts::fromArray([
                    'jingle-strike',
                    'viral-catchphrase',
                    'forgotten-favorite',
                ])
            )
        );
    }

    public function testCollegeGiftKeysAreUniqueAcrossBardCatalogue(): void
    {
        $catalogue = new PathGiftCatalogue();
        $keys = [];

        foreach ($this->colleges() as $college) {
            foreach ($catalogue->all($college) as $gift) {
                $keys[] = (string) ($gift['key'] ?? '');
            }
        }

        self::assertCount(26, $keys);
        self::assertCount(26, array_unique($keys));
    }

    /** @return array<int,string> */
    private function colleges(): array
    {
        return [
            'college-of-the-seasoned-song',
            'college-of-nostalgia',
            'college-of-preservation',
            'charcutaire',
            'college-of-culinary-crescendo',
            'college-of-confection',
            'college-of-churned-verse',
        ];
    }
}

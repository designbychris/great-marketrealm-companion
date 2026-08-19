<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Paladin;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\PaladinProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class PaladinSacredOathGiftsRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function oaths(): array
    {
        return [
            'oath-of-inventory',
            'oath-of-the-colonel',
            'oath-of-the-creamfather',
            'oath-of-aroma',
            'oath-of-clearance',
            'oath-of-seasoning',
            'oath-of-carbonation',
            'oath-of-the-cleaver-saint',
        ];
    }

    public function testAllEightOathsHaveFourAutomaticGifts(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->oaths() as $oath) {
            $gifts = $catalogue->all($oath);

            self::assertCount(4, $gifts);

            self::assertSame(
                [3, 7, 15, 20],
                array_column($gifts, 'level')
            );

            self::assertSame(
                [
                    'automatic',
                    'automatic',
                    'automatic',
                    'automatic',
                ],
                array_column($gifts, 'mode')
            );
        }
    }

    public function testEveryOathGiftHasPlayerFacingExplanation(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->oaths() as $oath) {
            foreach ($catalogue->all($oath) as $gift) {
                self::assertNotSame(
                    '',
                    trim((string) $gift['label'])
                );

                self::assertNotSame(
                    '',
                    trim((string) $gift['summary'])
                );

                self::assertNotSame(
                    '',
                    trim((string) $gift['detail'])
                );
            }
        }
    }

    public function testAllEightChoiceCardsExposeGuidanceAndGiftPreview(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('paladin')
        );

        self::assertCount(8, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame(
                '',
                trim((string) $candidate['identity'])
            );

            self::assertNotSame(
                '',
                trim((string) $candidate['playstyle'])
            );

            self::assertNotSame(
                '',
                trim((string) $candidate['best_for'])
            );

            self::assertCount(
                4,
                $candidate['gift_preview']
            );
        }
    }

    public function testEveryOathPreviewShowsFullSacredCadence(): void
    {
        $candidates = (
            new PathCandidateCatalogue()
        )->forClass(
            CharacterClass::fromString('paladin')
        );

        foreach ($candidates as $candidate) {
            self::assertSame(
                [3, 7, 15, 20],
                array_column(
                    $candidate['gift_preview'],
                    'level'
                )
            );
        }
    }

    public function testLevelThreeDelegatesOathAndFirstGift(): void
    {
        $entry = (new PaladinProgression())
            ->forLevel(
                CharacterClass::fromString('paladin'),
                3
            );

        self::assertSame(
            ['path', 'path-gifts'],
            array_column(
                $entry['delegated'],
                'folio'
            )
        );

        self::assertSame(
            [
                'sacred-oath',
                'sacred-oath-gift',
            ],
            array_column(
                $entry['delegated'],
                'key'
            )
        );
    }

    public function testLaterSacredOathGiftMilestonesRemainReserved(): void
    {
        $progression = new PaladinProgression();
        $paladin =
            CharacterClass::fromString('paladin');

        foreach ([7, 15, 20] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression
                        ->forLevel(
                            $paladin,
                            $level
                        )['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testUnlockedGiftCatalogueRespectsTargetLevel(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $known =
            PathGifts::none();

        self::assertCount(
            1,
            $catalogue->unlocked(
                'oath-of-inventory',
                3,
                $known
            )
        );

        self::assertCount(
            2,
            $catalogue->unlocked(
                'oath-of-inventory',
                7,
                $known
            )
        );

        self::assertCount(
            4,
            $catalogue->unlocked(
                'oath-of-inventory',
                20,
                $known
            )
        );
    }

    public function testAllEightOathsAreRegisteredBySharedGiftCatalogue(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        foreach ($this->oaths() as $oath) {
            self::assertTrue(
                $catalogue->supports($oath)
            );

            self::assertNotSame(
                '',
                $catalogue->pathLabel($oath)
            );
        }
    }

    public function testOathGiftKeysAreUniqueAcrossPaladinCatalogue(): void
    {
        $catalogue =
            new PathGiftCatalogue();

        $keys = [];

        foreach ($this->oaths() as $oath) {
            foreach ($catalogue->all($oath) as $gift) {
                $keys[] = (string) $gift['key'];
            }
        }

        self::assertCount(
            32,
            $keys
        );

        self::assertCount(
            32,
            array_unique($keys)
        );
    }

    public function testInventoryOathPreviewStartsWithSacredStocktake(): void
    {
        $candidate = array_values(
            array_filter(
                (
                    new PathCandidateCatalogue()
                )->forClass(
                    CharacterClass::fromString(
                        'paladin'
                    )
                ),
                static fn (
                    array $candidate
                ): bool =>
                    ($candidate['key'] ?? '')
                    === 'oath-of-inventory'
            )
        )[0];

        self::assertSame(
            'Sacred Stocktake',
            $candidate[
                'gift_preview'
            ][0]['label']
        );

        self::assertSame(
            3,
            $candidate[
                'gift_preview'
            ][0]['level']
        );
    }
}

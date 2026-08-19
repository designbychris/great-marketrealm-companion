<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Monk;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\PathGifts;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Definitions\Classes\MonkProgression;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class MonkWayGiftsRegressionTest extends TestCase
{
    /** @return array<int,string> */
    private function ways(): array
    {
        return [
            'way-of-the-spun-cloud',
            'way-of-the-neon-crunch',
            'way-of-the-vacuum-seal',
            'way-of-the-simmering-soul',
            'way-of-the-whirling-utensil',
            'way-of-the-spongecake-soul',
        ];
    }

    public function testAllSixWaysHaveFourAutomaticGifts(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->ways() as $way) {
            $gifts = $catalogue->all($way);

            self::assertCount(4, $gifts);
            self::assertSame(
                [3, 6, 11, 17],
                array_column($gifts, 'level')
            );
            self::assertSame(
                ['automatic', 'automatic', 'automatic', 'automatic'],
                array_column($gifts, 'mode')
            );
        }
    }

    public function testEveryGiftHasPlayerFacingExplanation(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->ways() as $way) {
            foreach ($catalogue->all($way) as $gift) {
                self::assertNotSame('', trim((string) $gift['label']));
                self::assertNotSame('', trim((string) $gift['summary']));
                self::assertNotSame('', trim((string) $gift['detail']));
            }
        }
    }

    public function testChoiceCardsExposeIdentityPlaystyleAndBestFor(): void
    {
        $candidates = (new PathCandidateCatalogue())
            ->forClass(CharacterClass::fromString('monk'));

        self::assertCount(6, $candidates);

        foreach ($candidates as $candidate) {
            self::assertNotSame('', trim((string) $candidate['identity']));
            self::assertNotSame('', trim((string) $candidate['playstyle']));
            self::assertNotSame('', trim((string) $candidate['best_for']));
            self::assertCount(4, $candidate['gift_preview']);
        }
    }

    public function testGiftPreviewShowsFullMonkCadence(): void
    {
        $candidates = (new PathCandidateCatalogue())
            ->forClass(CharacterClass::fromString('monk'));

        foreach ($candidates as $candidate) {
            self::assertSame(
                [3, 6, 11, 17],
                array_column($candidate['gift_preview'], 'level')
            );
        }
    }

    public function testLevelThreeDelegatesChoiceAndFirstGift(): void
    {
        $entry = (new MonkProgression())
            ->forLevel(CharacterClass::fromString('monk'), 3);

        self::assertSame(
            ['path', 'path-gifts'],
            array_column($entry['delegated'], 'folio')
        );
    }

    public function testLaterWayGiftMilestonesRemainReserved(): void
    {
        $progression = new MonkProgression();
        $monk = CharacterClass::fromString('monk');

        foreach ([6, 11, 17] as $level) {
            self::assertContains(
                'path-gifts',
                array_column(
                    $progression->forLevel($monk, $level)['delegated'],
                    'folio'
                )
            );
        }
    }

    public function testUnlockedGiftCatalogueRespectsTargetLevel(): void
    {
        $catalogue = new PathGiftCatalogue();
        $known = PathGifts::none();

        self::assertCount(
            1,
            $catalogue->unlocked(
                'way-of-the-spun-cloud',
                3,
                $known
            )
        );

        self::assertCount(
            2,
            $catalogue->unlocked(
                'way-of-the-spun-cloud',
                6,
                $known
            )
        );

        self::assertCount(
            4,
            $catalogue->unlocked(
                'way-of-the-spun-cloud',
                17,
                $known
            )
        );
    }

    public function testMonkWaysAreRegisteredBySharedGiftCatalogue(): void
    {
        $catalogue = new PathGiftCatalogue();

        foreach ($this->ways() as $way) {
            self::assertTrue($catalogue->supports($way));
            self::assertNotSame('', $catalogue->pathLabel($way));
        }
    }
}

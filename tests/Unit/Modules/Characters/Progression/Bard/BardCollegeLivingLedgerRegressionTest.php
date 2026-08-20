<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Bard;

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Experience;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Bard\Services\BardCollegeGiftLedgerPresenter;
use PHPUnit\Framework\TestCase;

final class BardCollegeLivingLedgerRegressionTest extends TestCase
{
    public function testForeignCallingIsNotSupported(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->character('wizard', 14, '')
        );

        self::assertFalse($ledger['supported']);
        self::assertSame([], $ledger['gifts']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testLevelTwoBardHasNoLiveCollegeGiftsYet(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                2,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame([], $ledger['gifts']);
        self::assertSame(3, $ledger['next_level']);
        self::assertSame(
            ['Spice Notes', 'Herbal Harmonization'],
            array_column(
                $ledger['next_gifts'],
                'label'
            )
        );
    }

    public function testLevelThreeShowsBothOpeningSeasonedSongGifts(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                3,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(2, $ledger['count']);
        self::assertSame(
            ['Spice Notes', 'Herbal Harmonization'],
            array_column(
                $ledger['gifts'],
                'label'
            )
        );
        self::assertSame(6, $ledger['next_level']);
    }

    public function testLevelFiveDoesNotRevealLevelSixGiftEarly(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                5,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(
            ['Spice Notes', 'Herbal Harmonization'],
            array_column(
                $ledger['gifts'],
                'label'
            )
        );
        self::assertSame(
            ['Choral Infusion'],
            array_column(
                $ledger['next_gifts'],
                'label'
            )
        );
    }

    public function testLevelSixAddsTheNextCollegeGift(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                6,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(3, $ledger['count']);
        self::assertSame(
            'Choral Infusion',
            $ledger['gifts'][2]['label']
        );
        self::assertSame(14, $ledger['next_level']);
    }

    public function testLevelThirteenKeepsCapstoneAsFutureOnly(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                13,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(3, $ledger['count']);
        self::assertSame(14, $ledger['next_level']);
        self::assertSame(
            ['Symphony of the Senses'],
            array_column(
                $ledger['next_gifts'],
                'label'
            )
        );
    }

    public function testLevelFourteenMakesSuppliedCapstoneLive(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                14,
                'college-of-the-seasoned-song'
            )
        );

        self::assertSame(4, $ledger['count']);
        self::assertTrue($ledger['complete']);
        self::assertNull($ledger['next_level']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testNostalgiaCompletesAtItsSuppliedLevelSixBoundary(): void
    {
        $ledger = (
            new BardCollegeGiftLedgerPresenter()
        )->present(
            $this->bard(
                14,
                'college-of-nostalgia'
            )
        );

        self::assertSame(3, $ledger['count']);
        self::assertTrue($ledger['complete']);
        self::assertNull($ledger['next_level']);
        self::assertSame(
            [
                'Jingle Strike',
                'Viral Catchphrase',
                'Forgotten Favorite',
            ],
            array_column(
                $ledger['gifts'],
                'label'
            )
        );
    }

    public function testControllerSuppliesCollegeGiftsToOpenLedger(): void
    {
        $controller = $this->source(
            'app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertStringContainsString(
            'BardCollegeGiftLedgerPresenter',
            $controller
        );

        self::assertStringContainsString(
            "'collegeGifts' => \$collegeGifts",
            $controller
        );
    }

    public function testOpenLedgerRendersLiveAndNextCollegeGifts(): void
    {
        $view = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-bard-college-gifts',
            $view
        );

        self::assertStringContainsString(
            'Living College Gifts',
            $view
        );

        self::assertStringContainsString(
            'Next College milestone',
            $view
        );

        self::assertStringContainsString(
            "['detail']",
            $view
        );
    }

    public function testCollegeGiftLedgerStylesRemainResponsive(): void
    {
        $css = $this->source(
            'assets/css/modules/characters/'
            . 'arcane-pantry.css'
        );

        self::assertStringContainsString(
            '.gmrc-college-gifts',
            $css
        );

        self::assertStringContainsString(
            '.gmrc-college-gifts__grid',
            $css
        );

        self::assertStringContainsString(
            '@media (max-width: 620px)',
            $css
        );

        self::assertStringContainsString(
            '@media (forced-colors: active)',
            $css
        );
    }

    private function bard(
        int $level,
        string $college
    ): Character {
        return $this->character(
            'bard',
            $level,
            $college
        );
    }

    private function character(
        string $class,
        int $level,
        string $college
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString(
                'College Gift Ledger Tester'
            ),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath:
                CallingPath::fromString(
                    $college
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

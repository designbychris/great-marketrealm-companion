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
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services\ArtificerSpecialisationGiftLedgerPresenter;
use PHPUnit\Framework\TestCase;

final class ArtificerSpecialisationLivingLedgerRegressionTest extends TestCase
{
    public function testForeignCallingIsUnsupported(): void
    {
        $ledger = (new ArtificerSpecialisationGiftLedgerPresenter())
            ->present($this->character('bard', 15, ''));

        self::assertFalse($ledger['supported']);
        self::assertSame([], $ledger['gifts']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testLevelTwoShowsNoLiveGiftsAndPointsToLevelThree(): void
    {
        $ledger = $this->present(2, 'the-spice-engineer');

        self::assertSame([], $ledger['gifts']);
        self::assertSame(3, $ledger['next_level']);
        self::assertSame(
            ['Spicecrafting', 'Infused Condiments'],
            array_column($ledger['next_gifts'], 'label')
        );
    }

    public function testLevelThreeShowsBothOpeningSpiceEngineerGifts(): void
    {
        $ledger = $this->present(3, 'the-spice-engineer');

        self::assertSame(2, $ledger['count']);
        self::assertSame(
            ['Spicecrafting', 'Infused Condiments'],
            array_column($ledger['gifts'], 'label')
        );
        self::assertSame(5, $ledger['next_level']);
    }

    public function testLevelFourDoesNotRevealLevelFiveGiftEarly(): void
    {
        $ledger = $this->present(4, 'the-spice-engineer');

        self::assertSame(2, $ledger['count']);
        self::assertSame(
            ['Flavour Cascade'],
            array_column($ledger['next_gifts'], 'label')
        );
    }

    public function testLevelFiveAddsFlavourCascade(): void
    {
        $ledger = $this->present(5, 'the-spice-engineer');

        self::assertSame(3, $ledger['count']);
        self::assertSame('Flavour Cascade', $ledger['gifts'][2]['label']);
        self::assertSame(9, $ledger['next_level']);
    }

    public function testLevelEightKeepsLevelNineGiftFutureOnly(): void
    {
        $ledger = $this->present(8, 'the-cheesemonger');

        self::assertSame(3, $ledger['count']);
        self::assertSame(9, $ledger['next_level']);
        self::assertSame(
            ['Cheese Overload'],
            array_column($ledger['next_gifts'], 'label')
        );
    }

    public function testLevelFourteenKeepsLevelFifteenCapstoneFutureOnly(): void
    {
        $ledger = $this->present(14, 'the-culinary-engineer');

        self::assertSame(4, $ledger['count']);
        self::assertSame(15, $ledger['next_level']);
        self::assertSame(
            ['Master of Magical Cuisine'],
            array_column($ledger['next_gifts'], 'label')
        );
    }

    public function testLevelFifteenMakesSuppliedCapstoneLive(): void
    {
        $ledger = $this->present(15, 'the-culinary-engineer');

        self::assertSame(5, $ledger['count']);
        self::assertTrue($ledger['complete']);
        self::assertNull($ledger['next_level']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testSousSorcererCompletesAtItsSuppliedLevelThreeBoundary(): void
    {
        $ledger = $this->present(15, 'the-sous-sorcerer');

        self::assertSame(2, $ledger['count']);
        self::assertSame(
            ['Sous-Sorcerer Core Features', 'Flavour Surge'],
            array_column($ledger['gifts'], 'label')
        );
        self::assertTrue($ledger['complete']);
        self::assertNull($ledger['next_level']);
        self::assertSame([], $ledger['next_gifts']);
    }

    public function testControllerSuppliesArtificerGiftsToLedger(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Controllers/CharacterController.php'
        );

        self::assertStringContainsString(
            'ArtificerSpecialisationGiftLedgerPresenter',
            $source
        );
        self::assertStringContainsString(
            "'artificerGifts' => \$artificerGifts",
            $source
        );
    }

    public function testLedgerRendersLiveAndNextSpecialisationGifts(): void
    {
        $source = $this->source(
            'app/Modules/Characters/Views/show.php'
        );

        self::assertStringContainsString(
            'data-artificer-specialisation-gifts',
            $source
        );
        self::assertStringContainsString(
            'Living Specialisation Gifts',
            $source
        );
        self::assertStringContainsString(
            'Next Specialisation milestone',
            $source
        );
        self::assertStringContainsString("['detail']", $source);
    }

    public function testSpecialisationGiftLedgerStylesRemainResponsive(): void
    {
        $source = $this->source(
            'assets/css/modules/characters/arcane-pantry.css'
        );

        self::assertStringContainsString('.gmrc-artificer-gifts', $source);
        self::assertStringContainsString('.gmrc-artificer-gifts__grid', $source);
        self::assertStringContainsString('@media (max-width: 620px)', $source);
        self::assertStringContainsString('@media (forced-colors: active)', $source);
    }

    private function present(int $level, string $specialisation): array
    {
        return (new ArtificerSpecialisationGiftLedgerPresenter())
            ->present($this->character('artificer', $level, $specialisation));
    }

    private function character(
        string $class,
        int $level,
        string $specialisation
    ): Character {
        return Character::reconstitute(
            CharacterId::generate(),
            CharacterName::fromString('Artificer Living Ledger Tester'),
            Race::fromString('fructan'),
            CharacterClass::fromString($class),
            Level::fromInt($level),
            Experience::zero(),
            HitPoints::full(20),
            AbilityScores::average(),
            callingPath: CallingPath::fromString($specialisation)
        );
    }

    private function source(string $relative): string
    {
        $source = file_get_contents($this->root() . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }

    private function root(): string
    {
        return dirname(__DIR__, 6);
    }
}

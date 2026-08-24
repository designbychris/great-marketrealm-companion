<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Honours;

use GreatMarketrealmCompanion\Modules\Honours\Services\CharacterHonourRegistry;
use PHPUnit\Framework\TestCase;

final class CharacterHonoursLedgerStampsRegressionTest extends TestCase
{
    public function testCanonicalCharacterHonourRegistryBeginsWithSixDistinctions(): void
    {
        $honours = (new CharacterHonourRegistry())->all();

        self::assertCount(6, $honours);
        self::assertSame(
            ['first-footfall', 'calling-answered', 'seasoned-adventurer', 'marketrealm-veteran', 'hero-of-the-shelves', 'legend-of-the-aisles'],
            array_column($honours, 'key')
        );
    }

    public function testCharacterHonoursUseCertifiedCharacterFactsOnly(): void
    {
        $source = $this->source('app/Modules/Honours/Services/CharacterBookOfDeeds.php');

        self::assertStringContainsString("'level' => \$character->level()->value()", $source);
        self::assertStringContainsString("'calling_path' => \$character->callingPath()->isChosen() ? 1 : 0", $source);
        self::assertStringNotContainsString('$_POST', $source);
    }

    public function testCharacterHonourLedgerIsAppendOnlyPerStableCharacterId(): void
    {
        $source = $this->source('app/Modules/Honours/Services/CharacterHonourLedger.php');

        self::assertStringContainsString("private const META_KEY = '_gmrc_character_honours'", $source);
        self::assertStringContainsString('$stored[$characterId->value()] = $honours;', $source);
        self::assertStringContainsString('isset($honours[$key])', $source);
        self::assertStringNotContainsString('delete_user_meta', $source);
    }

    public function testCharacterBookCertifiesMilestonesRatherThanRecomputingTransientBadges(): void
    {
        $source = $this->source('app/Modules/Honours/Services/CharacterBookOfDeeds.php');

        self::assertStringContainsString('$this->ledger->certify(', $source);
        self::assertStringContainsString("'certified_at' => \$awarded[\$key] ?? ''", $source);
    }

    public function testHonoursProviderRegistersCharacterDistinctionServices(): void
    {
        $provider = $this->source('app/Modules/Honours/HonoursServiceProvider.php');

        self::assertStringContainsString('CharacterHonourRegistry::class', $provider);
        self::assertStringContainsString('CharacterHonourLedger::class', $provider);
        self::assertStringContainsString('CharacterBookOfDeeds::class', $provider);
    }

    public function testOwnerCharacterLedgerReceivesCharacterHonoursProjection(): void
    {
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');

        self::assertStringContainsString('private ?CharacterBookOfDeeds $characterHonours = null', $controller);
        self::assertStringContainsString("'characterHonours' => \$characterHonours", $controller);
        self::assertStringContainsString('$includeFellowships', $controller);
    }

    public function testDungeonMasterReadOnlyLedgerDoesNotCertifyOwnerCharacterHonours(): void
    {
        $controller = $this->source('app/Modules/Characters/Controllers/CharacterController.php');

        self::assertStringContainsString("'dungeonmaster.characters.show',\n            false", $controller);
        self::assertStringContainsString('&& $this->characterHonours instanceof CharacterBookOfDeeds', $controller);
    }

    public function testCharacterLedgerRendersEarnedAndUnwitnessedWaxStamps(): void
    {
        $view = $this->source('app/Modules/Characters/Views/show.php');

        self::assertStringContainsString('Character Honours', $view);
        self::assertStringContainsString('Certified distinction', $view);
        self::assertStringContainsString('Deed yet to be witnessed', $view);
        self::assertStringContainsString('Stamped in the Ledger:', $view);
    }

    public function testCharacterHonoursKeepGuildBookOfDeedsAsSeparateAccountArchive(): void
    {
        $view = $this->source('app/Modules/Characters/Views/show.php');

        self::assertStringContainsString('Guild-wide Book of Deeds', $view);
        self::assertStringContainsString('Account-level Guild Honours are recorded separately', $view);
        self::assertStringContainsString("'guild-honours'", $view);
    }

    public function testWaxStampPresentationIncludesResponsiveAndAccessibilityFallbacks(): void
    {
        $css = $this->source('assets/css/modules/characters/open-ledger.css');

        self::assertStringContainsString('.gmrc-character-honour__seal', $css);
        self::assertStringContainsString('@media (max-width: 760px)', $css);
        self::assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $relativePath);
        self::assertIsString($source);

        return $source;
    }
}
